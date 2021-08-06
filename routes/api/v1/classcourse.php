<?php

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', [
    'namespace' => 'App\Http\Controllers\Api\V1\ClassCourse',
    'middleware' => ['auth:api', 'role:admin|laboran|asprak|aslab'],
    'prefix' => 'v1/classcourse'
], function($api) {
    $api->get('/asprak', ['as' => 'laboran.get_asprak_class_course', 'uses' => 'ClassCourseController@get_asprak_class_course']);
    $api->post('/asprak', ['as' => 'laboran.set_asprak_class_course', 'uses' => 'ClassCourseController@set_asprak_class_course']);
    $api->post('/asprak/filter', ['as' => 'laboran.filter_asprak_class_course', 'uses' => 'ClassCourseController@filter_asprak_class_course']);
    $api->get('/asprak/{id}', ['as' => 'laboran.get_asprak_class_course_by_id', 'uses' => 'ClassCourseController@get_asprak_class_course_by_id']);
    $api->delete('/asprak/{id}', ['as' => 'laboran.delete_asprak_class_course', 'uses' => 'ClassCourseController@delete_asprak_class_course']);

    $api->get('/dropdown/classcoursestaffyear', ['as' => 'laboran.get_class_course_staff_year', 'uses' => 'ClassCourseController@get_class_course_staff_year']);
    $api->post('/', ['as' => 'laboran.create_class_course', 'uses' => 'ClassCourseController@create_class_course']);
    $api->get('/', ['as' => 'laboran.get_class_course', 'uses' => 'ClassCourseController@get_class_course']);
    $api->get('/{class_course_id}', ['as' => 'laboran.get_class_course_by_id', 'uses' => 'ClassCourseController@get_class_course_by_id']);
    $api->delete('/{class_course_id}', ['as' => 'laboran.delete_class_course_by_id', 'uses' => 'ClassCourseController@delete_class_course_by_id']);


});

