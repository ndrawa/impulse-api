<?php

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', [
    'namespace' => 'App\Http\Controllers\Api\V1\Student',
    'middleware' => ['auth:api', 'role:admin|laboran|asprak|student'],
    'prefix' => 'v1/laboran'
], function($api) {
    $api->get('/student', ['as' => 'laboran.index', 'uses' => 'LaboranController@index']);
    $api->get('/student/{id}', ['as' => 'laboran.show', 'uses' => 'LaboranController@show']);
    $api->get('/classes', ['as' => 'laboran.get_student_classes', 'uses' => 'LaboranController@get_student_classes']);
    $api->get('/role/{no_induk}', ['as' => 'laboran.get_role', 'uses' => 'LaboranController@get_role']);
    $api->post('/student', ['as' => 'laboran.create', 'uses' => 'LaboranController@create']);
    $api->delete('/student/{id}', ['as' => 'laboran.delete', 'uses' => 'LaboranController@delete']);
    $api->post('/student/import', ['as' => 'laboran.import', 'uses' => 'LaboranController@import']);
    $api->post('/student/classes', ['as' => 'laboran.student_classes', 'uses' => 'LaboranController@student_classes']);
    $api->put('/student/classes/{id}', ['as' => 'laboran.edit_student_classes', 'uses' => 'LaboranController@edit_student_classes']);
    $api->delete('/classes/{id}', ['as' => 'laboran.delete_student_classes', 'uses' => 'LaboranController@delete_student_classes']);
    $api->post('/role', ['as' => 'laboran.set_role', 'uses' => 'LaboranController@set_role']);
    $api->delete('/deleteall/{table}', ['as' => 'laboran.deleteall', 'uses' => 'LaboranController@deleteall']);
    $api->get('/roles/{role}', ['as' => 'laboran.report_roles', 'uses' => 'LaboranController@report_roles']);
    // $api->put('/student/{id}', ['as' => 'laboran.update', 'uses' => 'LaboranController@update']);
    $api->get('/bap/{schedule_id}', ['as' => 'laboran.show_bap', 'uses' => 'LaboranController@show_bap']);
    $api->post('/bap/{schedule_id}', ['as' => 'laboran.set_bap', 'uses' => 'LaboranController@set_bap']);
});

