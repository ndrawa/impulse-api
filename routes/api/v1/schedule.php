<?php

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', [
    'namespace' => 'App\Http\Controllers\Api\V1\Schedule',
    'middleware' => ['auth:api', 'role:staff|student|laboran'],
    'prefix' => 'v1/schedule'
], function($api) {
    $api->get('/', ['as' => 'schedule.index', 'uses' => 'ScheduleController@index']);
    $api->post('/', ['as' => 'schedule.create', 'uses' => 'ScheduleController@create']);
    $api->post('/import', ['as' => 'staff.import', 'uses' => 'ScheduleController@import']);
    //Test
    $api->post('/get-test', ['as' => 'schedule.gettest', 'uses' => 'ScheduleController@getTest']);
    $api->post('/create-test', ['as' => 'schedule.createtest', 'uses' => 'ScheduleController@create_test']);
    $api->delete('/delete-test/{id}', ['as' => 'schedule.deletetest', 'uses' => 'ScheduleController@delete_test']);
    $api->put('/update-test/{id}', ['as' => 'schedule.updatetest', 'uses' => 'ScheduleController@update_test']);
});
