<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use App\Util\HtmlString;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

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
    public function index(Request $request, Course $course)
    {
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
            'feedbacks' => $feedbackDatas,
        ]);
    }
}
