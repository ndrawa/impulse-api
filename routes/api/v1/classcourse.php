<?php

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', [
    'namespace' => 'App\Http\Controllers\Api\V1\ClassCourse',
    'middleware' => ['auth:api', 'role:admin|laboran|asprak|student|aslab'],
    'prefix' => 'v1/classcourse'
], function($api) {
    $api->get('/asprak', ['as' => 'laboran.get_asprak_class_course', 'uses' => 'ClassCourseController@get_asprak_class_course']);
    $api->post('/asprak', ['as' => 'laboran.set_asprak_class_course', 'uses' => 'ClassCourseController@set_asprak_class_course']);
    $api->get('/asprak/{id}', ['as' => 'laboran.get_asprak_class_course_by_id', 'uses' => 'ClassCourseController@get_asprak_class_course_by_id']);
    $api->delete('/asprak/{id}', ['as' => 'laboran.delete_asprak_class_course', 'uses' => 'ClassCourseController@delete_asprak_class_course']);
    $api->get('/recap/{class_course_id}', ['as' => 'classcourse.showRecapPresence', 'uses' => 'ClassCourseController@showRecapPresence']);
    $api->get('/export_recap/{course_id}', ['as' => 'classcourse.export_recap', 'uses' => 'ClassCourseController@export_recap']);
    $api->get('/dropdown/classcoursestaffyear', ['as' => 'laboran.get_class_course_staff_year', 'uses' => 'ClassCourseController@get_class_course_staff_year']);
    $api->post('/', ['as' => 'laboran.create_class_course', 'uses' => 'ClassCourseController@create_class_course']);
    $api->get('/', ['as' => 'laboran.get_class_course', 'uses' => 'ClassCourseController@get_class_course']);
    $api->get('/{class_course_id}', ['as' => 'laboran.get_class_course_by_id', 'uses' => 'ClassCourseController@get_class_course_by_id']);
    $api->delete('/{class_course_id}', ['as' => 'laboran.delete_class_course_by_id', 'uses' => 'ClassCourseController@delete_class_course_by_id']);

    $api->post('/asprak/praktikan', ['as' => 'classcourse.select_asprak', 'uses' => 'ClassCourseController@select_asprak']);
    $api->post('/asprak/plotting', ['as' => 'classcourse.get_plotting_asprak', 'uses' => 'ClassCourseController@get_plotting_asprak']);
    $api->post('/asprak/plotting/{id}', ['as' => 'classcourse.edit_plotting_asprak', 'uses' => 'ClassCourseController@edit_plotting_asprak']);

});
