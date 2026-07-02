<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengumuman;
use App\Models\PushSubscription;
use App\Services\WebPushSender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PengumumanController extends Controller
{
    public function index()
    {
        $pengumumans = Pengumuman::orderBy('urutan')->orderBy('created_at', 'desc')->get();
        $jenisOptions = Pengumuman::jenisOptions();
        return view('admin.pengumuman', compact('pengumumans', 'jenisOptions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'jenis' => 'required|string',
            'isi' => 'required|string',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'waktu' => 'nullable|date_format:H:i',
            'gambar' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,svg,bmp|max:5120',
        ]);

        $data = $request->only(['judul', 'jenis', 'isi', 'tanggal_mulai', 'tanggal_selesai', 'waktu']);
        $data['is_active'] = true;
        $data['sembunyikan_detail'] = $request->boolean('sembunyikan_detail');
        // Geser semua urutan ke bawah, taruh yang baru di paling atas
        Pengumuman::query()->increment('urutan');
        $data['urutan'] = 0;

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('pengumuman', 'public');
        }

        $pengumuman = Pengumuman::create($data);

        if ($request->boolean('kirim_push')) {
            $this->pushPengumuman($pengumuman);
        }

        return redirect()->route('admin.pengumuman.index')->with('success', 'Pengumuman berhasil ditambahkan');
    }

    public function show($id)
    {
        return response()->json(Pengumuman::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'jenis' => 'required|string',
            'isi' => 'required|string',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'waktu' => 'nullable|date_format:H:i',
            'gambar' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,svg,bmp|max:5120',
        ]);

        $pengumuman = Pengumuman::findOrFail($id);
        $data = $request->only(['judul', 'jenis', 'isi', 'tanggal_mulai', 'tanggal_selesai', 'waktu']);
        $data['sembunyikan_detail'] = $request->boolean('sembunyikan_detail');

        if ($request->hasFile('gambar')) {
            if ($pengumuman->gambar && Storage::disk('public')->exists($pengumuman->gambar)) {
                Storage::disk('public')->delete($pengumuman->gambar);
            }
            $data['gambar'] = $request->file('gambar')->store('pengumuman', 'public');
        }

        if ($request->has('hapus_gambar') && !$request->hasFile('gambar')) {
            if ($pengumuman->gambar && Storage::disk('public')->exists($pengumuman->gambar)) {
                Storage::disk('public')->delete($pengumuman->gambar);
            }
            $data['gambar'] = null;
        }

        $pengumuman->update($data);

        return redirect()->route('admin.pengumuman.index')->with('success', 'Pengumuman berhasil diperbarui');
    }

    public function destroy($id)
    {
        $pengumuman = Pengumuman::findOrFail($id);
        if ($pengumuman->gambar && Storage::disk('public')->exists($pengumuman->gambar)) {
            Storage::disk('public')->delete($pengumuman->gambar);
        }
        $pengumuman->delete();

        return redirect()->route('admin.pengumuman.index')->with('success', 'Pengumuman berhasil dihapus');
    }

    public function sendPush(int $id)
    {
        $pengumuman = Pengumuman::findOrFail($id);
        [$sent, $failed] = $this->pushPengumuman($pengumuman);
        return response()->json(['success' => true, 'sent' => $sent, 'failed' => $failed]);
    }

    public function broadcastCustom(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:100',
            'body'  => 'required|string|max:200',
            'url'   => 'nullable|string|max:255',
        ]);

        $sender = new WebPushSender();
        $sent = $failed = 0;
        foreach (PushSubscription::all() as $sub) {
            $code = $sender->send($sub->endpoint, $sub->public_key, $sub->auth_token, [
                'title' => $request->title,
                'body'  => $request->body,
                'tag'   => 'custom-' . time(),
                'url'   => $request->filled('url') ? $request->url : '/pegawai/dashboard',
            ]);
            if ($code >= 200 && $code < 300) {
                $sent++;
            } elseif (in_array($code, [404, 410])) {
                $sub->delete();
            } else {
                $failed++;
            }
        }

        return response()->json(['success' => true, 'sent' => $sent, 'failed' => $failed]);
    }

    private function pushPengumuman(Pengumuman $pengumuman): array
    {
        $sender = new WebPushSender();
        $sent = $failed = 0;
        foreach (PushSubscription::all() as $sub) {
            $code = $sender->send($sub->endpoint, $sub->public_key, $sub->auth_token, [
                'title' => '📢 ' . $pengumuman->judul,
                'body'  => Str::limit(strip_tags($pengumuman->isi), 100),
                'tag'   => 'pengumuman-' . $pengumuman->id,
                'url'   => '/pegawai/dashboard',
            ]);
            if ($code >= 200 && $code < 300) {
                $sent++;
            } elseif (in_array($code, [404, 410])) {
                $sub->delete();
            } else {
                $failed++;
            }
        }
        return [$sent, $failed];
    }

    public function toggle($id)
    {
        $pengumuman = Pengumuman::findOrFail($id);
        $pengumuman->update(['is_active' => !$pengumuman->is_active]);
        return response()->json(['success' => true, 'is_active' => $pengumuman->is_active]);
    }

    public function uploadImage(Request $request)
    {
        $request->validate(['image' => 'required|file|mimes:jpg,jpeg,png,gif,webp,svg,bmp|max:5120']);
        $path = $request->file('image')->store('pengumuman/content', 'public');
        return response()->json(['url' => asset('public/storage/' . $path)]);
    }

    public function reorder(Request $request)
    {
        $ids = $request->input('ids', []);
        foreach ($ids as $i => $id) {
            Pengumuman::where('id', $id)->update(['urutan' => $i]);
        }
        return response()->json(['success' => true]);
    }
}
