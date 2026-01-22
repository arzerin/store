# Browser Push Notifications

This implementation provides a complete browser push notification system using the Web Push API with VAPID authentication.

## Features

- Browser push notifications using Web Push API
- VAPID authentication for secure communication
- Service Worker for background notification handling
- Database storage for push subscriptions
- Easy-to-use JavaScript client library
- CLI command to generate VAPID keys

## Setup

### 1. Install Dependencies

```bash
composer install
```

This will install the required `minishlink/web-push` package.

### 2. Run Database Migration

```bash
php spark migrate
```

This creates the `push_subscriptions` table to store user subscriptions.

### 3. Generate VAPID Keys

```bash
php spark webpush:generate-keys
```

This command will generate a public and private VAPID key pair. Copy these keys to your `app/Config/WebPush.php` file:

```php
public string $publicKey = 'YOUR_PUBLIC_KEY_HERE';
public string $privateKey = 'YOUR_PRIVATE_KEY_HERE';
public string $subject = 'mailto:your-email@example.com'; // or your website URL
```

**Important:** Never commit your private key to version control!

### 4. Configure Routes

Add these routes to your `app/Config/Routes.php`:

```php
$routes->get('push_public_key', 'Home::push_public_key');
$routes->post('push_subscribe', 'Home::push_subscribe');
$routes->post('push_unsubscribe', 'Home::push_unsubscribe');
$routes->post('push_send', 'Home::push_send');
```

## Frontend Integration

### 1. Include JavaScript Files

Add these scripts to your HTML page:

```html
<script src="/push-notifications.js"></script>
```

### 2. Initialize and Subscribe

```javascript
// Create instance
const pushManager = new PushNotificationManager();

// Initialize
await pushManager.init();

// Subscribe to notifications
const subscribeButton = document.getElementById('subscribe-btn');
subscribeButton.addEventListener('click', async () => {
    try {
        const subscription = await pushManager.subscribe();
        console.log('Subscribed successfully!', subscription);
        alert('You are now subscribed to push notifications!');
    } catch (error) {
        console.error('Subscription failed:', error);
        alert('Failed to subscribe: ' + error.message);
    }
});

// Unsubscribe
const unsubscribeButton = document.getElementById('unsubscribe-btn');
unsubscribeButton.addEventListener('click', async () => {
    try {
        await pushManager.unsubscribe();
        console.log('Unsubscribed successfully!');
        alert('You have been unsubscribed from push notifications.');
    } catch (error) {
        console.error('Unsubscribe failed:', error);
        alert('Failed to unsubscribe: ' + error.message);
    }
});

// Check subscription status
const isSubscribed = await pushManager.isSubscribed();
console.log('Subscription status:', isSubscribed);
```

## API Endpoints

### Get VAPID Public Key

```
GET /push_public_key
```

Returns the VAPID public key needed for frontend subscription.

**Response:**
```json
{
    "publicKey": "YOUR_PUBLIC_KEY"
}
```

### Subscribe to Push Notifications

```
POST /push_subscribe
Content-Type: application/json
```

**Request Body:**
```json
{
    "endpoint": "https://fcm.googleapis.com/fcm/send/...",
    "keys": {
        "p256dh": "...",
        "auth": "..."
    },
    "contentEncoding": "aesgcm"
}
```

**Response:**
```json
{
    "status": "success",
    "message": "User subscribed for push notifications"
}
```

### Unsubscribe from Push Notifications

```
POST /push_unsubscribe
Content-Type: application/json
```

**Request Body:**
```json
{
    "endpoint": "https://fcm.googleapis.com/fcm/send/..."
}
```

**Response:**
```json
{
    "status": "success",
    "message": "Unsubscribed successfully"
}
```

### Send Push Notification

```
POST /push_send
```

Sends a push notification to all subscribed users.

**Response:**
```json
{
    "status": "success",
    "message": "Push notifications sent",
    "sent": 5,
    "failed": 0
}
```

## Customizing Notifications

To customize the notification payload, edit the `push_send()` method in `app/Controllers/Home.php`:

```php
$payload = json_encode([
    'title' => 'Your Custom Title',
    'body' => 'Your custom message',
    'icon' => '/path/to/icon.png',
    'badge' => '/path/to/badge.png',
    'data' => [
        'url' => 'https://yoursite.com/page'
    ]
]);
```

## Browser Support

Push notifications are supported in:
- Chrome 50+
- Firefox 44+
- Edge 17+
- Safari 16+ (macOS 13+, iOS 16.4+)
- Opera 37+

## Security Notes

1. **HTTPS Required:** Push notifications only work over HTTPS (except localhost for testing)
2. **Keep Private Key Secret:** Never expose your VAPID private key in client-side code or public repositories
3. **User Permission:** Always request permission before subscribing users
4. **Validate Input:** The implementation validates all subscription data before saving

## Testing

1. Open your application in a supported browser
2. Click the subscribe button
3. Grant notification permission when prompted
4. Call the `/push_send` endpoint to send a test notification
5. You should receive a notification with "Hello Sir"

## Troubleshooting

### Service Worker Not Registering
- Ensure you're using HTTPS or localhost
- Check browser console for errors
- Verify `/service-worker.js` is accessible

### Notifications Not Appearing
- Check notification permissions in browser settings
- Verify VAPID keys are correctly configured
- Check browser console for errors
- Ensure the service worker is active

### Subscription Fails
- Verify VAPID public key is correctly set
- Check network requests in browser dev tools
- Ensure database migration has been run

## Database Schema

The `push_subscriptions` table stores:
- `id`: Primary key
- `endpoint`: Push service endpoint URL
- `public_key`: Subscriber's public key (p256dh)
- `auth_token`: Subscriber's auth token
- `content_encoding`: Content encoding (default: aesgcm)
- `created_at`: Timestamp
- `updated_at`: Timestamp
