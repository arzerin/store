// Service Worker for Push Notifications

self.addEventListener('install', (event) => {
    console.log('Service Worker installing.');
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    console.log('Service Worker activating.');
    event.waitUntil(clients.claim());
});

// Handle push notifications
self.addEventListener('push', (event) => {
    console.log('Push notification received:', event);

    let data = {};

    if (event.data) {
        try {
            data = event.data.json();
        } catch (e) {
            data = {
                title: 'Notification',
                body: event.data.text()
            };
        }
    }

    const title = data.title || 'New Notification';
    const options = {
        body: data.body || 'You have a new notification',
        icon: data.icon || '/favicon.ico',
        badge: data.badge || '/favicon.ico',
        image: data.image || null,                   // optional large image
        tag: 'push-' + Date.now(), // UNIQUE every time
        renotify: true,
        requireInteraction: data.requireInteraction || false,
        data: data.data || {},
        //data: data.url || '/',                        // URL to open on click
        actions: data.actions || [],
        sound: data.sound || null                     // Note: Only supported on some OS
    };

    event.waitUntil(
        self.registration.showNotification(title, options)
    );
});

// Handle notification click
self.addEventListener('notificationclick', (event) => {
    console.log('Notification clicked:', event);

    event.notification.close();

    event.waitUntil(
        clients.openWindow(event.notification.data.url || '/')
    );
});

// Handle notification close
self.addEventListener('notificationclose', (event) => {
    console.log('Notification closed:', event);
});
