<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Absen Darurat | KARISMA</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Segoe UI',sans-serif; background:#000; color:#fff; height:100vh; display:flex; flex-direction:column; overflow:hidden; }
        .camera-area { flex:1; position:relative; overflow:hidden; background:#111; }
        #video { width:100%; height:100%; object-fit:cover; display:block; transform:scaleX(-1); }
        .overlay-top { position:absolute; top:0; left:0; right:0; padding:12px 16px; background:linear-gradient(to bottom,rgba(0,0,0,0.6),transparent); z-index:2; display:flex; justify-content:space-between; align-items:center; }
        .user-info { font-size:13px; font-weight:600; }
        .user-info small { display:block; font-size:10px; opacity:0.7; font-weight:400; }
        .badge-darurat { background:#ef4444; color:#fff; padding:3px 10px; border-radius:20px; font-size:10px; font-weight:700; }
        .bottom-panel { background:#111; padding:12px 16px 20px; z-index:2; }
        .map-strip { height:80px; border-radius:12px; overflow:hidden; margin-bottom:10px; border:1px solid #333; }
        #miniMap { width:100%; height:100%; }
        .location-info { font-size:11px; color:#94a3b8; margin-bottom:10px; display:flex; align-items:center; gap:6px; }
        .location-info i { color:#10b981; }
        .location-info.outside i { color:#ef4444; }
        .btn-row { display:flex; gap:10px; }
        .btn-absen { flex:1; height:52px; border:none; border-radius:14px; font-size:15px; font-weight:700; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:8px; -webkit-tap-highlight-color:transparent; }
        .btn-absen:active { opacity:0.85; transform:scale(0.97); }
        .btn-absen:disabled { opacity:0.4; cursor:not-allowed; }
        .btn-masuk { background:linear-gradient(135deg,#5AB6EA,#2E97D4); color:#fff; }
        .btn-pulang { background:linear-gradient(135deg,#f59e0b,#d97706); color:#fff; }
        .shift-row { display:flex; gap:8px; margin-bottom:10px; }
        .shift-btn { flex:1; padding:8px; border:1px solid #333; border-radius:10px; background:#1a1a2e; color:#94a3b8; font-size:11px; font-weight:600; cursor:pointer; text-align:center; }
        .shift-btn.active { border-color:#5AB6EA; color:#5AB6EA; background:rgba(90,182,234,0.1); }
        .toast { position:fixed; top:20px; left:50%; transform:translateX(-50%); padding:12px 20px; border-radius:12px; font-size:13px; font-weight:600; z-index:100; display:none; }
        .toast-success { background:#10b981; color:#fff; }
        .toast-error { background:#ef4444; color:#fff; }
        .guide-oval { position:absolute; top:50%; left:50%; transform:translate(-50%,-55%); width:200px; height:260px; border:2px solid rgba(255,255,255,0.3); border-radius:50%; z-index:1; pointer-events:none; }
        canvas#captureCanvas { display:none; }
    </style>
</head>
<body>
    <div class="camera-area">
        <video id="video" autoplay playsinline muted></video>
        <div class="guide-oval"></div>
        <div class="overlay-top">
            <div class="user-info">{{ $user->name }}<small>{{ $user->nip }}</small></div>
            <span class="badge-darurat"><i class="fas fa-bolt"></i> DARURAT</span>
        </div>
    </div>

    <div class="bottom-panel">
        <div class="map-strip"><div id="miniMap"></div></div>
        <div class="location-info" id="locationInfo"><i class="fas fa-spinner fa-spin"></i> Mendeteksi lokasi...</div>

        @if($shifts->count() > 0)
        <div class="shift-row">
            <div class="shift-btn active" data-shift="" onclick="pickShift(this,'')">Normal</div>
            @foreach($shifts as $s)
            <div class="shift-btn" data-shift="{{ $s->id }}" onclick="pickShift(this,'{{ $s->id }}')">{{ $s->nama }}</div>
            @endforeach
        </div>
        @endif

        <div class="btn-row">
            <button class="btn-absen btn-masuk" id="btnMasuk" onclick="doAbsen('masuk')"><i class="fas fa-arrow-right-to-bracket"></i> Masuk</button>
            <button class="btn-absen btn-pulang" id="btnPulang" onclick="doAbsen('pulang')"><i class="fas fa-arrow-right-from-bracket"></i> Pulang</button>
        </div>
    </div>

    <canvas id="captureCanvas"></canvas>
    <div class="toast" id="toast"></div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        var currentLokasi = '';
        var selectedShift = '';
        var processing = false;

        // Kamera
        (function() {
            var video = document.getElementById('video');
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) return;
            navigator.mediaDevices.getUserMedia({ video: { facingMode:'user', width:{ideal:480}, height:{ideal:360} }, audio:false })
                .then(function(stream) { video.srcObject = stream; video.play(); })
                .catch(function() { showToast('Kamera tidak dapat diakses', 'error'); });
        })();

        // Lokasi + Map
        var wilayahList = @json($wilayahJson);
        var miniMap = L.map('miniMap', { zoomControl:false, attributionControl:false, dragging:false, scrollWheelZoom:false });
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom:19 }).addTo(miniMap);

        function haversine(lat1,lng1,lat2,lng2) {
            var R=6371000, dLat=(lat2-lat1)*Math.PI/180, dLng=(lng2-lng1)*Math.PI/180;
            var a = Math.sin(dLat/2)*Math.sin(dLat/2) + Math.cos(lat1*Math.PI/180)*Math.cos(lat2*Math.PI/180)*Math.sin(dLng/2)*Math.sin(dLng/2);
            return R*2*Math.atan2(Math.sqrt(a),Math.sqrt(1-a));
        }

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(pos) {
                var lat = pos.coords.latitude, lng = pos.coords.longitude;
                currentLokasi = lat + ',' + lng;
                miniMap.setView([lat,lng], 16);
                L.marker([lat,lng]).addTo(miniMap);

                var inRadius = false;
                for (var i = 0; i < wilayahList.length; i++) {
                    if (haversine(lat,lng,wilayahList[i].lat,wilayahList[i].lng) <= wilayahList[i].radius) {
                        inRadius = true; break;
                    }
                }
                var el = document.getElementById('locationInfo');
                if (inRadius) {
                    el.className = 'location-info';
                    el.innerHTML = '<i class="fas fa-check-circle"></i> Di dalam wilayah kerja';
                } else {
                    el.className = 'location-info outside';
                    el.innerHTML = '<i class="fas fa-exclamation-circle"></i> Di luar wilayah kerja (perlu approval admin)';
                }
            }, function() {
                document.getElementById('locationInfo').innerHTML = '<i class="fas fa-times-circle" style="color:#ef4444;"></i> Gagal mendapatkan lokasi';
            }, { enableHighAccuracy:true, timeout:15000 });
        }

        // Shift
        function pickShift(el, id) {
            selectedShift = id;
            document.querySelectorAll('.shift-btn').forEach(function(b) { b.classList.remove('active'); });
            el.classList.add('active');
        }

        // Absen
        function doAbsen(jenis) {
            if (processing) return;
            var btn = jenis === 'masuk' ? document.getElementById('btnMasuk') : document.getElementById('btnPulang');
            btn.disabled = true;
            processing = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';

            // Capture foto (tidak mirror — gambar asli)
            var video = document.getElementById('video');
            var canvas = document.getElementById('captureCanvas');
            var w = video.videoWidth || 480;
            var h = video.videoHeight || 360;
            canvas.width = w;
            canvas.height = h;
            canvas.getContext('2d').drawImage(video, 0, 0, w, h);
            var fotoData = canvas.toDataURL('image/jpeg', 0.5);

            // Submit
            var form = new FormData();
            form.append('_token', document.querySelector('meta[name="csrf-token"]').content);
            form.append('jenis', jenis);
            form.append('foto', fotoData);
            form.append('lokasi', currentLokasi);
            form.append('is_lembur', '0');
            if (selectedShift) form.append('jam_shift_id', selectedShift);

            fetch('{{ route("pegawai.presensi.store") }}', { method:'POST', body:form })
                .then(function(r) {
                    if (r.redirected || r.ok) {
                        showToast('Absen ' + jenis + ' berhasil!', 'success');
                        setTimeout(function() { window.location.href = '{{ route("pegawai.dashboard") }}'; }, 1500);
                    } else {
                        return r.text().then(function(t) { throw new Error(t); });
                    }
                })
                .catch(function(e) {
                    showToast('Gagal: ' + (e.message || 'Coba lagi'), 'error');
                    btn.disabled = false;
                    processing = false;
                    btn.innerHTML = jenis === 'masuk' ? '<i class="fas fa-arrow-right-to-bracket"></i> Masuk' : '<i class="fas fa-arrow-right-from-bracket"></i> Pulang';
                });
        }

        function showToast(msg, type) {
            var el = document.getElementById('toast');
            el.textContent = msg;
            el.className = 'toast toast-' + type;
            el.style.display = 'block';
            setTimeout(function() { el.style.display = 'none'; }, 3000);
        }
    </script>
</body>
</html>
