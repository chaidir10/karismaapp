// Karisma SW v7
self.addEventListener('install', function() { self.skipWaiting(); });
self.addEventListener('activate', function(e) { e.waitUntil(self.clients.claim()); });

// ── Push handler ──────────────────────────────────────────────
self.addEventListener('push', function(event) {
    var data = {};
    try { data = event.data ? event.data.json() : {}; } catch(e) {}

    event.waitUntil(
        self.registration.showNotification(data.title || 'Karisma', {
            body  : data.body || '',
            icon  : '/public/pwa/icons/icon-192x192.png',
            badge : '/public/pwa/icons/icon-192x192.png',
            tag   : data.tag || 'karisma',
            data  : { url: data.url || '/pegawai/dashboard' }
        }).then(function() {
            return self.clients.matchAll({ type: 'window' }).then(function(list) {
                list.forEach(function(c) { c.postMessage({ type: 'NOTIF_RECEIVED' }); });
            });
        })
    );
});

// ── Notification click ────────────────────────────────────────
self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    var target = (event.notification.data && event.notification.data.url) || '/pegawai/dashboard';
    var isExternal = target.startsWith('http://') || target.startsWith('https://');

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function(list) {
            // Untuk URL internal: cari tab dashboard yang sudah terbuka
            if (!isExternal) {
                for (var i = 0; i < list.length; i++) {
                    var c = list[i];
                    if (c.url.indexOf('/pegawai/dashboard') !== -1 && 'focus' in c) {
                        c.focus();
                        // Kirim perintah buka modal ke halaman
                        c.postMessage({ type: 'OPEN_URL', url: target });
                        return;
                    }
                }
                // Tidak ada tab terbuka, buka baru
                return clients.openWindow(target);
            }
            // URL eksternal: buka tab baru
            return clients.openWindow(target);
        })
    );
});
