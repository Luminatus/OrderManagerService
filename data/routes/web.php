<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->post('/api/order/create', 'OrderController@create');
$router->post('/api/order/update/{orderId}', 'OrderController@update');
$router->post('/api/order/list', 'OrderController@list');
$router->get('/api/doc', function () {
    return view('swagger.index');
});
