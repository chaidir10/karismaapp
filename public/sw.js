// Karisma SW v4
self.addEventListener('install', function() { self.skipWaiting(); });

// ── IndexedDB helpers ─────────────────────────────────────────
function openNotifDB() {
    return new Promise(function(resolve) {
        var req = indexedDB.open('karisma-notif-db', 1);
        req.onupgradeneeded = function(e) {
            e.target.result.createObjectStore('notifications', { keyPath: 'id', autoIncrement: true });
        };
        req.onsuccess = function(e) { resolve(e.target.result); };
        req.onerror   = function()  { resolve(null); };
    });
}

function saveNotif(data) {
    return openNotifDB().then(function(db) {
        if (!db) return;
        var tx = db.transaction('notifications', 'readwrite');
        tx.objectStore('notifications').add({
            title : data.title || 'Karisma',
            body  : data.body  || '',
            tag   : data.tag   || '',
            url   : data.url   || '/pegawai/dashboard',
            time  : Date.now(),
            read  : false
        });
    });
}

// ── Push handler ──────────────────────────────────────────────
self.addEventListener('push', function(event) {
    var data = {};
    try { data = event.data ? event.data.json() : {}; } catch(e) {}

    event.waitUntil(
        saveNotif(data).then(function() {
            return self.registration.showNotification(data.title || 'Karisma', {
                body  : data.body || '',
                icon  : '/public/pwa/icons/icon-192x192.png',
                badge : '/public/pwa/icons/icon-192x192.png',
                tag   : data.tag || 'karisma',
                data  : { url: data.url || '/pegawai/dashboard' }
            });
        }).then(function() {
            // Beritahu semua tab yang terbuka agar update badge
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
                if ('focus' in list[i]) return list[i].focus();
            }
            if (clients.openWindow) return clients.openWindow(target);
        })
    );
});
