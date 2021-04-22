<?php

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', [
    'namespace' => 'App\Http\Controllers\Api\V1\Classroom',
    'middleware' => ['auth:api', 'role:staff|student|admin'],
    'prefix' => 'v1/classroom'
], function($api) {
    $api->get('/', ['as' => 'classroom.index', 'uses' => 'ClassroomController@index']);
});
