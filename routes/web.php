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

Route::middleware(['auth', 'verified', 'restoreFormData'])->group(function () {

    Route::get('/', 'CourseController@noCourse')->name('home');
    Route::get('/course', 'CourseController@noCourse');
    Route::get('/user', 'HomeController@editUser')->name('user');
    Route::post('/user', 'HomeController@updateUser')->name('user.update');

    Route::get('/course/{course}', 'HomeController@index')->name('index');

    Route::get('/course/{course}/blocks', 'BlockListController@index')->name('blocks');
    Route::get('/course/{course}/crib', 'BlockListController@crib')->name('crib');

    Route::middleware('courseNotArchived')->group(function () {
        Route::get('/course/{course}/participants', 'ParticipantListController@index')->name('participants');
        Route::get('/course/{course}/participants/{participant}', 'ParticipantDetailController@index')->name('participants.detail');

        Route::get('/course/{course}/overview', 'ObservationController@overview')->name('overview');

        Route::get('/course/{course}/observation/new', 'ObservationController@create')->name('observation.new');
        Route::post('/course/{course}/observation/new', 'ObservationController@store')->name('observation.store');
        Route::get('/course/{course}/observation/{observation}', 'ObservationController@edit')->name('observation.edit');
        Route::post('/course/{course}/observation/{observation}', 'ObservationController@update')->name('observation.update');
        Route::delete('/course/{course}/observation/{observation}', 'ObservationController@destroy')->name('observation.delete');

        Route::post('/course/{course}/admin/archive', 'CourseController@archive')->name('admin.course.archive');
    });

    Route::get('/course/{course}/admin', 'CourseController@edit')->name('admin.course');
    Route::post('/course/{course}/admin', 'CourseController@update')->name('admin.course.update');
    Route::delete('/course/{course}/admin', 'CourseController@delete')->name('admin.course.delete');

    Route::get('/course/{course}/admin/equipe', 'EquipeController@index')->name('admin.equipe');
    Route::delete('/course/{course}/admin/equipe/{user}', 'EquipeController@destroy')->name('admin.equipe.delete');

    Route::post('/course/{course}/admin/invitation', 'InvitationController@store')->name('admin.invitation.store');
    Route::delete('/course/{course}/admin/invitation/{email}', 'InvitationController@destroy')->name('admin.invitation.delete');
    Route::get('/invitation/{token}', 'InvitationController@index')->name('invitation.view');
    Route::post('/invitation', 'InvitationController@claim')->name('invitation.claim');

    Route::middleware('courseNotArchived')->group(function() {
        Route::get('/course/{course}/admin/participants', 'ParticipantController@index')->name('admin.participants');
        Route::post('/course/{course}/admin/participants', 'ParticipantController@store')->name('admin.participants.store');
        Route::get('/course/{course}/admin/participants/import', 'ParticipantController@upload')->name('admin.participants.upload');
        Route::post('/course/{course}/admin/participants/import', 'ParticipantController@import')->name('admin.participants.import');
        Route::get('/course/{course}/admin/participants/{participant}', 'ParticipantController@edit')->name('admin.participants.edit');
        Route::post('/course/{course}/admin/participants/{participant}', 'ParticipantController@update')->name('admin.participants.update');
        Route::delete('/course/{course}/admin/participants/{participant}', 'ParticipantController@destroy')->name('admin.participants.delete');

        Route::resource('/course/{course}/admin/participantGroups', 'ParticipantGroupController',  ['as' => 'admin'])->except('show', 'create');

    });



    Route::get('/course/{course}/admin/blocks', 'BlockController@index')->name('admin.blocks');
    Route::post('/course/{course}/admin/blocks', 'BlockController@store')->name('admin.block.store');
    Route::get('/course/{course}/admin/blocks/import', 'BlockController@upload')->name('admin.block.upload');
    Route::post('/course/{course}/admin/blocks/import', 'BlockController@import')->name('admin.block.import');
    Route::get('/course/{course}/admin/blocks/{block}', 'BlockController@edit')->name('admin.block.edit');
    Route::post('/course/{course}/admin/blocks/{block}', 'BlockController@update')->name('admin.block.update');
    Route::delete('/course/{course}/admin/blocks/{block}', 'BlockController@destroy')->name('admin.block.delete');

    Route::get('/course/{course}/admin/requirement', 'RequirementController@index')->name('admin.requirements');
    Route::post('/course/{course}/admin/requirement', 'RequirementController@store')->name('admin.requirements.store');
    Route::get('/course/{course}/admin/requirement/{requirement}', 'RequirementController@edit')->name('admin.requirements.edit');
    Route::post('/course/{course}/admin/requirement/{requirement}', 'RequirementController@update')->name('admin.requirements.update');
    Route::delete('/course/{course}/admin/requirement/{requirement}', 'RequirementController@destroy')->name('admin.requirements.delete');

    Route::get('/course/{course}/admin/category', 'CategoryController@index')->name('admin.categories');
    Route::post('/course/{course}/admin/category', 'CategoryController@store')->name('admin.categories.store');
    Route::get('/course/{course}/admin/category/{category}', 'CategoryController@edit')->name('admin.categories.edit');
    Route::post('/course/{course}/admin/category/{category}', 'CategoryController@update')->name('admin.categories.update');
    Route::delete('/course/{course}/admin/category/{category}', 'CategoryController@destroy')->name('admin.categories.delete');

    Route::get('/newcourse', 'CourseController@create')->name('admin.newcourse');
    Route::post('/newcourse', 'CourseController@store')->name('admin.newcourse.store');
});

Auth::routes(['verify' => true]);
Route::get('login/hitobito', 'Auth\LoginController@redirectToHitobitoOAuth')->name('login.hitobito');
Route::get('login/hitobito/callback', 'Auth\LoginController@handleHitobitoOAuthCallback')->name('login.hitobito.callback');
Route::get('locale/{locale}', 'LocalizationController@select')->name('locale.select');

Route::post('/error-report', 'ErrorReportController@submit')->name('errorReport.submit');
Route::get('/error-report', 'ErrorReportController@after')->name('errorReport.after');
