<?php

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', [
    'namespace' => 'App\Http\Controllers\Api\V1\Student',
    'middleware' => ['auth:api', 'role:admin|laboran'],
    'prefix' => 'v1/laboran'
], function($api) {
    $api->get('/student', ['as' => 'laboran.index', 'uses' => 'LaboranController@index']);
    $api->get('/student/{id}', ['as' => 'laboran.show', 'uses' => 'LaboranController@show']);
    // $api->put('/student/{id}', ['as' => 'laboran.update', 'uses' => 'LaboranController@update']);
    $api->post('/student', ['as' => 'laboran.create', 'uses' => 'LaboranController@create']);
    $api->delete('/student/{id}', ['as' => 'laboran.delete', 'uses' => 'LaboranController@delete']);
    $api->post('/student/import', ['as' => 'laboran.import', 'uses' => 'LaboranController@import']);
    $api->post('/student/classes', ['as' => 'laboran.import', 'uses' => 'LaboranController@student_classes']);
});