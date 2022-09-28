<?php

namespace App\Http\Controllers;

use App\Exceptions\ParticipantObservationNotFoundException;
use App\Exceptions\RequirementNotFoundException;
use App\Exceptions\RequirementsMismatchException;
use App\Http\Requests\FeedbackContentRequest;
use App\Models\Course;
use App\Models\Feedback;
use App\Models\Participant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class FeedbackContentController extends Controller {

    /**
     * @param Request $request
     * @param Course $course
     * @param Participant $participant
     * @param Feedback $feedback
     * @return JsonResponse
     */
    public function print(Request $request, Course $course, Participant $participant, Feedback $feedback) {
        return response()->json([
            'course' => $course,
            'participant' => $participant,
            'feedback' => $feedback,
            'feedbackContents' => $feedback->contents,
            'observations' => $participant->observations()->with(['block', 'participants'])->withPivot('id')->get(),
            'statuses' => $course->requirement_statuses,
        ]);
    }

    /**
     * @param Request $request
     * @param Course $course
     * @param Participant $participant
     * @param Feedback $feedback
     * @return View
     */
    public function edit(Request $request, Course $course, Participant $participant, Feedback $feedback) {
        return response()->view('feedbackContent.edit', [
            'participant' => $participant,
            'feedback' => $feedback,
            'observations' => $participant->observations()
                ->with(['block', 'participants'])
                ->withPivot('id')
                ->join('blocks', 'blocks.id', 'observations.block_id')
                ->orderBy('blocks.block_date')
                ->orderBy('blocks.day_number')
                ->orderBy('blocks.block_number')
                ->orderBy('blocks.name')
                ->orderBy('blocks.id')
                ->orderBy('observations.created_at')
                ->get(),
        ])->withHeaders([
            'Cache-Control' => 'max-age=0, s-maxage=0, no-cache, must-revalidate, proxy-revalidate, no-store',
            'Pragma' => 'no-cache'
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param FeedbackContentRequest $request
     * @param Feedback $feedback
     * @return Response
     */
    public function update(FeedbackContentRequest $request, Course $course, Participant $participant, Feedback $feedback) {
        $data = $request->validated()['feedback_contents'];
        return DB::transaction(function() use($course, $participant, $feedback, $data) {
            try {
                $feedback->contents = $data;
                return response()->json(['status' => 'ok']);
            } catch (RequirementsMismatchException $e) {
                // Edit the original request in order to change the old_input that is displayed to the user after
                // the validation error
                app(Request::class)->offsetSet('feedback_contents', $e->getCorrectedContents());
                throw ValidationException::withMessages(['feedback_contents' => trans('t.views.feedback_content.error_requirements_changed')]);
            } catch (RequirementNotFoundException $e) {
                throw ValidationException::withMessages(['feedback_contents' => trans('t.views.feedback_content.error_requirement_not_found')]);
            } catch (ParticipantObservationNotFoundException $e) {
                throw ValidationException::withMessages(['feedback_contents' => trans('t.views.feedback_content.error_observation_not_found')]);
            }
        });
    }
}
