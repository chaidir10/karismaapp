const CACHE_NAME = "karisma-pwa-v3";

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
            Promise.all(names.filter(n => n !== CACHE_NAME).map(n => caches.delete(n)))
        )
    );
    self.clients.claim();
});

// Network-first strategy — always try network, fallback to cache
// This works well with Turbo Drive (no stale pages served)
self.addEventListener("fetch", event => {
    // Skip non-GET requests
    if (event.request.method !== 'GET') return;

    // Skip cross-origin requests (CDN scripts, fonts, etc)
    if (!event.request.url.startsWith(self.location.origin)) return;

    event.respondWith(
        fetch(event.request)
            .then(response => {
                // Cache successful responses for offline fallback
                if (response.status === 200) {
                    var clone = response.clone();
                    caches.open(CACHE_NAME).then(cache => cache.put(event.request, clone));
                }
                return response;
            })
            .catch(() => {
                // Offline — try cache
                return caches.match(event.request).then(cached => {
                    if (cached) return cached;
                    // Fallback for navigation requests
                    if (event.request.destination === 'document') {
                        return caches.match('/pegawai/dashboard');
                    }
                });
            })
    );
});
