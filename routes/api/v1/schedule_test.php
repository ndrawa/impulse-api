<?php

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', [
    'namespace' => 'App\Http\Controllers\Api\V1\Schedule',
    'middleware' => ['auth:api', 'role:staff|student|laboran'],
    'prefix' => 'v1/schedule_test'
], function($api) {
    $api->post('/', ['as' => 'schedule_test.create', 'uses' => 'ScheduleTestController@create']);
    $api->get('/{id}', ['as' => 'schedule_test.getById', 'uses' => 'ScheduleTestController@get']);
    $api->put('/{id}', ['as' => 'schedule_test.update', 'uses' => 'ScheduleTestController@update']);
    $api->get('/{schedule_id}/{test_id}', ['as' => 'schedule_test.getScheduleTest', 'uses' => 'ScheduleTestController@getScheduleTest']);

});
