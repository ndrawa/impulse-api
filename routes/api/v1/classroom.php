<?php

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', [
    'namespace' => 'App\Http\Controllers\Api\V1\Classroom',
    'middleware' => ['auth:api', 'role:staff|student|admin'],
    'prefix' => 'v1/classroom'
], function($api) {
    $api->get('/', ['as' => 'classroom.index', 'uses' => 'ClassroomController@index']);
    $api->get('/{id}', ['as' => 'classroom.show', 'uses' => 'ClassroomController@show']);
    $api->put('/{id}', ['as' => 'classroom.update', 'uses' => 'ClassroomController@update']);
    $api->post('/', ['as' => 'classroom.create', 'uses' => 'ClassroomController@create']);
    $api->delete('/{id}', ['as' => 'classroom.delete', 'uses' => 'ClassroomController@delete']);
});
