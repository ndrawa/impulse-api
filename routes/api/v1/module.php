<?php

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', [
    'namespace' => 'App\Http\Controllers\Api\V1\Module',
    'middleware' => ['auth:api', 'role:admin|laboran|asprak|student|aslab'],
    'prefix' => 'v1/module'
], function($api) {
    $api->get('/{course_id}/{academic_year_id}', ['as' => 'module.percourse', 'uses' => 'ModuleController@getCourseModule']);
    $api->get('/{course_id}/{academic_year_id}/{index}', ['as' => 'module.getmodule', 'uses' => 'ModuleController@getModule']);
    $api->get('/{id}', ['as' => 'module.show', 'uses' => 'ModuleController@show']);
    $api->post('/journal/{module_id}/{journal_id}', ['as' => 'module.download', 'uses' => 'ModuleController@download']);
    $api->post('/journal/{id}', ['as' => 'module.uploadFile', 'uses' => 'ModuleController@uploadFile']);
});
