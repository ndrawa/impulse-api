<?php

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', [
    'namespace' => 'App\Http\Controllers\Api\V1\Student',
    'middleware' => ['auth:api', 'role:admin|laboran|asprak|student|aslab'],
    'prefix' => 'v1/ticket'
], function($api) {
   
    $api->get('/', ['as' => 'student.ticket.index', 'uses' => 'StudentController@index_ticket']);
    $api->get('/{nim}', ['as' => 'student.ticket.show', 'uses' => 'StudentController@show_ticket']);
    $api->put('/{id}', ['as' => 'student.ticket.update', 'uses' => 'StudentController@update_ticket']);
    $api->post('/', ['as' => 'student.ticket.create', 'uses' => 'StudentController@create_ticket']);
    $api->delete('/{id}', ['as' => 'student.ticket.delete', 'uses' => 'StudentController@delete_ticket']);
    $api->post('/download/{ticket_id}', ['as' => 'student.ticket.download', 'uses' => 'StudentController@download']);
});
