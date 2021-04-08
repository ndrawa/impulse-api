<?php

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', [
    'namespace' => 'App\Http\Controllers\Api\V1\Asprak',
    'middleware' => ['auth:api', 'role:asprak|admin'],
    'prefix' => 'v1/asprak'
], function($api) {
    
});