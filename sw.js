/* Service Worker für Förderverein intern. Gültigkeitsbereich: /intern/ */
'use strict';

self.addEventListener('push', function (event) {
    let data = {};
    if (event.data) {
        try { data = event.data.json(); }
        catch (_) { data = { body: event.data.text() }; }
    }

    const title = typeof data.title === 'string' && data.title ? data.title : 'Förderverein intern';
    const options = {
        body: typeof data.body === 'string' ? data.body : 'Du hast eine neue Benachrichtigung.',
        icon: '/intern/assets/icons/icon-192.png',
        badge: '/intern/assets/icons/icon-192.png',
        data: { url: typeof data.url === 'string' ? data.url : '/intern/notifications.php' },
        tag: typeof data.tag === 'string' ? data.tag : 'foerderverein-notification'
    };

    event.waitUntil(self.registration.showNotification(title, options));
});

self.addEventListener('notificationclick', function (event) {
    event.notification.close();

    let target = '/intern/notifications.php';
    try {
        const candidate = new URL(event.notification.data && event.notification.data.url || target, self.location.origin);
        if (candidate.origin === self.location.origin && candidate.pathname.startsWith('/intern/')) {
            target = candidate.href;
        }
    } catch (_) {}

    event.waitUntil((async function () {
        const clientsList = await self.clients.matchAll({ type: 'window', includeUncontrolled: true });
        for (const client of clientsList) {
            if (client.url.startsWith(self.location.origin + '/intern/') && 'focus' in client) {
                await client.focus();
                if ('navigate' in client && client.url !== target) {
                    try { await client.navigate(target); } catch (_) {}
                }
                return;
            }
        }
        if (self.clients.openWindow) await self.clients.openWindow(target);
    })());
});
