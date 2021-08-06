<?php

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', [
    'namespace' => 'App\Http\Controllers\Api\V1',
    'middleware' => ['auth:api', 'role:staff|student|admin'],
    'prefix' => 'v1/academicYear'
], function($api) {
    $api->get('/', ['as' => 'academicYear.index', 'uses' => 'AcademicYearController@index']);
    $api->get('/{id}', ['as' => 'academicYear.show', 'uses' => 'AcademicYearController@show']);
    $api->put('/{id}', ['as' => 'academicYear.update', 'uses' => 'AcademicYearController@update']);
    $api->post('/', ['as' => 'academicYear.create', 'uses' => 'AcademicYearController@create']);
    $api->delete('/{id}', ['as' => 'academicYear.delete', 'uses' => 'AcademicYearController@delete']);
    // $api->get('/show_student_grade/{user_id}', ['as' => 'student.show', 'uses' => 'StudentController@show_student_grade']);
});
