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

    $router->get('/{id}/subscription/', [
        'as' => 'customers.subscription',
        'uses' => 'CustomersController@subscription'
    ]);

    $router->put('/{id}', [
        'as' => 'customers.update',
        'uses' => 'CustomersController@update'
    ]);

    $router->post('/{customerId}/subscriptions', [
        'as'   => 'subscriptions.create',
        'uses' => 'SubscriptionsController@create'
    ]);

    $router->get('/{customerId}/subscription/pets', [
        'as' => 'subscriptions.pets',
        'uses' => 'SubscriptionsController@pets'
    ]);

    $router->post('/{customerId}/subscription/pets/', [
        'as' => 'subscriptions.pets.add',
        'uses' => 'SubscriptionsController@addPet'
    ]);

    $router->delete('/{customerId}/subscription/pets/{petId}', [
        'as' => 'subscriptions.pets.remove',
        'uses' => 'SubscriptionsController@removePet'
    ]);

    $router->put('/{customerId}/subscription/', [
        'as' => 'subscriptions.update',
        'uses' => 'SubscriptionsController@updateNextOrder'
    ]);

    $router->post('/{customerId}/orders', [
        'as' => 'subscriptions.update',
        'uses' => 'SubscriptionsController@orderNow'
    ]);
});

