<?php
/**
 *
 * Routes file, this file must contain all the routes available for the application
 *  The base namespace for controllers is 'App\Controllers' if the controller is located
 *  in a deeper package please specify i.e.: Home/IndexController @ execute
 */

use App\Lib\Routing\Routes;

Routes::get('/','IndexController@sayHi');

Routes::get('/api/v1/users','UsersController@index');

Routes::get('/api/v1/users/{user_id}','UsersController@show');

Routes::post('/api/v1/users','UsersController@store');

Routes::put('/api/v1/users/{user_id}','UsersController@update');

Routes::delete('/api/v1/users/{user_id}','UsersController@destroy');

//Routes::get('/api/v1/phones','PhoneController@index');
//
//Routes::get('/api/v1/phones/{phone_id}','PhonesController@show');
//
//Routes::post('/api/v1/phones','PhonesController@store');
//
//Routes::put('/api/v1/phones/{phone_id}','PhonesController@update');
//
//Routes::patch('/api/v1/phones/{phone_id}','PhonesController@patchUpdate');




