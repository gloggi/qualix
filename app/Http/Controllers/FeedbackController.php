<?php

namespace App\Http\Controllers;

use App\Exceptions\RequirementsMismatchException;
use App\Http\Requests\FeedbackCreateRequest;
use App\Http\Requests\FeedbackUpdateRequest;
use App\Models\Course;
use App\Models\Feedback;
use App\Models\FeedbackData;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;

class FeedbackController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {
        return view('admin.feedbacks.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param FeedbackCreateRequest $request
     * @param Course $course
     * @return RedirectResponse
     */
    public function store(FeedbackCreateRequest $request, Course $course) {
        $data = $request->validated();
        return DB::transaction(function () use ($request, $course, $data) {
            $feedbackData = FeedbackData::create(array_merge($data, ['course_id' => $course->id]));

            $feedbacks = $feedbackData->feedbacks()->createMany(
                collect(array_filter(explode(',', $data['participants'])))
                    ->map(function ($participant) { return ['participant' => ['id' => $participant]]; })
            );

            $feedbacks->each(function(Feedback $feedback) use($data, $course) {
                $feedback->requirements()->sync(collect(array_filter(explode(',', $data['requirements'])))
                    ->mapWithKeys(function ($requirementId) { return [$requirementId => ['order' => 0, 'passed' => null]]; })
                );
                $feedback->unsetRelation('requirements');

                try {
                    $feedback->contents = $data['feedback_contents_template'];
                } catch(RequirementsMismatchException $e) {
                    // Edit the original request in order to change the old_input that is displayed to the user after
                    // the validation error
                    app(Request::class)->offsetSet('feedback_contents_template', $e->getCorrectedContents());
                    throw ValidationException::withMessages(['requirements' => trans('t.views.admin.feedbacks.error_requirements_dont_match')]);
                }
            });

            $this->setTrainerAssignments($feedbacks, $data);

            $request->session()->flash('alert-success', __('t.views.admin.feedbacks.create_success', ['name' => $feedbackData->name]));
            return Redirect::route('admin.feedbacks', ['course' => $course->id]);
        });
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Course $course
     * @param FeedbackData $feedback_data
     * @return Response
     */
    public function edit(Course $course, FeedbackData $feedback_data) {
        return view('admin.feedbacks.edit', ['feedback_data' => $feedback_data]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param FeedbackUpdateRequest $request
     * @param Course $course
     * @param FeedbackData $feedbackData
     * @return RedirectResponse
     */
    public function update(FeedbackUpdateRequest $request, Course $course, FeedbackData $feedbackData) {
        $data = $request->validated();
        return DB::transaction(function() use($request, $course, $feedbackData, $data) {
            $feedbackData->update($data);

            $participants = array_filter(explode(',', $data['participants']));
            $requirements = array_filter(explode(',', $data['requirements']));

            $feedbackData->feedbacks()->whereNotIn('participant_id', $participants)->delete();
            collect($participants)->each(function ($participant) use($feedbackData, $data) {
                $feedbackData->feedbacks()->updateOrCreate(['participant_id' => $participant], []);
            });

            $feedbackData->feedbacks()->each(function (Feedback $feedback) use($requirements) {
                $feedback->requirements()->wherePivotNotIn('requirement_id', $requirements)->detach();
                $feedback->unsetRelation('requirements');
                $feedback->appendRequirements(
                    $feedback->feedback_data->course->requirements()
                        ->whereIn('id', $requirements)
                        ->whereNotIn('id', $feedback->requirements()->pluck('requirements.id'))
                        ->get()
                );
            });

            $this->setTrainerAssignments($feedbackData->feedbacks(), $data);

            $request->session()->flash('alert-success', __('t.views.admin.feedbacks.edit_success', ['name' => $feedbackData->name]));
            return Redirect::route('admin.feedbacks', ['course' => $course->id]);
        });
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param Course $course
     * @param FeedbackData $feedbackData
     * @return RedirectResponse
     */
    public function destroy(Request $request, Course $course, FeedbackData $feedbackData) {
        $feedbackData->delete();
        $request->session()->flash('alert-success', __('t.views.admin.feedbacks.delete_success', ['name' => $feedbackData->name]));
        return Redirect::route('admin.feedbacks', ['course' => $course->id]);
    }

    /**
     * @param Collection|HasMany $feedbacks
     * @param array $data
     */
    protected function setTrainerAssignments($feedbacks, $data) {
        $feedbacks->each(function(Feedback $feedback) use($data) {
            $key = 'feedbacks.'.$feedback->participant->id.'.users';
            $user_ids = array_filter(explode(',', Arr::get($data, $key, '')));
            $feedback->users()->detach();
            $feedback->users()->attach($user_ids);
        });
    }
}
