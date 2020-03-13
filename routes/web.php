<?php

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('register', function () {
    return redirect('/login');
});

Auth::routes(['register' => false]);

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/albums', 'PhotoController@index')->middleware('auth')->name('albums');
Route::post('/file-upload', 'PhotoController@store')->middleware('auth');
Route::get('/show/{tag}', 'PhotoController@show')->middleware('auth')->name('show');
Route::get('/loadPhotos/{tag}', 'PhotoController@loadPhotos')->middleware('auth')->name('loadmore');
Route::get('/getAllPhotos/{tag}', 'PhotoController@getAllPhotos')->middleware('auth')->name('getall');
