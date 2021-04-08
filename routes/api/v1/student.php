<?php

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', [
    'namespace' => 'App\Http\Controllers\Api\V1\Student',
    'middleware' => ['auth:api', 'role:student|admin'],
    'prefix' => 'v1/student'
], function($api) {
    
});