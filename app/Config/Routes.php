<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');


$routes->get('push_public_key', 'Home::push_public_key');
$routes->post('push_subscribe', 'Home::push_subscribe');
$routes->post('push_unsubscribe', 'Home::push_unsubscribe');
$routes->get('push_send', 'Home::push_send');
$routes->post('push_send', 'Home::push_send');


$routes->cli('push_send', 'Home::push_send');

