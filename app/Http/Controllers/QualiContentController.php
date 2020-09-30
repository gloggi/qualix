<?php

namespace App\Http\Controllers;

use App\Exceptions\ParticipantObservationNotFoundException;
use App\Exceptions\RequirementNotFoundException;
use App\Exceptions\RequirementsMismatchException;
use App\Http\Requests\QualiContentRequest;
use App\Models\Course;
use App\Models\Participant;
use App\Models\Quali;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class QualiContentController extends Controller {

    /**
     * @param Request $request
     * @param Course $course
     * @param Participant $participant
     * @param Quali $quali
     * @return View
     */
    public function print(Request $request, Course $course, Participant $participant, Quali $quali) {
        return view('qualiContent.print', [
            'participant' => $participant,
            'quali' => $quali,
            'observations' => $participant->observations()->with(['block', 'participants'])->withPivot('id')->get(),
        ]);
    }

    /**
     * @param Request $request
     * @param Course $course
     * @param Participant $participant
     * @param Quali $quali
     * @return View
     */
    public function edit(Request $request, Course $course, Participant $participant, Quali $quali) {
        return view('qualiContent.edit', [
            'participant' => $participant,
            'quali' => $quali,
            'observations' => $participant->observations()->with(['block', 'participants'])->withPivot('id')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param QualiContentRequest $request
     * @param Quali $quali
     * @return RedirectResponse
     */
    public function update(QualiContentRequest $request, Course $course, Participant $participant, Quali $quali) {
        $data = $request->validated()['quali_contents'];
        return DB::transaction(function() use($course, $participant, $quali, $data) {
            try {
                $quali->contents = $data;
                return Redirect::route('participants.detail', ['course' => $course->id, 'participant' => $participant->id]);
            } catch (RequirementsMismatchException $e) {
                // Edit the original request in order to change the old_input that is displayed to the user after
                // the validation error
                app(Request::class)->offsetSet('quali_contents', $e->getCorrectedContents());
                throw ValidationException::withMessages(['quali_contents' => trans('t.views.quali_content.error_requirements_changed')]);
            } catch (RequirementNotFoundException $e) {
                throw ValidationException::withMessages(['quali_contents' => trans('t.views.quali_content.error_requirement_not_found')]);
            } catch (ParticipantObservationNotFoundException $e) {
                throw ValidationException::withMessages(['quali_contents' => trans('t.views.quali_content.error_observation_not_found')]);
            }
        });
    }
}
