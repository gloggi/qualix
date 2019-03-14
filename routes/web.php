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

Route::get('/', 'KitchenSinkController@index')->name('home')->middleware('auth');
Route::get('/user', 'KitchenSinkController@index')->name('user')->middleware('auth');

Route::get('/bloecke', 'KitchenSinkController@index')->name('bloecke')->middleware('auth');
Route::get('/tn', 'KitchenSinkController@index')->name('tn')->middleware('auth');
Route::get('/ma', 'KitchenSinkController@index')->name('ma')->middleware('auth');
Route::get('/tagesspick', 'KitchenSinkController@index')->name('tagesspick')->middleware('auth');
Route::get('/admin/kurs', 'KitchenSinkController@index')->name('admin.kurs')->middleware('auth');
Route::get('/admin/equipe', 'KitchenSinkController@index')->name('admin.equipe')->middleware('auth');
Route::get('/admin/tn', 'KitchenSinkController@index')->name('admin.tn')->middleware('auth');
Route::get('/admin/bloecke', 'KitchenSinkController@index')->name('admin.bloecke')->middleware('auth');
Route::get('/admin/ma', 'KitchenSinkController@index')->name('admin.ma')->middleware('auth');
Route::get('/admin/qk', 'KitchenSinkController@index')->name('admin.qk')->middleware('auth');
Route::get('/admin/neuerkurs', 'KitchenSinkController@index')->name('admin.neuerkurs')->middleware('auth');

Auth::routes();
