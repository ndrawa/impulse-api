<?php

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', [
    'namespace' => 'App\Http\Controllers\Api\V1\Asprak',
    'middleware' => ['auth:api', 'role:admin|laboran|asprak|student|aslab'],
    'prefix' => 'v1/asprak'
], function($api) {
    
});