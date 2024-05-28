<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', 'App\Http\Controllers\ContestsController@index')->name('top');

Route::resource('contests', 'App\Http\Controllers\ContestsController', ['only' => ['create', 'store', 'show', 'update', 'destroy']]);
// Route::resource('users', 'App\Http\Controllers\AtCoderUsersController', ['only' => ['index', 'store', 'show']]);
Route::get('users/{user_id}', 'App\Http\Controllers\AtCoderUsersController@show')->name('users.show');

// GitHub の認証後に戻るためのルーティング
Route::get('social-auth/{provider}/callback', 'App\Http\Controllers\Auth\SocialLoginController@providerCallback');
// GitHub の認証ベージに遷移するためのルーティング
Route::get('social-auth/{provider}', 'App\Http\Controllers\Auth\SocialLoginController@redirectToProvider')->name('social.redirect');


// ログアウト用
Route::post('logout', 'App\Http\Controllers\Auth\LoginController@logout')->name('logout');
