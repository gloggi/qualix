<?php

namespace App\Http\Controllers;

use App\Http\Requests\FeedbackRequirementRequest;
use App\Models\Course;
use App\Models\FeedbackData;
use App\Models\FeedbackRequirement;
use App\Models\Participant;
use App\Models\Requirement;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class FeedbackListController extends Controller {
    /**
     * Display the feedback progress page which lists all requirements versus all participants (which I am responsible
     * for) of a particular feedback in a large table.
     * @param Request $request
     * @param FeedbackData $feedbackData
     * @return View
     */
    public function progressOverview(Request $request, Course $course, FeedbackData $feedbackData) {
        $userId = $request->input('view') ?? 'all';
        $anyResponsibilities = $feedbackData->has('feedbacks.users')->exists();
        return response()->view('feedback.progress-overview', [
            'course' => $course,
            'feedbackData' => $feedbackData,
            'feedbackRequirements' => $feedbackData->feedback_requirements()->with([
                'feedback',
                'feedback.participant',
                'requirement'
            ])->get(),
            'feedbacks' => $feedbackData->feedbacks()->with(['participant'])->when('all' !== $userId, function($feedbacks) use($userId) {
                $feedbacks->whereRelation('users', 'id', $userId);
            })->get()->map->append('contents'),
            'allRequirements' => $course->requirements,
            'allParticipants' => $course->participants,
            'anyResponsibilities' => $anyResponsibilities,
            'userId' => $userId,
            'user' => $course->users()->select('name')->findOr($userId, ['id'], fn() => null),
        ])->withHeaders([
            'Cache-Control' => 'max-age=0, s-maxage=0, no-cache, must-revalidate, proxy-revalidate, no-store',
            'Pragma' => 'no-cache'
        ]);
    }

    /**
     * Updates the data of a single feedback requirement. Used on the requirements matrix in the edit dialog.
     * @param FeedbackRequirementRequest $request
     * @param Course $course
     * @param FeedbackData $feedbackData
     * @param FeedbackRequirement $feedbackRequirement
     * @return JsonResponse
     */
    public function updateRequirementStatus(FeedbackRequirementRequest $request, Course $course, FeedbackData $feedbackData, Participant $participant, Requirement $requirement) {
        $data = $request->validated();
        $feedbackRequirement = $feedbackData->feedback_requirements()->firstWhere([
           'participant_id' => $participant->id,
           'requirement_id' => $requirement->id,
        ]);
        if (!$feedbackRequirement) {
            throw ValidationException::withMessages(['feedback_requirement' => trans('404 not found')]);
        }
        $feedbackRequirement->update($data);
        return response()->json(['status' => 'ok']);
    }
}
