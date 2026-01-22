/**
 * Push Notifications Client-side Handler
 */

class PushNotificationManager {
    constructor() {
        this.publicKey = null;
        this.registration = null;
        this.isSupported = this.checkSupport();
    }

    /**
     * Check if push notifications are supported
     */
    checkSupport() {
        if (!('serviceWorker' in navigator)) {
            console.warn('Service Workers not supported');
            return false;
        }

        if (!('PushManager' in window)) {
            console.warn('Push API not supported');
            return false;
        }

        if (!('Notification' in window)) {
            console.warn('Notifications not supported');
            return false;
        }

        return true;
    }

    /**
     * Initialize push notifications
     */
    async init() {
        if (!this.isSupported) {
            throw new Error('Push notifications are not supported in this browser');
        }

        try {
            // Register service worker
            this.registration = await navigator.serviceWorker.register('/service-worker.js');
            console.log('Service Worker registered:', this.registration);

            // Get VAPID public key from server
            const response = await fetch('/push_public_key');
            const data = await response.json();
            this.publicKey = data.publicKey;

            if (!this.publicKey) {
                throw new Error('VAPID public key not configured on server');
            }

            console.log('Push Notification Manager initialized');
            return true;
        } catch (error) {
            console.error('Failed to initialize push notifications:', error);
            throw error;
        }
    }

    /**
     * Request notification permission
     */
    async requestPermission() {
        const permission = await Notification.requestPermission();

        if (permission !== 'granted') {
            throw new Error('Notification permission denied');
        }

        return permission;
    }

    /**
     * Subscribe to push notifications
     */
    async subscribe() {
        try {
            // Ensure service worker is ready
            await navigator.serviceWorker.ready;

            // Request permission
            await this.requestPermission();

            // Get push subscription
            let subscription = await this.registration.pushManager.getSubscription();

            // If already subscribed, return existing subscription
            if (subscription) {
                console.log('Already subscribed:', subscription);
                return subscription;
            }

            // Subscribe to push
            subscription = await this.registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: this.urlBase64ToUint8Array(this.publicKey)
            });

            console.log('New subscription:', subscription);

            // Send subscription to server
            await this.sendSubscriptionToServer(subscription);

            return subscription;
        } catch (error) {
            console.error('Failed to subscribe:', error);
            throw error;
        }
    }

    /**
     * Unsubscribe from push notifications
     */
    async unsubscribe() {
        try {
            const subscription = await this.registration.pushManager.getSubscription();

            if (!subscription) {
                console.log('Not subscribed');
                return true;
            }

            // Unsubscribe
            await subscription.unsubscribe();

            // Notify server
            await this.sendUnsubscribeToServer(subscription);

            console.log('Unsubscribed successfully');
            return true;
        } catch (error) {
            console.error('Failed to unsubscribe:', error);
            throw error;
        }
    }

    /**
     * Check if currently subscribed
     */
    async isSubscribed() {
        try {
            const subscription = await this.registration.pushManager.getSubscription();
            return subscription !== null;
        } catch (error) {
            console.error('Failed to check subscription status:', error);
            return false;
        }
    }

    /**
     * Get current subscription
     */
    async getSubscription() {
        try {
            return await this.registration.pushManager.getSubscription();
        } catch (error) {
            console.error('Failed to get subscription:', error);
            return null;
        }
    }

    /**
     * Send subscription to server
     */
    async sendSubscriptionToServer(subscription) {
        const response = await fetch('/push_subscribe', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(subscription.toJSON())
        });

        if (!response.ok) {
            throw new Error('Failed to send subscription to server');
        }

        return await response.json();
    }

    /**
     * Send unsubscribe to server
     */
    async sendUnsubscribeToServer(subscription) {
        const response = await fetch('/push_unsubscribe', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                endpoint: subscription.endpoint
            })
        });

        if (!response.ok) {
            throw new Error('Failed to unsubscribe on server');
        }

        return await response.json();
    }

    /**
     * Convert base64 VAPID key to Uint8Array
     */
    urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding)
            .replace(/\-/g, '+')
            .replace(/_/g, '/');

        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);

        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }

        return outputArray;
    }
}

// Export for use
if (typeof module !== 'undefined' && module.exports) {
    module.exports = PushNotificationManager;
}
