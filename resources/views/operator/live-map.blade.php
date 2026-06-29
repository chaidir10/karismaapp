@extends('layouts.operator')
@section('title', 'Live Tracking')

@section('content')
<div class="page-header-glass" style="margin-bottom:16px;">
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px;">
        <div>
            <h1><i class="fas fa-satellite-dish" style="margin-right:8px;"></i> Live Tracking Lokasi</h1>
            <p>Posisi terakhir semua pegawai yang aktif</p>
        </div>
        <div style="display:flex; gap:8px; align-items:center;">
            <span id="lastUpdate" style="font-size:11px; color:var(--dm-muted,#64748b);"></span>
            <span id="onlineCount" class="badge badge-success" style="font-size:11px;"><i class="fas fa-circle" style="font-size:6px;"></i> 0 user</span>
            <button onclick="refreshMap()" class="btn-primary" style="padding:8px 14px; font-size:12px;"><i class="fas fa-rotate"></i> Refresh</button>
        </div>
    </div>
</div>

<div style="display:grid; grid-template-columns:1fr 300px; gap:14px;">
    <div style="background:var(--dm-card,#fff); border:1px solid var(--dm-border,#e2e8f0); border-radius:14px; overflow:hidden;">
        <div id="liveMap" style="height:calc(100vh - 250px); min-height:400px;"></div>
    </div>

    <div style="display:flex; flex-direction:column; gap:14px;">
        <div style="background:var(--dm-card,#fff); border:1px solid var(--dm-border,#e2e8f0); border-radius:14px; overflow:hidden;">
            <div style="padding:12px 14px; border-bottom:1px solid var(--dm-border,#e2e8f0); display:flex; align-items:center; justify-content:space-between;">
                <div style="font-size:13px; font-weight:700; color:var(--dm-text,#1e293b);"><i class="fas fa-users" style="margin-right:6px; color:#2E97D4;"></i> Daftar User</div>
                <input type="text" id="userSearch" placeholder="Cari..." style="width:100px; padding:4px 8px; border:1px solid var(--dm-border,#d1d5db); border-radius:6px; font-size:11px; background:var(--dm-input,#fff); color:var(--dm-text); outline:none;">
            </div>
            <div id="userList" style="max-height:calc(100vh - 340px); overflow-y:auto; padding:6px;"></div>
        </div>

        <div style="background:var(--dm-card,#fff); border:1px solid var(--dm-border,#e2e8f0); border-radius:14px; padding:14px;">
            <div style="font-size:11px; font-weight:600; color:var(--dm-muted,#64748b); margin-bottom:8px;">LEGENDA</div>
            <div style="display:flex; flex-direction:column; gap:6px; font-size:12px; color:var(--dm-text,#1e293b);">
                <div style="display:flex; align-items:center; gap:8px;"><span style="width:12px; height:12px; border-radius:50%; background:#10b981; flex-shrink:0;"></span> Online (&lt; 10 menit)</div>
                <div style="display:flex; align-items:center; gap:8px;"><span style="width:12px; height:12px; border-radius:50%; background:#f59e0b; flex-shrink:0;"></span> Idle (10–30 menit)</div>
                <div style="display:flex; align-items:center; gap:8px;"><span style="width:12px; height:12px; border-radius:50%; background:#94a3b8; flex-shrink:0;"></span> Offline (&gt; 30 menit)</div>
            </div>
        </div>
    </div>
</div>

