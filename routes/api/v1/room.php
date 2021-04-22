<?php

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', [
    'namespace' => 'App\Http\Controllers\Api\V1\Room',
    'middleware' => ['auth:api', 'role:staff|student|admin'],
    'prefix' => 'v1/room'
], function($api) {
    $api->get('/', ['as' => 'room.index', 'uses' => 'RoomController@index']);
    // without paginator
    // $api->get('/rooms', ['as' => 'room.rooms', 'uses' => 'RoomController@rooms']);
});
