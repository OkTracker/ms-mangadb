<?php

/** @var \Laravel\Lumen\Routing\Router $router */

use App\Http\Controllers\BookController;
use Illuminate\Support\Facades\Storage;

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

$router->group(['prefix' => 'books'], function () use ($router) {
    $router->get('/', 'BookController@index');
    $router->get('/search/{query}', 'BookController@search');
    $router->get('/isbn/{isbn}', 'BookController@isbn');
    $router->get('/{id}', 'BookController@show');
    $router->post('/', 'BookController@store');
    $router->put('/{id}', 'BookController@update');
    $router->delete('/{id}', 'BookController@destroy');
});

$router->group(['prefix' => 'series'], function () use ($router) {
    $router->get('/', 'SeriesController@index');
    $router->get('/search/{query}', 'SeriesController@search');
    $router->get('/{id}', 'SeriesController@show');
    $router->post('/', 'SeriesController@store');
    $router->put('/{id}', 'SeriesController@update');
    $router->delete('/{id}', 'SeriesController@destroy');
});