/**
 * Push Notifications Client-side Handler
 */

class PushNotificationManager {
    constructor() {
        this.publicKey = null;
        this.registration = null;
        this.isSupported = this.checkSupport();
    }

    checkSupport() {
        if (!('serviceWorker' in navigator)) return false;
        if (!('PushManager' in window)) return false;
        if (!('Notification' in window)) return false;
        return true;
    }

    async init() {
        if (!this.isSupported) {
            throw new Error('Push notifications are not supported in this browser');
        }

        // Register service worker (RELATIVE PATH — IMPORTANT)
        this.registration = await navigator.serviceWorker.register(
            'http://localhost/personal/store/public/service-worker.js?v=1.0.3'
        );

        // Fetch VAPID public key
        const response = await fetch('http://localhost/personal/store/public/push_public_key');
        const data = await response.json();

        if (!data.publicKey) {
            throw new Error('VAPID public key missing from server');
        }

        this.publicKey = data.publicKey;
        return true;
    }

    /**
     * Request permission safely
     */
    async requestPermission() {
        if (Notification.permission === 'granted') {
            return 'granted';
        }

        if (Notification.permission === 'denied') {
            throw new Error(
                'Notifications are blocked. Please enable them in browser site settings.'
            );
        }

        const permission = await Notification.requestPermission();

        if (permission !== 'granted') {
            throw new Error('Notification permission denied');
        }

        return permission;
    }

    async subscribe() {
        try {
            await navigator.serviceWorker.ready;

            await this.requestPermission();

            let subscription =
                await this.registration.pushManager.getSubscription();

            if (subscription) {
                return subscription;
            }

            subscription = await this.registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: this.urlBase64ToUint8Array(this.publicKey),
            });

            await this.sendSubscriptionToServer(subscription);

            return subscription;
        } catch (error) {
            console.error(error);
            throw error;
        }
    }

    async unsubscribe() {
        const subscription =
            await this.registration.pushManager.getSubscription();

        if (!subscription) return true;

        await subscription.unsubscribe();
        await this.sendUnsubscribeToServer(subscription);

        return true;
    }

    async isSubscribed() {
        const subscription =
            await this.registration.pushManager.getSubscription();
        return subscription !== null;
    }

    async sendSubscriptionToServer(subscription) {
        const response = await fetch('http://localhost/personal/store/public/push_subscribe', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(subscription.toJSON()),
        });

        if (!response.ok) {
            throw new Error('Failed to store subscription on server');
        }
    }

    async sendUnsubscribeToServer(subscription) {
        await fetch('http://localhost/personal/store/public/push_unsubscribe', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ endpoint: subscription.endpoint }),
        });
    }

    urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - (base64String.length % 4)) % 4);
        const base64 = (base64String + padding)
            .replace(/-/g, '+')
            .replace(/_/g, '/');

        const rawData = atob(base64);
        return Uint8Array.from([...rawData].map(c => c.charCodeAt(0)));
    }
}
