// Karisma SW v6
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
            // Beritahu page agar update badge dari API (server sudah log saat kirim push)
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
    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function(list) {
            for (var i = 0; i < list.length; i++) {
                if (list[i].url.indexOf(target) !== -1 && 'focus' in list[i]) return list[i].focus();
            }
            if (clients.openWindow) return clients.openWindow(target);
        })
    );
});
