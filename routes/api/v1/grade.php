<?php

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', [
    'namespace' => 'App\Http\Controllers\Api\V1',
    'middleware' => ['auth:api', 'role:admin|laboran|asprak|student|aslab'],
    'prefix' => 'v1/grade'
], function($api) {
    $api->get('/me', ['as' => 'course.me_grade', 'uses' => 'GradeController@getMeGrades']);
    $api->get('/test/{student_id}/{test_id}', ['as' => 'grade.me_grade', 'uses' => 'GradeController@getStudentTestGrade']);
    $api->get('/all/{student_id}/{course_id?}', ['as' => 'grade.getStudentGrades', 'uses' => 'GradeController@getStudentGrades']);
    $api->get('/schedule/{schedule_id}', ['as' => 'grade.getScheduleGrade', 'uses' => 'GradeController@getScheduleGrade']);
    $api->put('/student/{student_id}', ['as' => 'grade.asprakUpdateGrade', 'uses' => 'GradeController@asprakUpdateGrade']);

});
