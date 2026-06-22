const CACHE_NAME = "karisma-pwa-v6";

const urlsToCache = [
    "/public/pwa/icons/icon-192x192.png",
    "/public/pwa/icons/icon-512x512.png"
];

self.addEventListener("install", event => {
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => cache.addAll(urlsToCache))
    );
    self.skipWaiting();
});

self.addEventListener("activate", event => {
    event.waitUntil(
        caches.keys().then(names =>
            Promise.all(names.map(n => caches.delete(n)))
        ).then(() => caches.open(CACHE_NAME).then(cache => cache.addAll(urlsToCache)))
    );
    self.clients.claim();
    self.clients.matchAll().then(clients => {
        clients.forEach(client => client.navigate(client.url));
    });
});

// Network-first: always fetch fresh, cache for offline only
self.addEventListener("fetch", event => {
    if (event.request.method !== 'GET') return;
    if (!event.request.url.startsWith(self.location.origin)) return;

    event.respondWith(
        fetch(event.request)
            .then(response => {
                if (response.status === 200) {
                    var clone = response.clone();
                    caches.open(CACHE_NAME).then(cache => cache.put(event.request, clone));
                }
                return response;
            })
            .catch(() => {
                return caches.match(event.request).then(cached => {
                    if (cached) return cached;
                    if (event.request.destination === 'document') {
                        return caches.match('/pegawai/dashboard');
                    }
                });
            })
    );
});
