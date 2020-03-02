<?php
/**
 *
 * Routes file, this file must contain all the routes available for the application
 *  The base namespace for controllers is 'App\Controllers' if the controller is located
 *  in a deeper package please specify i.e.: Home/IndexController @ execute
 */

use App\Lib\Routing\Routes;

Routes::get('/','IndexController@sayHi');
/**
 *  Users Routes
 */

Routes::get('/api/v1/users','UsersController@index');

Routes::get('/api/v1/users/{user_id}','UsersController@show');

Routes::post('/api/v1/users','UsersController@store');

Routes::put('/api/v1/users/{user_id}','UsersController@update');

Routes::delete('/api/v1/users/{user_id}','UsersController@destroy');

/**
 * User's phones
 */

Routes::post('/api/v1/users/{user_id}/phones','PhonesController@store');

/**
 * User's emails
 */


Routes::post('/api/v1/users/{user_id}/emails','EmailsController@store');

/**
 * Phones Routes
 */

Routes::get('/api/v1/phones','PhonesController@index');

Routes::get('/api/v1/phones/{phone_id}','PhonesController@show');

Routes::put('/api/v1/phones/{phone_id}','PhonesController@update');

Routes::delete('/api/v1/phones/{phone_id}','PhonesController@destroy');

/**
 * Emails Routes
 */

Routes::get('/api/v1/emails','EmailsController@index');

Routes::get('/api/v1/emails/{email_id}','EmailsController@show');

Routes::put('/api/v1/emails/{email_id}','EmailsController@update');

Routes::delete('/api/v1/emails/{email_id}','EmailsController@destroy');




