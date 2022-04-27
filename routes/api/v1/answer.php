<?php

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', [
    'namespace' => 'App\Http\Controllers\Api\V1',
    'middleware' => ['auth:api', 'role:admin|laboran|asprak|student|aslab'],
    'prefix' => 'v1/answer'
], function($api) {
    $api->post('/StoreEssay', ['as' => 'answer.store_essay', 'uses' => 'AnswerController@StoreEssayAnswer']);
    $api->post('/StoreMultipleChoice', ['as' => 'answer.store_multiple_choice', 'uses' => 'AnswerController@StoreMultipleChoiceAnswer']);
    $api->put('/updateEssayAnswer', ['as' => 'answer.update_essay', 'uses' => 'AnswerController@updateEssayAnswer']);
    $api->put('/updateMultipleChoiceAnswer', ['as' => 'answer.update_multiple_choice', 'uses' => 'AnswerController@updateMultipleChoiceAnswer']);
    $api->get('/essayAnswer/{student_id}/{test_id}', ['as' => 'answer.getEssayAnswer', 'uses' => 'AnswerController@getStudentEssayAnswer']);
    $api->get('/multipleChoiceAnswer/{student_id}/{test_id}', ['as' => 'answer.getStudentMultipleChoiceAnswer', 'uses' => 'AnswerController@getStudentMultipleChoiceAnswer']);
    $api->get('/answer-grade/{student_id}/{test_id}', ['as' => 'answer.getAnswerGrade', 'uses' => 'AnswerController@getAnswerGrade']);
    $api->delete('/student/{student_id}/{test_id}', ['as' => 'answer.asprakResetStudentTest', 'uses' => 'AnswerController@asprakResetStudentTest']);
});
