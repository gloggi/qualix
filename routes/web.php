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

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/', 'KitchenSinkController@index')->name('home');
    Route::post('/', 'CourseController@select')->name('admin.kurs.select');
    Route::get('/user', 'KitchenSinkController@index')->name('user');

    Route::get('/bloecke', 'KitchenSinkController@index')->name('bloecke');
    Route::get('/tn', 'KitchenSinkController@index')->name('tn');
    Route::get('/ma', 'KitchenSinkController@index')->name('ma');
    Route::get('/tagesspick', 'KitchenSinkController@index')->name('tagesspick');
    Route::get('/admin/kurs', 'CourseController@edit')->name('admin.kurs');
    Route::post('/admin/kurs', 'CourseController@update')->name('admin.kurs.update');
    Route::get('/admin/equipe', 'KitchenSinkController@index')->name('admin.equipe');
    Route::get('/admin/tn', 'KitchenSinkController@index')->name('admin.tn');
    Route::get('/admin/bloecke', 'KitchenSinkController@index')->name('admin.bloecke');
    Route::get('/admin/ma', 'KitchenSinkController@index')->name('admin.ma');
    Route::get('/admin/qk', 'QKController@index')->name('admin.qk');
    Route::post('/admin/qk', 'QKController@store')->name('admin.qk.store');
    Route::delete('/admin/qk/{id}', 'QKController@destroy')->name('admin.qk.delete');
    Route::get('/admin/neuerkurs', 'CourseController@create')->name('admin.neuerkurs');
    Route::post('/admin/neuerkurs', 'CourseController@store')->name('admin.neuerkurs.store');

});

Auth::routes(['verify' => true]);
