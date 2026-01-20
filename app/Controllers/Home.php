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
}
