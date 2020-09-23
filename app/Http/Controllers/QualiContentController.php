<?php

namespace App\Http\Controllers;

use App\Exceptions\ObservationNotFoundException;
use App\Exceptions\RequirementNotFoundException;
use App\Http\Requests\QualiContentRequest;
use App\Models\Course;
use App\Models\Participant;
use App\Models\Quali;
use App\Exceptions\RequirementsOutdatedException;
use App\Services\TiptapFormatter;
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
    public function index(Request $request, Course $course, Participant $participant, Quali $quali) {
        return view('qualiContent.index', [
            'participant' => $participant,
            'quali' => $quali,
            'observations' => $participant->observations()->with(['block', 'participants'])->get(),
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
            'observations' => $participant->observations()->with(['block', 'participants'])->get(),
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
        $data = $request->validated()['qualiContents'];
        return DB::transaction(function() use($course, $participant, $quali, $data) {
            try {
                $quali->contents = $data;
                return Redirect::route('qualiContent.detail', ['course' => $course->id, 'participant' => $participant->id, 'quali' => $quali->id]);
            } catch (RequirementsOutdatedException $e) {
                // Edit the original request in order to change the old_input that is displayed to the user after
                // the validation error
                app(Request::class)->offsetSet('qualiContents', $e->getCorrectedContents());
                throw ValidationException::withMessages(['qualiContents' => trans('t.views.quali_content.error_requirements_changed')]);
            } catch (RequirementNotFoundException $e) {
                throw ValidationException::withMessages(['qualiContents' => trans('t.views.quali_content.error_requirement_not_found')]);
            } catch (ObservationNotFoundException $e) {
                throw ValidationException::withMessages(['qualiContents' => trans('t.views.quali_content.error_observation_not_found')]);
            }
        });
    }
}
