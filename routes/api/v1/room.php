<?php

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', [
    'namespace' => 'App\Http\Controllers\Api\V1\Room',
    'middleware' => ['auth:api', 'role:staff|student|admin|laboran'],
    'prefix' => 'v1/room'
], function($api) {
    $api->get('/', ['as' => 'room.index', 'uses' => 'RoomController@index']);
    $api->get('/{id}', ['as' => 'room.show', 'uses' => 'RoomController@show']);
    $api->put('/{id}', ['as' => 'room.update', 'uses' => 'RoomController@update']);
    $api->post('/', ['as' => 'room.create', 'uses' => 'RoomController@create']);
    $api->delete('/{id}', ['as' => 'room.delete', 'uses' => 'RoomController@delete']);
});
