<?php

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', [
    'namespace' => 'App\Http\Controllers\Api\V1\Module',
    'middleware' => ['auth:api', 'role:staff|student|laboran'],
    'prefix' => 'v1/module'
], function($api) {
    $api->get('/{course_id}/{academic_year_id}', ['as' => 'module.percourse', 'uses' => 'ModuleController@getCourseModule']);
    $api->get('/{course_id}/{academic_year_id}/{index}', ['as' => 'module.getmodule', 'uses' => 'ModuleController@getModule']);
});