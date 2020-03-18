<?php

namespace App\Http\Controllers;

use App\Http\Requests\QualiRequest;
use App\Models\Course;
use App\Models\Quali;
use App\Models\QualiData;
use App\Util\HtmlString;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

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
     * @param QualiRequest $request
     * @param Course $course
     * @return RedirectResponse
     */
    public function store(QualiRequest $request, Course $course) {
        $data = $request->validated();
        return DB::transaction(function () use ($request, $course, $data) {
            $qualiData = QualiData::create(array_merge($data, ['course_id' => $course->id]));

            $qualis = $qualiData->qualis()->createMany(
                collect(array_filter(explode(',', $data['participants'])))
                    ->map(function ($participant) use($data) {
                        return [
                            'participant' => ['id' => $participant],
                            'notes' => $data['quali_notes_template'],
                        ];
                    })
                    ->all()
            );

            $qualis->each(function(Quali $quali) use($data) {
                $qualiRequirements = collect(array_filter(explode(',', $data['requirements'])))
                    ->map(function ($requirement) use($data) { return ['requirement' => ['id' => $requirement]]; })
                    ->all();
                $quali->requirements()->createMany($qualiRequirements);
            });

            $request->session()->flash('alert-success', __('t.views.admin.qualis.create_success', ['name' => $qualiData->name, 'back_to_quali_list' => $this->qualiListLink($course, 't.views.admin.qualis.back_to_quali_list')]));
            $request->session()->flash('hideLeaderAssignments', false);
            return Redirect::route('admin.qualis.edit', ['course' => $course->id, 'quali_data' => $qualiData->id]);
        });
    }

    /**
     * Creates a link to the quali list page with the text given through a translation key.
     *
     * @param Course $course
     * @param $translationKey
     * @return HtmlString
     */
    protected function qualiListLink(Course $course, $translationKey) {
        return (new HtmlString)
            ->s('<a href="' . route('admin.qualis', ['course' => $course->id]) . '">')
            ->__($translationKey)
            ->s(' <i class="fas fa-arrow-right"></i></a>');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Course $course
     * @param QualiData $quali_data
     * @return Response
     */
    public function edit(Course $course, QualiData $quali_data) {
        return view('admin.qualis.edit', ['quali_data' => $quali_data, 'hideLeaderAssignments' => session('hideLeaderAssignments', true)]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param QualiRequest $request
     * @param Course $course
     * @param QualiData $qualiData
     * @return RedirectResponse
     */
    public function update(QualiRequest $request, Course $course, QualiData $qualiData) {
        $data = $request->validated();
        return DB::transaction(function() use($request, $course, $qualiData, $data) {
            $qualiData->update($data);

            $participants = array_filter(explode(',', $data['participants']));
            $requirements = array_filter(explode(',', $data['requirements']));

            $qualiData->qualis()->whereNotIn('participant_id', $participants)->delete();
            $qualiData->quali_requirements()->whereNotIn('requirement_id', $requirements)->delete();

            collect($participants)->each(function ($participant) use($qualiData, $data) {
                $qualiData->qualis()->updateOrCreate(['participant_id' => $participant], []);
            });

            $qualiData->qualis()->each(function(Quali $quali) use($requirements) {
                collect($requirements)->each(function ($requirement) use ($quali) {
                    $quali->requirements()->updateOrCreate(['requirement_id' => $requirement], []);
                });
            });

            $qualiData->qualis()->each(function(Quali $quali) use($data) {
                $key = 'qualis.'.$quali->id.'.user';
                $quali->update(['user' => Arr::get($data, $key)]);

                // Bug in laravel-fillable-relations. Remove this once
                // https://github.com/troelskn/laravel-fillable-relations/pull/27 is merged
                if (Arr::has($data, $key) && Arr::get($data, $key) === null) {
                    $quali->user()->dissociate();
                    $quali->save();
                }
            });

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
}
