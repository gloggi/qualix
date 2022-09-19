<?php

namespace App\Http\Controllers;

use App\Http\Requests\FeedbackRequirementRequest;
use App\Models\Course;
use App\Models\FeedbackData;
use App\Models\FeedbackRequirement;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FeedbackListController extends Controller
{
    /**
     * Display the feedback overview page which lists all participants which I am responsible for in the context of
     * a feedback.
     *
     * @param Request $request
     * @param Course $course
     * @param User $user
     * @return Response
     */
    public function index(Request $request, Course $course) {
        $userId = $request->input('view') ?? 'all';
        $feedbackDatas = $course->feedback_datas()
            ->with('feedbacks', function($feedbacks) use($userId) {
                if ('all' !== $userId) {
                    $feedbacks->whereRelation('users', 'id', $userId);
                }
            })->get();
        $anyResponsibilities = $course->feedback_datas()->has('feedbacks.users')->exists();
        return view('feedbacks', [
            'course' => $course,
            'userId' => $userId,
            'anyResponsibilities' => $anyResponsibilities,
            'user' => $course->users()->select('name')->findOr($userId, ['id'], fn() => null),
            'feedbackDatas' => $feedbackDatas,
        ]);
    }

    /**
     * Display the feedback progress page which lists all requirements versus all participants of a particular
     * feedback in a large table.
     * @param Request $request
     * @param FeedbackData $feedbackData
     * @return View
     */
    public function progressOverview(Request $request, Course $course, FeedbackData $feedbackData) {
        return response()->view('feedback.requirements-matrix', [
            'course' => $course,
            'feedbackData' => $feedbackData,
            'feedbackRequirements' => $feedbackData->feedback_requirements()->with([
                'feedback',
                'feedback.participant',
                'requirement'
            ])->get(),
            'feedbacks' => $feedbackData->feedbacks->map->append('contents'),
            'allRequirements' => $course->requirements,
            'allParticipants' => $course->participants,
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
    public function updateRequirementStatus(FeedbackRequirementRequest $request, Course $course, FeedbackData $feedbackData, FeedbackRequirement $feedbackRequirement) {
        $data = $request->validated();
        $feedbackRequirement->update($data);
        return response()->json(['status' => 'ok']);
    }
}
