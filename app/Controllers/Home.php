<?php

namespace App\Controllers;

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

    public function push_subscribe()
    {
        // Allow user to subscribe for push notification
        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'User subscribed for push notifications'
        ]);
    }

    public function push_send()
    {
        // Send push notification to user with "Hello Sir"
        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Push notification sent',
            'notification' => 'Hello Sir'
        ]);
    }
}
