<?php

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', [
    'namespace' => 'App\Http\Controllers\Api\V1',
    'middleware' => ['auth:api', 'role:student|asprak|aslab|laboran'],
    'prefix' => 'v1/answer'
], function($api) {
    $api->post('/StoreEssay', ['as' => 'answer.store_essay', 'uses' => 'AnswerController@StoreEssayAnswer']);
    $api->post('/StoreMultipleChoice', ['as' => 'answer.store_multiple_choice', 'uses' => 'AnswerController@StoreMultipleChoiceAnswer']);
    $api->put('/updateEssayAnswer', ['as' => 'answer.update_essay', 'uses' => 'AnswerController@updateEssayAnswer']);
    $api->put('/updateMultipleChoiceAnswer', ['as' => 'answer.update_multiple_choice', 'uses' => 'AnswerController@updateMultipleChoiceAnswer']);
    $api->get('/getEssayAnswer/{test_id}/{user_id}', ['as' => 'answer.getEssayAnswer', 'uses' => 'AnswerController@getStudentEssayAnswer']);
    $api->get('/getMultipleChoiceAnswer/{test_id}/{user_id}', ['as' => 'answer.getStudentMultipleChoiceAnswer', 'uses' => 'AnswerController@getStudentMultipleChoiceAnswer']);
    $api->get('/getMultipleChoiceGrade/{test_id}/{user_id}', ['as' => 'answer.getMultipleChoiceGrade', 'uses' => 'AnswerController@getMultipleChoiceGrade']);
});
