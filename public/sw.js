// Brandara Service Worker — Web Push Notifications

self.addEventListener('push', function (event) {
    if (!event.data) return;

    let payload;
    try {
        payload = event.data.json();
    } catch (e) {
        payload = { title: 'Brandara', body: event.data.text() };
    }

    const title = payload.title || 'Brandara';
    const options = {
        body: payload.body || '',
        icon: payload.icon || '/brandara-icon.svg',
        badge: '/brandara-icon.svg',
        data: { url: payload.action_url || '/' },
        actions: payload.actions || [],
        vibrate: [200, 100, 200],
    };

    event.waitUntil(self.registration.showNotification(title, options));
});

self.addEventListener('notificationclick', function (event) {
    event.notification.close();

    const url = event.notification.data?.url || '/';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function (windowClients) {
            for (let client of windowClients) {
                if (client.url === url && 'focus' in client) {
                    return client.focus();
                }
            }
            if (clients.openWindow) {
                return clients.openWindow(url);
            }
        })
    );
});
