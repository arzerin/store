<?php

namespace App\Config;

use CodeIgniter\Config\BaseConfig;

class WebPush extends BaseConfig
{
    /**
     * VAPID Public Key
     * Generate using: vendor/bin/web-push generate-vapid-keys
     */
    public string $publicKey = '';

    /**
     * VAPID Private Key
     * Generate using: vendor/bin/web-push generate-vapid-keys
     * IMPORTANT: Keep this secret and never commit to version control
     */
    public string $privateKey = '';

    /**
     * Subject (must be a URL or mailto: address)
     * Example: 'mailto:admin@example.com' or 'https://example.com'
     */
    public string $subject = 'mailto:admin@example.com';

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
