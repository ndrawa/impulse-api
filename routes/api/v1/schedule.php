<?php

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', [
    'namespace' => 'App\Http\Controllers\Api\V1\Schedule',
    'middleware' => ['auth:api', 'role:admin|laboran|asprak|student|aslab'],
    'prefix' => 'v1/schedule'
], function($api) {
    $api->get('/', ['as' => 'schedule.index', 'uses' => 'ScheduleController@index']);
    $api->get('/simple', ['as' => 'schedule.index', 'uses' => 'ScheduleController@index_simple']);
    $api->post('/', ['as' => 'schedule.create', 'uses' => 'ScheduleController@create']);
    $api->post('/import', ['as' => 'staff.import', 'uses' => 'ScheduleController@import']);
    //Test
    $api->get('/{id}', ['as' => 'schedule.show', 'uses' => 'ScheduleController@show']);
    $api->get('/get-test/{id}', ['as' => 'schedule.gettest', 'uses' => 'ScheduleController@getTest']);
    $api->post('/create-test', ['as' => 'schedule.createtest', 'uses' => 'ScheduleController@create_test']);
    $api->put('/update-question/{id}', ['as' => 'schedule.updatequestion', 'uses' => 'ScheduleController@update_question']);
    $api->delete('/delete-question/{id}', ['as' => 'schedule.delete_question', 'uses' => 'ScheduleController@delete_question']);
    $api->put('/update-answer/{id}', ['as' => 'schedule.update_answer', 'uses' => 'ScheduleController@update_answer']);
    $api->delete('/delete-answer/{id}', ['as' => 'schedule.delete_answer', 'uses' => 'ScheduleController@delete_answer']);
    $api->delete('/delete-test/{id}', ['as' => 'schedule.deletetest', 'uses' => 'ScheduleController@delete_test']);
    $api->put('/update-test/{id}', ['as' => 'schedule.updatetest', 'uses' => 'ScheduleController@update_test']);
    $api->get('/getstudentcourse/{student_id}', ['as' => 'schedule.getstudentcourse', 'uses' => 'ScheduleController@get_student_class_course']);
    $api->get('/show_schedule/{class_course_id}', ['as' => 'schedule.getstudentcourse', 'uses' => 'ScheduleController@show_schedule']);
    $api->put('/{id}', ['as' => 'schedule.update', 'uses' => 'ScheduleController@update']);
});
