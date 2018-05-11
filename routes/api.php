<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['middleware' => 'jwt.auth'], function(){
  Route::get('auth/user', 'AuthController@user');
  Route::post('auth/logout', 'Api\Auth\AuthController@logout');
  Route::post('auth/refresh', 'Api\Auth\AuthController@refresh');

  Route::resource('user', 'Api\User\UserController', ['except' => ['edit', 'create']]);
  Route::resource('role', 'Api\Role\RoleController', ['except' => ['edit', 'create']]);
  Route::resource('membership', 'Api\Membership\MembershipController', ['except' => ['edit', 'create']]);
});

//routes without jwt
Route::post('login',  'Api\Auth\AuthController@login');
Route::post('signup', 'Api\Auth\AuthController@signup');