<style>
    @media (max-width:900px) {
        div[style*="grid-template-columns:1fr 300px"] { grid-template-columns:1fr !important; }
        #liveMap { height:50vh !important; min-height:300px !important; }
    }
    .user-item {
        display:flex; align-items:center; gap:8px; padding:8px 10px; border-radius:8px;
        cursor:pointer; transition:background 0.15s; font-size:12px;
    }
    .user-item:hover { background:var(--dm-bg,#f1f5f9); }
    .user-dot { width:8px; height:8px; border-radius:50%; flex-shrink:0; }
    .user-item-name { font-weight:600; color:var(--dm-text,#1e293b); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .user-item-time { font-size:10px; color:var(--dm-muted,#94a3b8); }
</style>

@push('scripts')
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
var map = L.map('liveMap').setView([3.3, 117.6], 13);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap',
    maxZoom: 19
}).addTo(map);

var markers = {};
var allLocations = [];

function getColor(minutesAgo) {
    if (minutesAgo < 10) return '#10b981';
    if (minutesAgo < 30) return '#f59e0b';
    return '#94a3b8';
}

function createIcon(color) {
    return L.divIcon({
        className: '',
        html: '<div style="width:28px;height:28px;border-radius:50%;background:'+color+';border:3px solid #fff;box-shadow:0 2px 8px rgba(0,0,0,0.3);display:flex;align-items:center;justify-content:center;"><i class="fas fa-user" style="color:#fff;font-size:10px;"></i></div>',
        iconSize: [28, 28],
        iconAnchor: [14, 14],
        popupAnchor: [0, -16]
    });
}

function refreshMap() {
    fetch('{{ route("operator.tracking.locations-json") }}')
        .then(function(r) { return r.json(); })
        .then(function(data) {
            allLocations = data;
            var onlineCount = 0;
            var bounds = [];

            Object.keys(markers).forEach(function(k) { map.removeLayer(markers[k]); });
            markers = {};

            data.forEach(function(loc) {
                var color = getColor(loc.minutes_ago);
                if (loc.minutes_ago < 10) onlineCount++;

                var marker = L.marker([loc.lat, loc.lng], { icon: createIcon(color) }).addTo(map);
                marker.bindPopup(
                    '<div style="min-width:180px;font-family:Poppins,sans-serif;">' +
                    '<div style="font-size:14px;font-weight:700;margin-bottom:4px;">' + loc.name + '</div>' +
                    '<div style="font-size:11px;color:#64748b;margin-bottom:2px;">NIP: ' + loc.nip + '</div>' +
                    '<div style="font-size:11px;color:#64748b;margin-bottom:6px;">' + loc.jabatan + '</div>' +
                    '<div style="font-size:11px;padding:4px 8px;border-radius:6px;background:' + color + '15;color:' + color + ';font-weight:600;display:inline-block;">' +
                    (loc.minutes_ago < 1 ? 'Baru saja' : loc.minutes_ago + ' menit lalu') + '</div>' +
                    '<div style="font-size:10px;color:#94a3b8;margin-top:4px;">Akurasi: ' + (loc.accuracy ? Math.round(loc.accuracy) + 'm' : '-') + '</div>' +
                    '<div style="font-size:10px;color:#94a3b8;">' + loc.updated_at + '</div>' +
                    '</div>'
                );
                markers[loc.user_id] = marker;
                bounds.push([loc.lat, loc.lng]);
            });

            if (bounds.length > 0) {
                map.fitBounds(bounds, { padding: [40, 40], maxZoom: 15 });
            }

            document.getElementById('onlineCount').innerHTML = '<i class="fas fa-circle" style="font-size:6px;"></i> ' + onlineCount + ' online';
            document.getElementById('lastUpdate').textContent = 'Update: ' + new Date().toLocaleTimeString('id-ID');
            renderUserList(data);
        });
}

function renderUserList(data) {
    var search = (document.getElementById('userSearch').value || '').toLowerCase();
    var html = '';

    data.sort(function(a, b) { return a.minutes_ago - b.minutes_ago; });

    data.forEach(function(loc) {
        if (search && loc.name.toLowerCase().indexOf(search) === -1) return;
        var color = getColor(loc.minutes_ago);
        var timeText = loc.minutes_ago < 1 ? 'Baru saja' : loc.minutes_ago + 'm lalu';
        html += '<div class="user-item" onclick="focusUser(' + loc.user_id + ')">' +
            '<span class="user-dot" style="background:' + color + ';"></span>' +
            '<div style="flex:1;min-width:0;"><div class="user-item-name">' + loc.name + '</div>' +
            '<div class="user-item-time">' + timeText + '</div></div></div>';
    });

    if (!html) html = '<div style="padding:20px;text-align:center;font-size:12px;color:var(--dm-muted,#94a3b8);">Belum ada data lokasi</div>';
    document.getElementById('userList').innerHTML = html;
}

function focusUser(userId) {
    var m = markers[userId];
    if (m) {
        map.setView(m.getLatLng(), 17, { animate: true });
        m.openPopup();
    }
}

document.getElementById('userSearch').addEventListener('input', function() {
    renderUserList(allLocations);
});

refreshMap();
setInterval(refreshMap, 30000);
</script>
@endpush
@endsection
