const CACHE_NAME = "karisma-sw-v2";

self.addEventListener("install", () => self.skipWaiting());

self.addEventListener("activate", event => {
    event.waitUntil(
        caches.keys().then(names =>
            Promise.all(names.filter(n => n !== CACHE_NAME).map(n => caches.delete(n)))
        ).then(() => self.clients.claim())
    );
});

// Network-first: selalu ke server, cache hanya untuk offline fallback
self.addEventListener("fetch", event => {
    if (event.request.method !== 'GET') return;
    if (!event.request.url.startsWith(self.location.origin)) return;
    event.respondWith(
        fetch(event.request)
            .then(res => {
                if (res.status === 200) {
                    caches.open(CACHE_NAME).then(c => c.put(event.request, res.clone()));
                }
                return res;
            })
            .catch(() => caches.match(event.request))
    );
});

// Tampilkan notifikasi dari server (Web Push — aktif jika VAPID dikonfigurasi)
self.addEventListener("push", event => {
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

// Klik notifikasi → buka/fokus halaman dashboard
self.addEventListener("notificationclick", event => {
    event.notification.close();
    var target = (event.notification.data && event.notification.data.url) || '/pegawai/dashboard';
    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(list => {
            for (var c of list) {
                if ('focus' in c) return c.focus();
            }
            if (clients.openWindow) return clients.openWindow(target);
        })
    );
});
