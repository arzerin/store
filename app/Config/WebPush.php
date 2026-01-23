<?php

namespace App\Config;

use CodeIgniter\Config\BaseConfig;

class WebPush extends BaseConfig
{
    /**
     * VAPID Public Key
     * Generate using: vendor/bin/web-push generate-vapid-keys 
     * php spark webpush:generate-keys
     */
    public string $publicKey = 'BG9cNBNsug8HT2HMuJKden9pijawi0d4axhaNjoWtXYzHLRL-swpjMTVF-26SEcHLn1lBDI1wyCMc9jx59NkXME';

    /**
     * VAPID Private Key
     * Generate using: vendor/bin/web-push generate-vapid-keys
     * IMPORTANT: Keep this secret and never commit to version control
     */
    public string $privateKey = 'BVKkxg5U3yaieE7auSDIDe9NSSDZ879z-oRfYL2ED0w';

    /**
     * Subject (must be a URL or mailto: address)
     * Example: 'mailto:admin@example.com' or 'https://example.com'
     */
    public string $subject = 'mailto:zerin@popularllc.com';

    /**
     * Default TTL (Time To Live) in seconds
     * How long the push service should keep trying to deliver
     */
    public int $ttl = 3600;

    /**
     * Default urgency
     * Can be: 'very-low', 'low', 'normal', 'high'
     */
    public string $urgency = 'normal';
}
