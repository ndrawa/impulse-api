<?php

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', [
    'namespace' => 'App\Http\Controllers\Api\V1\Laboran',
    'middleware' => ['auth:api', 'role:admin|laboran'],
    'prefix' => 'v1/laboran'
], function($api) {
    $api->get('/', ['as' => 'laboran.index', 'uses' => 'LaboranController@index']);
    $api->get('/{id}', ['as' => 'laboran.show', 'uses' => 'LaboranController@show']);
    // $api->put('/{id}', ['as' => 'laboran.update', 'uses' => 'LaboranController@update']);
    $api->post('/', ['as' => 'laboran.create', 'uses' => 'LaboranController@create']);
    $api->delete('/{id}', ['as' => 'laboran.delete', 'uses' => 'LaboranController@delete']);
});