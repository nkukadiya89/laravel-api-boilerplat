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

$exceptMethods = ['except' => ['create', 'edit']];

Route::prefix('v1')->name('api.')->group(function () use ($exceptMethods) {
    // Login as user
    Route::post('/login', 'AuthenticateController@login')->name('auth.login');
    Route::post('/register', 'AuthenticateController@register')->name('auth.register');

    //Forgot Password
    Route::post('/forgot-password', 'AuthenticateController@forgotPassword')->name('password.forgot');

    // Temp
    Route::get('/user', 'UserController@indexUser')->name('user.index');

    Route::group(['middleware' => ['auth:api', 'jwt.auth']], function () {
        // Logout
        Route::post('/logout', 'AuthenticateController@logout')->name('auth.logout');
        //Change Password
        Route::post('/change-password', 'AuthenticateController@changePassword')->name('password.change');
        // User Profile
        Route::get('/profile', 'UserController@showUser')->name('profile.show');
        Route::post('/profile', 'UserController@updateUser')->name('profile.update');
    });
});
