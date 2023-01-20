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
$router->group(['prefix' => 'api'], function ($router) {
    $router->post('/routes', 'CrudController@getRoutes');
    $router->post('/routes/add', 'CrudController@addRoute');
    $router->post('/route_stages/{id}', 'CrudController@getRouteStages');
    $router->post('/stages', 'CrudController@getStages');
    $router->post('/stages/add', 'CrudController@addStage');
});
