<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::auth();

Route::get('/home', 'HomeController@index');

/**
 * Rotas para testar o autenticação via jwt
 */
Route::post('api/auth', 'AuthenticateController@authenticate');

Route::get('getUser', 'AuthenticateController@getAuthenticatedUser');

// Route::group(['prefix' => 'api'], function() 
Route::group(['prefix' => 'api', 'middleware' => 'jwt.auth'], function() 
{
	Route::get('user', function() 
	{
		return 'Autenticado!';
	});
});