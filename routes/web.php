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
    Route::get('/user', 'HomeController@editUser')->name('user');
    Route::post('/user', 'HomeController@updateUser')->name('user.update');

    Route::get('/kurs/{course}', 'HomeController@index')->name('index');

    Route::get('/kurs/{course}/bloecke', 'BlockListController@index')->name('blocks');
    Route::get('/kurs/{course}/tn', 'ParticipantListController@index')->name('tn');
    Route::get('/kurs/{course}/tn/{participant}', 'ParticipantDetailController@index')->name('tn.detail');

    Route::get('/kurs/{course}/ueberblick', 'ObservationController@overview')->name('overview');

    Route::get('/kurs/{course}/beobachtungen/neu', 'ObservationController@create')->name('observation.new');
    Route::post('/kurs/{course}/beobachtungen/neu', 'ObservationController@store')->name('observation.store');
    Route::get('/kurs/{course}/beobachtungen/{observation}', 'ObservationController@edit')->name('observation.edit');
    Route::post('/kurs/{course}/beobachtungen/{observation}', 'ObservationController@update')->name('observation.update');
    Route::delete('/kurs/{course}/beobachtungen/{observation}', 'ObservationController@destroy')->name('observation.delete');

    Route::get('/kurs/{course}/admin', 'CourseController@edit')->name('admin.course');
    Route::post('/kurs/{course}/admin', 'CourseController@update')->name('admin.course.update');

    Route::get('/kurs/{course}/admin/equipe', 'EquipeController@index')->name('admin.equipe');
    Route::delete('/kurs/{course}/admin/equipe/{user}', 'EquipeController@destroy')->name('admin.equipe.delete');

    Route::post('/kurs/{course}/admin/invitation', 'InvitationController@store')->name('admin.invitation.store');
    Route::delete('/kurs/{course}/admin/invitation/{email}', 'InvitationController@destroy')->name('admin.invitation.delete');
    Route::get('/invitation/{token}', 'InvitationController@index')->name('invitation.view');
    Route::post('/invitation', 'InvitationController@claim')->name('invitation.claim');

    Route::get('/kurs/{course}/admin/tn', 'ParticipantController@index')->name('admin.participants');
    Route::post('/kurs/{course}/admin/tn', 'ParticipantController@store')->name('admin.participants.store');
    Route::get('/kurs/{course}/admin/tn/{participant}', 'ParticipantController@edit')->name('admin.participants.edit');
    Route::post('/kurs/{course}/admin/tn/{participant}', 'ParticipantController@update')->name('admin.participants.update');
    Route::delete('/kurs/{course}/admin/tn/{participant}', 'ParticipantController@destroy')->name('admin.participants.delete');

    Route::get('/kurs/{course}/admin/bloecke', 'BlockController@index')->name('admin.blocks');
    Route::post('/kurs/{course}/admin/bloecke', 'BlockController@store')->name('admin.block.store');
    Route::get('/kurs/{course}/admin/bloecke/{block}', 'BlockController@edit')->name('admin.block.edit');
    Route::post('/kurs/{course}/admin/bloecke/{block}', 'BlockController@update')->name('admin.block.update');
    Route::delete('/kurs/{course}/admin/bloecke/{block}', 'BlockController@destroy')->name('admin.block.delete');

    Route::get('/kurs/{course}/admin/ma', 'RequirementController@index')->name('admin.requirements');
    Route::post('/kurs/{course}/admin/ma', 'RequirementController@store')->name('admin.requirements.store');
    Route::get('/kurs/{course}/admin/ma/{requirement}', 'RequirementController@edit')->name('admin.requirements.edit');
    Route::post('/kurs/{course}/admin/ma/{requirement}', 'RequirementController@update')->name('admin.requirements.update');
    Route::delete('/kurs/{course}/admin/ma/{requirement}', 'RequirementController@destroy')->name('admin.requirements.delete');

    Route::get('/kurs/{course}/admin/qk', 'CategoryController@index')->name('admin.categories');
    Route::post('/kurs/{course}/admin/qk', 'CategoryController@store')->name('admin.categories.store');
    Route::get('/kurs/{course}/admin/qk/{category}', 'CategoryController@edit')->name('admin.categories.edit');
    Route::post('/kurs/{course}/admin/qk/{category}', 'CategoryController@update')->name('admin.categories.update');
    Route::delete('/kurs/{course}/admin/qk/{category}', 'CategoryController@destroy')->name('admin.categories.delete');

    Route::get('/neuerkurs', 'CourseController@create')->name('admin.neuerkurs');
    Route::post('/neuerkurs', 'CourseController@store')->name('admin.neuerkurs.store');
});

Auth::routes(['verify' => true]);
