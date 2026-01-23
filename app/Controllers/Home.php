<?php

namespace App\Controllers;

use App\Models\PushSubscriptionModel;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class Home extends BaseController
{
    public function index(): string
    {
        return view('welcome_message');
    }

    public function helloWorld(): string
    {
        return 'Hello World';
    }

    public function helloWorld2(): string
    {
        return 'Hello World2';
    }

    /**
     * Get VAPID public key for frontend
     */
    public function push_public_key()
    {
        $config = config('WebPush');

        return $this->response->setJSON([
            'publicKey' => $config->publicKey
        ]);
    }

    /**
     * Subscribe user to push notifications
     */
    public function push_subscribe()
    {
        $model = new PushSubscriptionModel();

        // Get JSON input
        $json = $this->request->getJSON();

        if (!$json) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'message' => 'Invalid JSON data'
            ]);
        }

        // Validate required fields
        if (empty($json->endpoint) || empty($json->keys->p256dh) || empty($json->keys->auth)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'message' => 'Missing required subscription data'
            ]);
        }

        // Check if subscription already exists
        $existing = $model->findByEndpoint($json->endpoint);

        if ($existing) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Subscription already exists'
            ]);
        }

        // Save subscription
        $data = [
            'endpoint' => $json->endpoint,
            'public_key' => $json->keys->p256dh,
            'auth_token' => $json->keys->auth,
            'content_encoding' => $json->contentEncoding ?? 'aesgcm',
        ];

        if ($model->insert($data)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'User subscribed for push notifications'
            ]);
        }

        return $this->response->setStatusCode(500)->setJSON([
            'status' => 'error',
            'message' => 'Failed to save subscription'
        ]);
    }

    /**
     * Send push notification to all subscribed users
     */
    public function push_send()
    {
        $config = config('WebPush');
        $model = new PushSubscriptionModel();

        // Check if VAPID keys are configured
        if (empty($config->publicKey) || empty($config->privateKey)) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'VAPID keys not configured. Please run: php spark webpush:generate-keys'
            ]);
        }

        // Get all subscriptions
        $subscriptions = $model->getAllSubscriptions();

        if (empty($subscriptions)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'No subscribers found',
                'sent' => 0
            ]);
        }

        // Initialize WebPush
        $auth = [
            'VAPID' => [
                'subject' => $config->subject,
                'publicKey' => $config->publicKey,
                'privateKey' => $config->privateKey,
            ],
        ];

        $webPush = new WebPush($auth);

        // Prepare notification payload
        $payload = json_encode([
            'title' => 'Hello Sir',
            'body' => 'This is a push notification from your store!',
            'icon' => '/favicon.ico',
            'badge' => '/favicon.ico',
            'sound' => 'http://localhost/personal/store/public/beep.mp3',
            'image' => 'http://localhost/personal/store/public/team1.jpg',
        ]);

        $sent = 0;
        $failed = 0;

        // Send to all subscriptions
        foreach ($subscriptions as $sub) {
            $subscription = Subscription::create([
                'endpoint' => $sub['endpoint'],
                'publicKey' => $sub['public_key'],
                'authToken' => $sub['auth_token'],
                'contentEncoding' => $sub['content_encoding'],
            ]);

            try {
                $webPush->queueNotification($subscription, $payload);
                $sent++;
            } catch (\Exception $e) {
                $failed++;
                log_message('error', 'Push notification failed: ' . $e->getMessage());
            }
        }

        // Flush the queue
        foreach ($webPush->flush() as $report) {
            $endpoint = $report->getRequest()->getUri()->__toString();

            if (!$report->isSuccess()) {
                // Remove invalid subscriptions
                if ($report->isSubscriptionExpired()) {
                    $model->where('endpoint', $endpoint)->delete();
                }

                log_message('error', 'Push notification failed for endpoint: ' . $endpoint);
            }
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Push notifications sent',
            'sent' => $sent,
            'failed' => $failed
        ]);
    }

    /**
     * Unsubscribe from push notifications
     */
    public function push_unsubscribe()
    {
        $model = new PushSubscriptionModel();
        $json = $this->request->getJSON();

        if (!$json || empty($json->endpoint)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'message' => 'Endpoint is required'
            ]);
        }

        $model->where('endpoint', $json->endpoint)->delete();

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Unsubscribed successfully'
        ]);
    }
}
