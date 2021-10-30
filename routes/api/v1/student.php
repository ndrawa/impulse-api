<?php

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', [
    'namespace' => 'App\Http\Controllers\Api\V1\Student',
    'middleware' => ['auth:api', 'role:staff|student|admin'],
    'prefix' => 'v1/student'
], function($api) {
    $api->get('/show_student_grade/{user_id}', ['as' => 'student.show', 'uses' => 'StudentController@show_student_grade']);
    $api->get('/presence', ['as' => 'student.presence', 'uses' => 'StudentController@show_me_presence']);
    $api->get('/', ['as' => 'student.index', 'uses' => 'StudentController@index']);
    $api->get('/{id}', ['as' => 'student.show', 'uses' => 'StudentController@show']);
    $api->put('/{id}', ['as' => 'student.update', 'uses' => 'StudentController@update']);
    $api->post('/', ['as' => 'student.create', 'uses' => 'StudentController@create']);
    $api->delete('/{id}', ['as' => 'student.delete', 'uses' => 'StudentController@delete']);
});
