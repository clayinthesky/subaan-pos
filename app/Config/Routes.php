<?php
$routes->setAutoRoute(false);
$routes->get('login', 'Auth::index');
$routes->post('login', 'Auth::login');
$routes->group('', ['filter'=>'auth'], static function($routes) {
    $routes->get('/', 'Dashboard::index');
    $routes->post('logout', 'Auth::logout');
    foreach (['products','customers','staff'] as $resource) {
        $routes->get($resource, 'Management::index/'.$resource);
        $routes->get($resource.'/new', 'Management::form/'.$resource);
        $routes->get($resource.'/(:num)/edit', 'Management::form/'.$resource.'/$1');
        $routes->post($resource.'/save', 'Management::save/'.$resource);
        $routes->post($resource.'/(:num)/delete', 'Management::delete/'.$resource.'/$1');
    }
    $routes->get('sales/new', 'Sales::form');
    $routes->post('sales', 'Sales::save');
    $routes->get('sales', 'Sales::index');
    $routes->get('media/(:segment)', 'Media::show/$1');
});
