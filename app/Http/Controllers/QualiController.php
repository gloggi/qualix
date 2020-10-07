<?php

namespace App\Http\Controllers;

use App\Exceptions\RequirementsMismatchException;
use App\Http\Requests\QualiCreateRequest;
use App\Http\Requests\QualiUpdateRequest;
use App\Models\Course;
use App\Models\Quali;
use App\Models\QualiData;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;

class QualiController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {
        return view('admin.qualis.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param QualiCreateRequest $request
     * @param Course $course
     * @return RedirectResponse
     */
    public function store(QualiCreateRequest $request, Course $course) {
        $data = $request->validated();
        return DB::transaction(function () use ($request, $course, $data) {
            $qualiData = QualiData::create(array_merge($data, ['course_id' => $course->id]));

            $qualis = $qualiData->qualis()->createMany(
                collect(array_filter(explode(',', $data['participants'])))
                    ->map(function ($participant) { return ['participant' => ['id' => $participant]]; })
            );

            $qualis->each(function(Quali $quali) use($data, $course) {
                $quali->requirements()->sync(collect(array_filter(explode(',', $data['requirements'])))
                    ->mapWithKeys(function ($requirementId) { return [$requirementId => ['order' => 0, 'passed' => null]]; })
                );
                $quali->unsetRelation('requirements');

                try {
                    $quali->contents = $data['quali_contents_template'];
                } catch(RequirementsMismatchException $e) {
                    // Edit the original request in order to change the old_input that is displayed to the user after
                    // the validation error
                    app(Request::class)->offsetSet('quali_contents_template', $e->getCorrectedContents());
                    throw ValidationException::withMessages(['requirements' => trans('t.views.admin.qualis.error_requirements_dont_match')]);
                }
            });

            $this->setTrainerAssignments($qualis, $data);

            $request->session()->flash('alert-success', __('t.views.admin.qualis.create_success', ['name' => $qualiData->name]));
            return Redirect::route('admin.qualis', ['course' => $course->id]);
        });
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Course $course
     * @param QualiData $quali_data
     * @return Response
     */
    public function edit(Course $course, QualiData $quali_data) {
        return view('admin.qualis.edit', ['quali_data' => $quali_data]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param QualiUpdateRequest $request
     * @param Course $course
     * @param QualiData $qualiData
     * @return RedirectResponse
     */
    public function update(QualiUpdateRequest $request, Course $course, QualiData $qualiData) {
        $data = $request->validated();
        return DB::transaction(function() use($request, $course, $qualiData, $data) {
            $qualiData->update($data);

            $participants = array_filter(explode(',', $data['participants']));
            $requirements = array_filter(explode(',', $data['requirements']));

            $qualiData->qualis()->whereNotIn('participant_id', $participants)->delete();
            collect($participants)->each(function ($participant) use($qualiData, $data) {
                $qualiData->qualis()->updateOrCreate(['participant_id' => $participant], []);
            });

            $qualiData->qualis()->each(function (Quali $quali) use($requirements) {
                $quali->requirements()->wherePivotNotIn('requirement_id', $requirements)->detach();
                $quali->unsetRelation('requirements');
                $quali->appendRequirements(
                    $quali->quali_data->course->requirements()
                        ->whereIn('id', $requirements)
                        ->whereNotIn('id', $quali->requirements()->pluck('requirements.id'))
                        ->get()
                );
            });

            $this->setTrainerAssignments($qualiData->qualis(), $data);

            $request->session()->flash('alert-success', __('t.views.admin.qualis.edit_success', ['name' => $qualiData->name]));
            return Redirect::route('admin.qualis', ['course' => $course->id]);
        });
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param Course $course
     * @param QualiData $qualiData
     * @return RedirectResponse
     */
    public function destroy(Request $request, Course $course, QualiData $qualiData) {
        $qualiData->delete();
        $request->session()->flash('alert-success', __('t.views.admin.qualis.delete_success', ['name' => $qualiData->name]));
        return Redirect::route('admin.qualis', ['course' => $course->id]);
    }

    /**
     * @param Collection|HasMany $qualis
     * @param array $data
     */
    protected function setTrainerAssignments($qualis, $data) {
        $qualis->each(function(Quali $quali) use($data) {
            $key = 'qualis.'.$quali->participant->id.'.user';
            $quali->update(['user' => Arr::get($data, $key)]);

            // Bug in laravel-fillable-relations. Remove this once
            // https://github.com/troelskn/laravel-fillable-relations/pull/27 is merged
            if (Arr::has($data, $key) && Arr::get($data, $key) === null) {
                $quali->user()->dissociate();
                $quali->save();
            }
        });
    }
}
