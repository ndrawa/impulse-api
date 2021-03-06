<?php

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', [
    'namespace' => 'App\Http\Controllers\Api\V1\Course',
    'middleware' => ['auth:api', 'role:admin|laboran|asprak|student|aslab'],
    'prefix' => 'v1/course'
], function($api) {
    $api->get('/', ['as' => 'course.index', 'uses' => 'CourseController@index']);
    $api->get('/getall', ['as' => 'course.getall', 'uses' => 'CourseController@getall']);
    $api->post('/', ['as' => 'course.create', 'uses' => 'CourseController@create']);
    $api->put('/{id}', ['as' => 'course.update', 'uses' => 'CourseController@update']);
    $api->delete('/{id}', ['as' => 'course.delete', 'uses' => 'CourseController@delete']);
});
