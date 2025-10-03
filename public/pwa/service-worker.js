const CACHE_NAME = "karisma-pwa-v1";

// Daftar URL yang dicache saat install
const urlsToCache = [
    "/",
    "/css/app.css",
    "/js/app.js",
    "public/pwa/icons/icon-192x192.png",
    "public/pwa/icons/icon-512x512.png"
];

// Install Service Worker
self.addEventListener("install", event => {
    console.log("[SW] Installing...");
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => {
            console.log("[SW] Caching app shell");
            return cache.addAll(urlsToCache);
        })
    );
    self.skipWaiting(); // aktifkan SW baru segera
});

// Activate Service Worker dan hapus cache lama
self.addEventListener("activate", event => {
    console.log("[SW] Activating...");
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames
                    .filter(name => name !== CACHE_NAME)
                    .map(name => caches.delete(name))
            );
        })
    );
    self.clients.claim(); // kontrol client segera
});

// Fetch requests dengan cache-first strategy
self.addEventListener("fetch", event => {
    event.respondWith(
        caches.match(event.request).then(response => {
            if (response) {
                // Return dari cache jika ada
                return response;
            }
            // Fetch dari network jika tidak ada di cache
            return fetch(event.request)
                .then(networkResponse => {
                    // Cache hasil fetch untuk next visit
                    if (!networkResponse || networkResponse.status !== 200 || networkResponse.type !== "basic") {
                        return networkResponse;
                    }
                    const responseClone = networkResponse.clone();
                    caches.open(CACHE_NAME).then(cache => {
                        cache.put(event.request, responseClone);
                    });
                    return networkResponse;
                })
                .catch(() => {
                    // Fallback jika offline dan resource tidak ada di cache
                    if (event.request.destination === "document") {
                        return caches.match("/"); // fallback ke homepage
                    }
                });
        })
    );
});
