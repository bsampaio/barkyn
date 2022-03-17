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
    return 'Barkyn Challenge';
});

/**
 * Customers
 */
$router->group(['prefix' => 'customers'], function () use ($router) {
    $router->get('/', [
        'as' => 'customers.index',
        'uses' => 'CustomersController@index'
    ]);

    $router->get('/{id}/subscription', [
        'as' => 'customers.subscription',
        'uses' => 'CustomersController@getSubscription'
    ]);
});
