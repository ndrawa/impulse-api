<?php

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', [
    'namespace' => 'App\Http\Controllers\Api\V1\Student',
    'middleware' => ['auth:api', 'role:admin|laboran'],
    'prefix' => 'v1/laboran'
], function($api) {
    $api->get('/student', ['as' => 'laboran.index', 'uses' => 'LaboranController@index']);
    $api->get('/student/{id}', ['as' => 'laboran.show', 'uses' => 'LaboranController@show']);
    $api->get('/classes', ['as' => 'laboran.get_student_classes', 'uses' => 'LaboranController@get_student_classes']);
    $api->get('/student/role/{no_induk}', ['as' => 'laboran.get_role', 'uses' => 'LaboranController@get_role']);
    $api->post('/student', ['as' => 'laboran.create', 'uses' => 'LaboranController@create']);
    $api->delete('/student/{id}', ['as' => 'laboran.delete', 'uses' => 'LaboranController@delete']);
    $api->post('/student/import', ['as' => 'laboran.import', 'uses' => 'LaboranController@import']);
    $api->post('/student/classes', ['as' => 'laboran.classes', 'uses' => 'LaboranController@student_classes']);
    $api->post('/student/role', ['as' => 'laboran.set_role', 'uses' => 'LaboranController@set_role']);
    // $api->put('/student/{id}', ['as' => 'laboran.update', 'uses' => 'LaboranController@update']);
});