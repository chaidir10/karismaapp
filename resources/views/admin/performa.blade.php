@extends('layouts.admin')

@section('title', 'Performa Pegawai')

@push('styles')
<style>
    .perf-bar {
        height: 6px;
        border-radius: 3px;
        background: #e5e7eb;
        overflow: hidden;
    }
    .perf-bar-fill {
        height: 100%;
        border-radius: 3px;
        transition: width 0.6s ease;
    }
    .rank-badge {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 12px;
        color: #fff;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-amber-500 to-orange-600 rounded-xl p-6 mb-8 shadow-lg">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white">Performa Pegawai</h1>
                <p class="text-amber-100 mt-1">Penilaian kedisiplinan berdasarkan ketepatan waktu masuk & pulang</p>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="bg-white rounded-xl p-6 shadow-md mb-6">
        <h2 class="text-lg font-semibold mb-4 text-gray-800">Pilih Periode</h2>
        <form id="formFilter" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
            <div class="md:col-span-3">
                <label class="text-sm font-medium block mb-2 text-gray-700">Bulan</label>
                <select id="filterBulan" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:border-amber-500 text-sm">
                    @php $namaBulan = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember']; @endphp
                    @foreach($namaBulan as $i => $nb)
                        <option value="{{ $i+1 }}" {{ (int)now()->format('m') === $i+1 ? 'selected' : '' }}>{{ $nb }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="text-sm font-medium block mb-2 text-gray-700">Tahun</label>
                <select id="filterTahun" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:border-amber-500 text-sm">
                    @for($y = now()->year; $y >= 2024; $y--)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="md:col-span-7 flex gap-2">
                <button type="submit" class="btn-primary" style="flex:1">
                    <i class="fas fa-search mr-2"></i>
                    Tampilkan Performa
                </button>
                <a href="#" id="btnPdf" class="btn-danger">
                    <i class="fas fa-file-pdf mr-2"></i>
                    Download PDF
                </a>
            </div>
        </form>
    </div>

    <!-- Info Metodologi -->
    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6 text-sm text-amber-800">
        <div class="flex items-start gap-2">
            <i class="fas fa-info-circle mt-0.5"></i>
            <div>
                <strong>Sistem Penilaian (4 Komponen):</strong>
                Kehadiran (25%) + Kedisiplinan Masuk (30%) + Kedisiplinan Pulang (20%) + Jam Kerja Terpenuhi (25%) = Total Performa.
                Jika skor sama, peringkat ditentukan oleh total durasi kerja terbanyak.
                Dihitung dari hari kerja efektif (Senin-Jumat, non-libur nasional). Lembur tidak termasuk penilaian.
            </div>
        </div>
    </div>

    <!-- Ringkasan -->
    <div id="summaryCards" class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6 hidden">
        <div class="bg-white rounded-xl p-4 shadow-md text-center">
            <div class="text-2xl font-bold text-gray-800" id="sumHariKerja">-</div>
            <div class="text-xs text-gray-500 mt-1">Hari Kerja Efektif</div>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-md text-center">
            <div class="text-2xl font-bold text-green-600" id="sumTotalPegawai">-</div>
            <div class="text-xs text-gray-500 mt-1">Total Pegawai</div>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-md text-center">
            <div class="text-2xl font-bold text-amber-600" id="sumRataPerforma">-</div>
            <div class="text-xs text-gray-500 mt-1">Rata-rata Performa</div>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-md text-center">
            <div class="text-2xl font-bold text-blue-600" id="sumPerfect">-</div>
            <div class="text-xs text-gray-500 mt-1">Skor Sempurna (100%)</div>
        </div>
    </div>

    <!-- Tabel Performa -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-3 text-center text-xs font-semibold text-gray-700 uppercase w-12">#</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Pegawai</th>
                        <th class="px-3 py-3 text-center text-xs font-semibold text-gray-700 uppercase">Hadir</th>
                        <th class="px-3 py-3 text-center text-xs font-semibold text-gray-700 uppercase">Tepat Masuk</th>
                        <th class="px-3 py-3 text-center text-xs font-semibold text-gray-700 uppercase">Telat</th>
                        <th class="px-3 py-3 text-center text-xs font-semibold text-gray-700 uppercase">Pulang Tepat</th>
                        <th class="px-3 py-3 text-center text-xs font-semibold text-gray-700 uppercase">Pulang Cepat</th>
                        <th class="px-3 py-3 text-center text-xs font-semibold text-gray-700 uppercase">Jam Kerja Cukup</th>
                        <th class="px-3 py-3 text-center text-xs font-semibold text-gray-700 uppercase">Total Durasi</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 uppercase" style="min-width:180px">Performa</th>
                    </tr>
                </thead>
                <tbody id="performaBody" class="bg-white divide-y divide-gray-200 text-sm">
                    <tr>
                        <td colspan="10" class="px-4 py-8 text-center text-gray-500">
                            <i class="fas fa-trophy text-4xl text-gray-300 mb-3 block"></i>
                            <p class="text-sm font-medium">Pilih bulan dan tahun, lalu klik "Tampilkan Performa"</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="notification" class="fixed top-5 right-5 px-5 py-3 rounded-xl text-white z-50 opacity-0 transform -translate-y-5 transition-all duration-300 shadow-lg" style="display:none"></div>
@endsection

@push('scripts')
<script>
    function showNotification(msg, type) {
        var el = document.getElementById('notification');
        el.textContent = msg;
        el.style.display = 'block';
        el.style.background = type === 'success' ? 'linear-gradient(to right,#4ade80,#22c55e)' : 'linear-gradient(to right,#f87171,#ef4444)';
        setTimeout(function() { el.style.opacity = '1'; el.style.transform = 'translateY(0)'; }, 10);
        setTimeout(function() { el.style.opacity = '0'; el.style.transform = 'translateY(-20px)'; }, 3000);
        setTimeout(function() { el.style.display = 'none'; }, 3300);
    }

    function fmtDurasi(menit) {
        var j = Math.floor(menit / 60);
        var m = menit % 60;
        return j + 'j ' + m + 'm';
    }

    function perfColor(val) {
        if (val >= 90) return '#16a34a';
        if (val >= 75) return '#2563eb';
        if (val >= 50) return '#d97706';
        return '#dc2626';
    }

    function perfLabel(val) {
        if (val >= 90) return 'Sangat Baik';
        if (val >= 75) return 'Baik';
        if (val >= 50) return 'Cukup';
        return 'Kurang';
    }

    function rankBg(i) {
        if (i === 0) return 'background:linear-gradient(135deg,#f59e0b,#d97706)';
        if (i === 1) return 'background:linear-gradient(135deg,#9ca3af,#6b7280)';
        if (i === 2) return 'background:linear-gradient(135deg,#d97706,#b45309)';
        return 'background:#e5e7eb;color:#6b7280';
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('formFilter').addEventListener('submit', function(e) {
            e.preventDefault();
            loadPerforma();
        });

        document.getElementById('btnPdf').addEventListener('click', function(e) {
            e.preventDefault();
            var bulan = document.getElementById('filterBulan').value;
            var tahun = document.getElementById('filterTahun').value;
            window.open("{{ route('admin.performa.pdf') }}?bulan=" + bulan + "&tahun=" + tahun, '_blank');
            showNotification('Membuat PDF...', 'success');
        });

        loadPerforma();
    });

    function loadPerforma() {
        var bulan = document.getElementById('filterBulan').value;
        var tahun = document.getElementById('filterTahun').value;
        var tbody = document.getElementById('performaBody');

        tbody.innerHTML = '<tr><td colspan="10" class="px-4 py-8 text-center text-gray-500"><div class="inline-block w-5 h-5 border-3 border-amber-200 border-t-amber-600 rounded-full animate-spin mb-2"></div><p class="text-sm">Memuat data performa...</p></td></tr>';

        fetch("{{ route('admin.performa.data') }}?bulan=" + bulan + "&tahun=" + tahun)
            .then(function(r) { return r.json(); })
            .then(function(data) {
                var list = data.performa;
                var hk = data.hari_kerja;

                if (!list || list.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="10" class="px-4 py-8 text-center text-gray-500"><i class="fas fa-calendar-times text-4xl text-gray-300 mb-3 block"></i><p class="text-sm font-medium">Tidak ada data untuk periode ini</p></td></tr>';
                    document.getElementById('summaryCards').classList.add('hidden');
                    return;
                }

                // Summary
                document.getElementById('summaryCards').classList.remove('hidden');
                document.getElementById('sumHariKerja').textContent = hk;
                document.getElementById('sumTotalPegawai').textContent = list.length;
                var avg = list.reduce(function(s, x) { return s + x.performa; }, 0) / list.length;
                document.getElementById('sumRataPerforma').textContent = avg.toFixed(1) + '%';
                var perfect = list.filter(function(x) { return x.performa >= 100; }).length;
                document.getElementById('sumPerfect').textContent = perfect;

                tbody.innerHTML = '';
                list.forEach(function(item, idx) {
                    var color = perfColor(item.performa);
                    var label = perfLabel(item.performa);

                    tbody.innerHTML += '<tr class="hover:bg-gray-50 transition-colors">' +
                        '<td class="px-3 py-3 text-center"><span class="rank-badge" style="' + rankBg(idx) + '">' + (idx + 1) + '</span></td>' +
                        '<td class="px-4 py-3"><div class="font-medium text-gray-900">' + item.nama + '</div><div class="text-xs text-gray-500">' + (item.nip || '-') + (item.jabatan ? ' &middot; ' + item.jabatan : '') + '</div></td>' +
                        '<td class="px-3 py-3 text-center"><span class="font-semibold">' + item.hadir + '</span><span class="text-gray-400">/' + hk + '</span></td>' +
                        '<td class="px-3 py-3 text-center text-green-600 font-semibold">' + item.tepat_masuk + '</td>' +
                        '<td class="px-3 py-3 text-center text-red-600 font-semibold">' + item.telat + '</td>' +
                        '<td class="px-3 py-3 text-center text-green-600 font-semibold">' + item.pulang_tepat + '</td>' +
                        '<td class="px-3 py-3 text-center text-orange-600 font-semibold">' + item.pulang_cepat + '</td>' +
                        '<td class="px-3 py-3 text-center"><span class="font-semibold text-blue-600">' + item.jam_kerja_cukup + '</span><span class="text-gray-400">/' + item.hadir + '</span></td>' +
                        '<td class="px-3 py-3 text-center"><div class="font-semibold text-gray-800">' + fmtDurasi(item.total_menit_kerja) + '</div><div class="text-xs text-gray-400">std ' + fmtDurasi(item.total_menit_standar) + '</div></td>' +
                        '<td class="px-4 py-3"><div class="flex items-center gap-3"><div class="flex-1"><div class="perf-bar"><div class="perf-bar-fill" style="width:' + Math.min(item.performa, 100) + '%;background:' + color + '"></div></div></div><div class="text-right" style="min-width:70px"><span class="font-bold" style="color:' + color + '">' + item.performa.toFixed(1) + '%</span><div class="text-xs text-gray-400">' + label + '</div></div></div></td>' +
                        '</tr>';
                });

                showNotification('Data performa berhasil dimuat', 'success');
            })
            .catch(function() {
                tbody.innerHTML = '<tr><td colspan="10" class="px-4 py-8 text-center text-red-500"><i class="fas fa-exclamation-triangle text-4xl text-red-300 mb-3 block"></i><p class="text-sm font-medium">Gagal memuat data</p></td></tr>';
                showNotification('Gagal memuat data performa', 'error');
            });
    }
</script>
@endpush
