<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('lang/{lang}', 'LocalizationController@translate')->name('localization');
Auth::routes(['register' => false]);
Route::get('/', 'HomeController@index')->name('home');
Route::resource('users', 'UserController');
Route::get('user-deleted', 'UserController@deleted')->name('users.deleted');
Route::get('user-restore/{id}', 'UserController@restore')->name('users.restore');
Route::delete('user-force-delete/{id}', 'UserController@forceDelete')->name('users.forceDelete');
Route::get('change-password', 'PasswordController@editPassword')->name('change_password');
Route::patch('change-password', 'PasswordController@updatePassword')->name('update_password');
Route::post('import', 'UserController@import')->name('users.import');
