<?php

namespace App\Http\Controllers;

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
    public function index(Request $request, Course $course, Participant $participant, Quali $quali) {
        return view('quali-detail', ['participant' => $participant, 'quali' => $quali]);
    }

    /**
     * @param Request $request
     * @param Course $course
     * @param Participant $participant
     * @param Quali $quali
     * @return View
     */
    public function edit(Request $request, Course $course, Participant $participant, Quali $quali) {
        return view('quali-edit', ['participant' => $participant, 'quali' => $quali,
            'translations' => $this->getTranslations([
                't.models.observation.model_name',
                't.views.quali_content.text_placeholder'
            ])
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
        $data = collect($request->validated()['contents']);
        return DB::transaction(function() use($request, $course, $participant, $quali, $data) {

            $allRequirementIds = $data->filter(function ($element) { return $element['type'] === 'requirement'; })->map(function ($element) { return $element['id']; });
            if (count($allRequirementIds->diff($quali->requirements()->pluck('id'))) !== 0) {
                throw ValidationException::withMessages(['contents' => trans('t.views.quali_content.error_requirements_changed')]);
            }

            $allNoteIds = $data->filter(function ($element) { return ($element['type'] === 'text') && $element['content']; })->map(function ($element) { return $element['id']; });
            $quali->notes()->whereNotIn('id', $allNoteIds)->delete();

            $allQualiObservationIds = $data->filter(function ($element) { return $element['type'] === 'observation'; })->map(function ($element) { return $element['id']; });
            $quali->observations()->whereNotIn('id', $allQualiObservationIds)->detach();


            $order = 0;

            $data->each(function ($element) use($quali, &$order) {
                switch ($element['type']) {
                    case 'text':
                        if ($element['content']) {
                            $quali->notes()->updateOrCreate(['id' => $element['id']], ['order' => $order++, 'notes' => $element['content']]);
                        }
                        break;
                    case 'observation':
                        if ($element['id']) {
                            $quali->observations()->syncWithoutDetaching([$element['id'] => ['order' => $order++]]);
                        }
                        break;
                    case 'requirement':
                        $quali->requirements()->find($element['id'])->update(['order' => $order++, 'passed' => $element['passed']]);
                        // TODO reordering inside of a requirement
                        break;
                }
            });

            return Redirect::route('qualiContent.detail', ['course' => $course->id, 'participant' => $participant->id, 'quali' => $quali->id]);
        });
    }

    protected function getTranslations($keys) {
        return collect($keys)->mapWithKeys(function ($key) {
            return [$key => trans($key)];
        })->all();
    }
}
