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

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BlockController;
use App\Http\Controllers\BlockListController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\EquipeController;
use App\Http\Controllers\ErrorReportController;
use App\Http\Controllers\FeedbackContentController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\FeedbackListController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\LocalizationController;
use App\Http\Controllers\ObservationAssignmentController;
use App\Http\Controllers\ObservationController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\ParticipantDetailController;
use App\Http\Controllers\ParticipantGroupController;
use App\Http\Controllers\ParticipantListController;
use App\Http\Controllers\RequirementController;
use App\Http\Controllers\RequirementStatusController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'restoreFormData'])->group(function () {

    Route::get('/', [CourseController::class, 'noCourse'])->name('home');
    Route::get('/course', [CourseController::class, 'noCourse']);
    Route::get('/user', [HomeController::class, 'editUser'])->name('user');
    Route::post('/user', [HomeController::class, 'updateUser'])->name('user.update');

    Route::get('/course/{course}', [HomeController::class, 'index'])->name('index');

    Route::get('/course/{course}/crib/{user?}', [BlockListController::class, 'crib'])->name('crib');

    Route::middleware('courseNotArchived')->group(function () {
        Route::get('/course/{course}/feedbacks', [FeedbackListController::class, 'index'])->name('feedbacks');
        Route::get('/course/{course}/feedbacks/{feedback_data}', [FeedbackListController::class, 'progressOverview'])->name('feedback.requirementMatrix');
        Route::post('/course/{course}/feedbacks/{feedback_data}/{participant}/{requirement}', [FeedbackListController::class, 'updateRequirementStatus'])->name('feedback.updateRequirementStatus');
        Route::get('/course/{course}/participants', [ParticipantListController::class, 'index'])->name('participants');
        Route::get('/course/{course}/participants/{participant}', [ParticipantDetailController::class, 'index'])->name('participants.detail');

        Route::get('/course/{course}/participants/{participant}/feedbacks/{feedback}/print', [FeedbackContentController::class, 'print'])->name('feedbackContent.print');
        Route::get('/course/{course}/participants/{participant}/feedbacks/{feedback}/edit', [FeedbackContentController::class, 'edit'])->name('feedbackContent.edit');
        Route::post('/course/{course}/participants/{participant}/feedbacks/{feedback}', [FeedbackContentController::class, 'update'])->name('feedbackContent.update');

        Route::get('/course/{course}/overview/{feedback_data?}', [ObservationController::class, 'overview'])->name('overview');

        Route::get('/course/{course}/observation/new', [ObservationController::class, 'create'])->name('observation.new');
        Route::post('/course/{course}/observation/new', [ObservationController::class, 'store'])->name('observation.store');
        Route::get('/course/{course}/observation/{observation}', [ObservationController::class, 'edit'])->name('observation.edit');
        Route::post('/course/{course}/observation/{observation}', [ObservationController::class, 'update'])->name('observation.update');
        Route::delete('/course/{course}/observation/{observation}', [ObservationController::class, 'destroy'])->name('observation.delete');

        Route::post('/course/{course}/admin/archive', [CourseController::class, 'archive'])->name('admin.course.archive');
    });

    Route::get('/course/{course}/admin', [CourseController::class, 'edit'])->name('admin.course');
    Route::post('/course/{course}/admin', [CourseController::class, 'update'])->name('admin.course.update');
    Route::delete('/course/{course}/admin', [CourseController::class, 'delete'])->name('admin.course.delete');

    Route::get('/course/{course}/admin/equipe', [EquipeController::class, 'index'])->name('admin.equipe');
    Route::delete('/course/{course}/admin/equipe/{user}', [EquipeController::class, 'destroy'])->name('admin.equipe.delete');

    Route::post('/course/{course}/admin/invitation', [InvitationController::class, 'store'])->name('admin.invitation.store');
    Route::delete('/course/{course}/admin/invitation/{email}', [InvitationController::class, 'destroy'])->name('admin.invitation.delete');
    Route::get('/invitation/{token}', [InvitationController::class, 'index'])->name('invitation.view');
    Route::post('/invitation', [InvitationController::class, 'claim'])->name('invitation.claim');

    Route::middleware('courseNotArchived')->group(function() {
        Route::get('/course/{course}/admin/participants', [ParticipantController::class, 'index'])->name('admin.participants');
        Route::post('/course/{course}/admin/participants', [ParticipantController::class, 'store'])->name('admin.participants.store');
        Route::get('/course/{course}/admin/participants/import', [ParticipantController::class, 'upload'])->name('admin.participants.upload');
        Route::post('/course/{course}/admin/participants/import', [ParticipantController::class, 'import'])->name('admin.participants.import');
        Route::get('/course/{course}/admin/participants/{participant}', [ParticipantController::class, 'edit'])->name('admin.participants.edit');
        Route::post('/course/{course}/admin/participants/{participant}', [ParticipantController::class, 'update'])->name('admin.participants.update');
        Route::delete('/course/{course}/admin/participants/{participant}', [ParticipantController::class, 'destroy'])->name('admin.participants.delete');

        Route::get('/course/{course}/admin/participantGroups', [ParticipantGroupController::class, 'index'])->name('admin.participantGroups');
        Route::post('/course/{course}/admin/participantGroups', [ParticipantGroupController::class, 'store'])->name('admin.participantGroups.store');
        Route::get('/course/{course}/admin/participantGroups/generate', [ParticipantGroupController::class, 'generate'])->name('admin.participantGroups.generate');
        Route::post('/course/{course}/admin/participantGroups/generate', [ParticipantGroupController::class, 'storeMany'])->name('admin.participantGroups.storeMany');
        Route::get('/course/{course}/admin/participantGroups/{participantGroup}', [ParticipantGroupController::class, 'edit'])->name('admin.participantGroups.edit');
        Route::post('/course/{course}/admin/participantGroups/{participantGroup}', [ParticipantGroupController::class, 'update'])->name('admin.participantGroups.update');
        Route::delete('/course/{course}/admin/participantGroups/{participantGroup}', [ParticipantGroupController::class, 'destroy'])->name('admin.participantGroups.delete');

        Route::get('/course/{course}/admin/observationAssignments', [ObservationAssignmentController::class, 'index'])->name('admin.observationAssignments');
        Route::post('/course/{course}/admin/observationAssignments', [ObservationAssignmentController::class, 'store'])->name('admin.observationAssignments.store');
        Route::get('/course/{course}/admin/observationAssignments/{observationAssignment}', [ObservationAssignmentController::class, 'edit'])->name('admin.observationAssignments.edit');
        Route::post('/course/{course}/admin/observationAssignments/{observationAssignment}', [ObservationAssignmentController::class, 'update'])->name('admin.observationAssignments.update');
        Route::delete('/course/{course}/admin/observationAssignments/{observationAssignment}', [ObservationAssignmentController::class, 'destroy'])->name('admin.observationAssignments.delete');
    });

    Route::get('/course/{course}/admin/blocks', [BlockController::class, 'index'])->name('admin.blocks');
    Route::post('/course/{course}/admin/blocks', [BlockController::class, 'store'])->name('admin.block.store');
    Route::get('/course/{course}/admin/blocks/import', [BlockController::class, 'upload'])->name('admin.block.upload');
    Route::post('/course/{course}/admin/blocks/import', [BlockController::class, 'import'])->name('admin.block.import');
    Route::get('/course/{course}/admin/blocks/{block}', [BlockController::class, 'edit'])->name('admin.block.edit');
    Route::post('/course/{course}/admin/blocks/{block}', [BlockController::class, 'update'])->name('admin.block.update');
    Route::delete('/course/{course}/admin/blocks/{block}', [BlockController::class, 'destroy'])->name('admin.block.delete');

    Route::get('/course/{course}/admin/requirement', [RequirementController::class, 'index'])->name('admin.requirements');
    Route::post('/course/{course}/admin/requirement', [RequirementController::class, 'store'])->name('admin.requirements.store');
    Route::get('/course/{course}/admin/requirement/{requirement}', [RequirementController::class, 'edit'])->name('admin.requirements.edit');
    Route::post('/course/{course}/admin/requirement/{requirement}', [RequirementController::class, 'update'])->name('admin.requirements.update');
    Route::delete('/course/{course}/admin/requirement/{requirement}', [RequirementController::class, 'destroy'])->name('admin.requirements.delete');

    Route::get('/course/{course}/admin/requirement_status', [RequirementStatusController::class, 'index'])->name('admin.requirement_statuses');
    Route::post('/course/{course}/admin/requirement_status', [RequirementStatusController::class, 'store'])->name('admin.requirement_statuses.store');
    Route::get('/course/{course}/admin/requirement_status/{requirement_status}', [RequirementStatusController::class, 'edit'])->name('admin.requirement_statuses.edit');
    Route::post('/course/{course}/admin/requirement_status/{requirement_status}', [RequirementStatusController::class, 'update'])->name('admin.requirement_statuses.update');
    Route::delete('/course/{course}/admin/requirement_status/{requirement_status}', [RequirementStatusController::class, 'destroy'])->name('admin.requirement_statuses.delete');

    Route::get('/course/{course}/admin/category', [CategoryController::class, 'index'])->name('admin.categories');
    Route::post('/course/{course}/admin/category', [CategoryController::class, 'store'])->name('admin.categories.store');
    Route::get('/course/{course}/admin/category/{category}', [CategoryController::class, 'edit'])->name('admin.categories.edit');
    Route::post('/course/{course}/admin/category/{category}', [CategoryController::class, 'update'])->name('admin.categories.update');
    Route::delete('/course/{course}/admin/category/{category}', [CategoryController::class, 'destroy'])->name('admin.categories.delete');

    Route::get('/course/{course}/admin/feedbacks', [FeedbackController::class, 'index'])->name('admin.feedbacks');
    Route::middleware('courseNotArchived')->group(function() {
        Route::post('/course/{course}/admin/feedbacks', [FeedbackController::class, 'store'])->name('admin.feedbacks.store');
        Route::get('/course/{course}/admin/feedbacks/{feedback_data}', [FeedbackController::class, 'edit'])->name('admin.feedbacks.edit');
        Route::post('/course/{course}/admin/feedbacks/{feedback_data}', [FeedbackController::class, 'update'])->name('admin.feedbacks.update');
    });
    Route::delete('/course/{course}/admin/feedbacks/{feedback_data}', [FeedbackController::class, 'destroy'])->name('admin.feedbacks.delete');

    Route::get('/newcourse', [CourseController::class, 'create'])->name('admin.newcourse');
    Route::post('/newcourse', [CourseController::class, 'store'])->name('admin.newcourse.store');

    Route::get('/refreshCsrf', [HomeController::class, 'refreshCsrf'])->name('refreshCsrf');
});

Auth::routes(['verify' => true]);
Route::get('login/hitobito', [LoginController::class, 'redirectToHitobitoOAuth'])->name('login.hitobito');
Route::get('login/hitobito/callback', [LoginController::class, 'handleHitobitoOAuthCallback'])->name('login.hitobito.callback');
Route::get('locale/{locale}', [LocalizationController::class, 'select'])->name('locale.select');

Route::post('/error-report', [ErrorReportController::class, 'submit'])->name('errorReport.submit');
Route::get('/error-report', [ErrorReportController::class, 'after'])->name('errorReport.after');
