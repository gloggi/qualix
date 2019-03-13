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

Route::view('/', 'pages.kitchensink')->name('home');
Route::view('/login', 'pages.kitchensink')->name('login');
Route::view('/register', 'pages.kitchensink')->name('register');
Route::view('/bloecke', 'pages.kitchensink')->name('bloecke');
Route::view('/tn', 'pages.kitchensink')->name('tn');
Route::view('/ma', 'pages.kitchensink')->name('ma');
Route::view('/tagesspick', 'pages.kitchensink')->name('tagesspick');
Route::view('/admin/kurs', 'pages.kitchensink')->name('admin.kurs');
Route::view('/admin/equipe', 'pages.kitchensink')->name('admin.equipe');
Route::view('/admin/tn', 'pages.kitchensink')->name('admin.tn');
Route::view('/admin/bloecke', 'pages.kitchensink')->name('admin.bloecke');
Route::view('/admin/ma', 'pages.kitchensink')->name('admin.ma');
Route::view('/admin/qk', 'pages.kitchensink')->name('admin.qk');
Route::view('/admin/neuerkurs', 'pages.kitchensink')->name('admin.neuerkurs');
