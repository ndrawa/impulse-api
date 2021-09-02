<?php

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', [
    'namespace' => 'App\Http\Controllers\Api\V1',
    'middleware' => ['auth:api', 'role:student|staff|asprak|aslab|laboran'],
    'prefix' => 'v1/grade'
], function($api) {
    $api->get('/me', ['as' => 'course.me_grade', 'uses' => 'GradeController@getMeGrades']);
});