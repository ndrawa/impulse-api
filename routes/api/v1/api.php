<?php

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', [
    'namespace' => 'App\Http\Controllers\Api\V1',
    'prefix' => 'v1'
], function ($api) {
    $api->get('/', function() use ($api) {
        return microtime(true);
    });

    $api->group(['prefix' => 'auth'], function($api) {
        $api->post('login', ['as' => 'auth.login', 'uses' => 'AuthController@login']);
    });

    $api->group(['middleware' => 'auth:api'], function($api) {
        $api->get('/me', ['as' => 'profile.me', 'uses' => 'UserController@me']);
        $api->put('/me', ['as' => 'profile.me.update', 'uses' => 'UserController@updateUsername']);
        $api->put('/me/update-password', ['as' => 'profile.me.update.password', 'uses' => 'UserController@updatePassword']);
    });
});