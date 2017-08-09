<?php

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

$app->get('/', 'HomeController@index');

$app->post('/auth/login', 'AuthController@login');

//$app->get('/', function () use ($app) {
//    return $app->version();
//});

$app->group([
    'middleware' => 'auth',
], function ($app) {
    $app->get('/profile', 'ProfileController@index');
    $app->put('/profile', 'ProfileController@update');
});
