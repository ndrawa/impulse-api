<?php

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', [
    'namespace' => 'App\Http\Controllers\Api\V1\Student',
    'middleware' => ['auth:api', 'role:student|admin'],
    'prefix' => 'v1/student'
], function($api) {
    $api->get('/', ['as' => 'student.index', 'uses' => 'StudentController@index']);
    $api->get('/{id}', ['as' => 'student.show', 'uses' => 'StudentController@show']);
    $api->put('/{id}', ['as' => 'student.update', 'uses' => 'StudentController@update']);
    $api->post('/', ['as' => 'student.create', 'uses' => 'StudentController@create']);
    $api->delete('/{id}', ['as' => 'student.delete', 'uses' => 'StudentController@delete']);
});