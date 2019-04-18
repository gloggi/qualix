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

    Route::get('/', 'CourseController@noCourse')->name('home');
    Route::get('/kurs', 'CourseController@noCourse');
    Route::get('/user', 'KitchenSinkController@index')->name('user');
    Route::get('/kurs/{kurs}', 'KitchenSinkController@index')->name('index');

    Route::get('/kurs/{kurs}/bloecke', 'KitchenSinkController@index')->name('bloecke');
    Route::get('/kurs/{kurs}/tn', 'KitchenSinkController@index')->name('tn');
    Route::get('/kurs/{kurs}/ma', 'KitchenSinkController@index')->name('ma');
    Route::get('/kurs/{kurs}/tagesspick', 'KitchenSinkController@index')->name('tagesspick');
    Route::get('/kurs/{kurs}/admin', 'CourseController@edit')->name('admin.kurs');
    Route::post('/kurs/{kurs}/admin', 'CourseController@update')->name('admin.kurs.update');

    Route::get('/kurs/{kurs}/admin/equipe', 'EquipeController@index')->name('admin.equipe');
    Route::delete('/kurs/{kurs}/admin/equipe/{user}', 'EquipeController@destroy')->name('admin.equipe.delete');

    Route::post('/kurs/{kurs}/admin/invitation', 'InvitationController@store')->name('admin.invitation.store');
    Route::delete('/kurs/{kurs}/admin/invitation/{email}', 'InvitationController@destroy')->name('admin.invitation.delete');
    Route::get('/invitation/{token}', 'InvitationController@index')->name('invitation.view');
    Route::post('/invitation', 'InvitationController@claim')->name('invitation.claim');

    Route::get('/kurs/{kurs}/admin/tn', 'TNController@index')->name('admin.tn');
    Route::post('/kurs/{kurs}/admin/tn', 'TNController@store')->name('admin.tn.store');
    Route::get('/kurs/{kurs}/admin/tn/{tn}', 'TNController@edit')->name('admin.tn.edit');
    Route::post('/kurs/{kurs}/admin/tn/{tn}', 'TNController@update')->name('admin.tn.update');
    Route::delete('/kurs/{kurs}/admin/tn/{tn}', 'TNController@destroy')->name('admin.tn.delete');

    Route::get('/kurs/{kurs}/admin/bloecke', 'BlockController@index')->name('admin.bloecke');
    Route::post('/kurs/{kurs}/admin/bloecke', 'BlockController@store')->name('admin.block.store');
    Route::get('/kurs/{kurs}/admin/bloecke/{block}', 'BlockController@edit')->name('admin.block.edit');
    Route::post('/kurs/{kurs}/admin/bloecke/{block}', 'BlockController@update')->name('admin.block.update');
    Route::delete('/kurs/{kurs}/admin/bloecke/{block}', 'BlockController@destroy')->name('admin.block.delete');

    Route::get('/kurs/{kurs}/admin/ma', 'MAController@index')->name('admin.ma');
    Route::post('/kurs/{kurs}/admin/ma', 'MAController@store')->name('admin.ma.store');
    Route::get('/kurs/{kurs}/admin/ma/{ma}', 'MAController@edit')->name('admin.ma.edit');
    Route::post('/kurs/{kurs}/admin/ma/{ma}', 'MAController@update')->name('admin.ma.update');
    Route::delete('/kurs/{kurs}/admin/ma/{ma}', 'MAController@destroy')->name('admin.ma.delete');

    Route::get('/kurs/{kurs}/admin/qk', 'QKController@index')->name('admin.qk');
    Route::post('/kurs/{kurs}/admin/qk', 'QKController@store')->name('admin.qk.store');
    Route::get('/kurs/{kurs}/admin/qk/{qk}', 'QKController@edit')->name('admin.qk.edit');
    Route::post('/kurs/{kurs}/admin/qk/{qk}', 'QKController@update')->name('admin.qk.update');
    Route::delete('/kurs/{kurs}/admin/qk/{qk}', 'QKController@destroy')->name('admin.qk.delete');

    Route::get('/neuerkurs', 'CourseController@create')->name('admin.neuerkurs');
    Route::post('/neuerkurs', 'CourseController@store')->name('admin.neuerkurs.store');
});

Auth::routes(['verify' => true]);
