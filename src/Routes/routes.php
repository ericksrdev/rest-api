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


