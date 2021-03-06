<?php

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', [
    'namespace' => 'App\Http\Controllers\Api\V1\Staff',
    'middleware' => ['auth:api', 'role:admin|laboran|asprak|student|aslab'],
    'prefix' => 'v1/staff'
], function($api) {
    $api->get('/', ['as' => 'staff.index', 'uses' => 'StaffController@index']);
    $api->get('/getall', ['as' => 'staff.getall', 'uses' => 'StaffController@getall']);
    $api->get('/{id}', ['as' => 'staff.show', 'uses' => 'StaffController@show']);
    $api->put('/{id}', ['as' => 'staff.update', 'uses' => 'StaffController@update']);
    $api->post('/', ['as' => 'staff.create', 'uses' => 'StaffController@create']);
    $api->delete('/{id}', ['as' => 'staff.delete', 'uses' => 'StaffController@delete']);
    $api->post('/import', ['as' => 'staff.import', 'uses' => 'StaffController@import']);
    $api->post('/nip/{id}', ['as' => 'staff.edit_nip', 'uses' => 'StaffController@edit_nip']);
});