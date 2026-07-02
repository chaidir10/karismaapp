// Karisma SW v3
self.addEventListener('install', function() { self.skipWaiting(); });

self.addEventListener('push', function(event) {
    var data = {};
    try { data = event.data ? event.data.json() : {}; } catch(e) {}
    event.waitUntil(
        self.registration.showNotification(data.title || 'Karisma', {
            body: data.body || '',
            icon: '/public/pwa/icons/icon-192x192.png',
            badge: '/public/pwa/icons/icon-192x192.png',
            tag: data.tag || 'karisma',
            data: { url: data.url || '/pegawai/dashboard' }
        })
    );
});

self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    var target = (event.notification.data && event.notification.data.url) || '/pegawai/dashboard';
    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function(list) {
            for (var i = 0; i < list.length; i++) {
                if ('focus' in list[i]) return list[i].focus();
            }
            if (clients.openWindow) return clients.openWindow(target);
        })
    );
});
