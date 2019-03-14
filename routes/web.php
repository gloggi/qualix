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

Route::get('/', 'KitchenSinkController@index')->name('home');
Route::get('/user', 'KitchenSinkController@index')->name('user');

Route::get('/bloecke', 'KitchenSinkController@index')->name('bloecke');
Route::get('/tn', 'KitchenSinkController@index')->name('tn');
Route::get('/ma', 'KitchenSinkController@index')->name('ma');
Route::get('/tagesspick', 'KitchenSinkController@index')->name('tagesspick');
Route::get('/admin/kurs', 'KitchenSinkController@index')->name('admin.kurs');
Route::get('/admin/equipe', 'KitchenSinkController@index')->name('admin.equipe');
Route::get('/admin/tn', 'KitchenSinkController@index')->name('admin.tn');
Route::get('/admin/bloecke', 'KitchenSinkController@index')->name('admin.bloecke');
Route::get('/admin/ma', 'KitchenSinkController@index')->name('admin.ma');
Route::get('/admin/qk', 'KitchenSinkController@index')->name('admin.qk');
Route::get('/admin/neuerkurs', 'KitchenSinkController@index')->name('admin.neuerkurs');

Auth::routes();
