<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'auth'], function () use ($router) {
    $router->post('/login', 'AuthController@postLogin');
    $router->post('/register', 'AuthController@registerAccount');

    $router->group(['middleware' => 'auth',], function () use ($router){
        $router->post('/logout', 'AuthController@logout');
        $router->get('/me', 'AuthController@me');

        //Laboran only
        $router->group(['middleware'=>'role:laboran'], function () use ($router){
            $router->get('/usersRole', 'AuthController@getAllUsersRole');

            // $router->post('/registerStudents', 'AuthController@registerStudents');
            // $router->post('/registerStaffs', 'AuthController@registerStaffs');
        });
        
    });

    $router->post('students/register', 'AuthController@registerStudents');
    $router->post('staff/register', 'AuthController@registerStaffs');
    $router->get('user/find/{id}', 'AuthController@findUser');
    $router->get('student/find/{id}', 'AuthController@findStudent');
    $router->get('staff/find/{id}', 'AuthController@findStaff');
    $router->put('student/update/{id}', 'AuthController@updateStudent');
    $router->put('staff/update/{id}', 'AuthController@updateStaff');

    
});

// $router->group(['prefix' => 'order', 'middleware' => 'auth'], function () use ($router) {
//     $router->get('/getOrders', 'OrdersController@getOrders');
// });

// $router->group(['prefix' => 'products', 'middleware' => 'auth'], function () use ($router) {
//     $router->get('/getProducts', 'ProductsController@getProducts');
// });
