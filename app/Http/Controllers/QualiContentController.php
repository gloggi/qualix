<?php

namespace App\Http\Controllers;

use App\Http\Requests\QualiContentRequest;
use App\Models\Course;
use App\Models\Participant;
use App\Models\Quali;
use App\Models\QualiRequirement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
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
        return DB::transaction(function() use($course, $participant, $quali, $data) {
            $this->updateContents($quali, $data);
            return Redirect::route('qualiContent.detail', ['course' => $course->id, 'participant' => $participant->id, 'quali' => $quali->id]);
        });
    }

    protected function getTranslations($keys) {
        return collect($keys)->mapWithKeys(function ($key) {
            return [$key => trans($key)];
        })->all();
    }

    /**
     * @param Quali|QualiRequirement $parent
     * @param Collection $data
     * @throws ValidationException
     */
    protected function updateContents($parent, $data) {

        if ($parent instanceof Quali) {
            if ($correctedContents = $this->qualiRequirementsHaveBeenChanged($data, $parent)) {
                // Edit the original request because old_input is derived from that
                app(Request::class)->offsetSet('contents', $correctedContents->toJson());
                throw ValidationException::withMessages(['contents' => trans('t.views.quali_content.error_requirements_changed')]);
            }
        } else {
            $data = $data->reject(function ($element) { return $element['type'] === 'requirement'; });
        }

        $allNoteIds = $data->filter(function ($element) { return ($element['type'] === 'text') && $element['content']; })->map(function ($element) { return $element['id']; });
        $parent->notes()->whereNotIn('id', $allNoteIds)->delete();

        $allQualiObservationIds = $data->filter(function ($element) { return $element['type'] === 'observation'; })->map(function ($element) { return $element['id']; });
        $parent->observations()->whereNotIn('id', $allQualiObservationIds)->detach();

        $order = 0;
        $data->each(function ($element) use($parent, &$order) {
            switch ($element['type']) {
                case 'text':
                    if ($element['content']) {
                        $parent->notes()->updateOrCreate(['id' => $element['id']], ['order' => $order++, 'notes' => $element['content']]);
                    }
                    break;
                case 'observation':
                    if ($element['id']) {
                        $parent->observations()->syncWithoutDetaching([$element['id'] => ['order' => $order++]]);
                    }
                    break;
                case 'requirement':
                    if ($parent instanceof Quali) {
                        /** @var QualiRequirement $requirement */
                        $requirement = $parent->requirements()->find($element['id']);
                        $requirement->update(['order' => $order++, 'passed' => $element['passed']]);
                        $this->updateContents($requirement, collect($element['contents']));
                    }
                    break;
            }
        });
    }

    /**
     * @param Collection $qualiContentsFromRequest
     * @param $qualiFromDB
     * @return bool|Collection
     */
    protected function qualiRequirementsHaveBeenChanged(Collection $qualiContentsFromRequest, Quali $qualiFromDB) {
        $requestRequirementIds = $qualiContentsFromRequest
            ->filter(function ($element) { return $element['type'] === 'requirement'; })
            ->map(function ($element) { return $element['id']; });
        $dbRequirementIds = $qualiFromDB->requirements()
            ->pluck('id');
        if ($requestRequirementIds->sort()->values()->all() !== $dbRequirementIds->sort()->values()->all()) {
            $stillValid = $qualiContentsFromRequest->reject($this->requirementsExcept($dbRequirementIds));
            $missingRequirements = $qualiFromDB->contents->filter($this->requirementsExcept($requestRequirementIds));
            return $stillValid->merge($missingRequirements);
        }
        return false;
    }

    protected function requirementsExcept(Collection $excludedIds) {
        return function ($element) use($excludedIds) {
            return $element['type'] === 'requirement' && !$excludedIds->containsStrict($element['id']);
        };
    }
}
