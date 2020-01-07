<?php

namespace App\Http\Controllers;

use App\Http\Requests\ObservationCreateRequest;
use App\Http\Requests\ObservationUpdateRequest;
use App\Models\Block;
use App\Models\Course;
use App\Models\Observation;
use App\Models\Participant;
use App\Util\HtmlString;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;

class ObservationController extends Controller {
    /**
     * Display a form to create a new resource.
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request) {
        return view('observation.new', ['participant_id' => $request->input('participant'), 'block_id' => $request->input('block')]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ObservationCreateRequest $request
     * @param Course $course
     * @return RedirectResponse
     */
    public function store(ObservationCreateRequest $request, Course $course) {
        $data = $request->validated();
        DB::transaction(function() use ($request, $course, $data) {
            $participant_ids = array_filter(explode(',', $data['participant_ids']));
            $requirement_ids = array_filter(explode(',', $data['requirement_ids']));
            $category_ids = array_filter(explode(',', $data['category_ids']));

            $observation = Observation::create(array_merge($data, ['course_id' => $course->id, 'user_id' => Auth::user()->getAuthIdentifier()]));
            $observation->participants()->attach($participant_ids);
            $observation->requirements()->attach($requirement_ids);
            $observation->categories()->attach($category_ids);

            $flash = (new HtmlString)->trans_choice('t.views.observations.add_success', $participant_ids);
            if (count($participant_ids) == 1) {
                $participant = Participant::find($participant_ids[0]);
                $route = route('participants.detail', ['course' => $course->id, 'participant' => $participant->id]);
                $flash->s(" <a href=\"{$route}\">")
                      ->__('t.views.observations.back_to_participant', ['name' => $participant->scout_name])
                      ->s(' <i class="fas fa-arrow-right"></i></a>');
            }
            $request->session()->flash('alert-success', $flash);
        });

        return Redirect::route('observation.new', ['course' => $course->id, 'participant' => $data['participant_ids'], 'block' => $data['block_id']]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Request $request
     * @param Course $course
     * @param Observation $observation
     * @return Response
     */
    public function edit(Request $request, Course $course, Observation $observation) {
        $request->session()->flash('referer_before_edit', $request->session()->get('referer_before_edit', URL::previous()));
        return view('observation.edit', ['observation' => $observation]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ObservationUpdateRequest $request
     * @param Course $course
     * @param Observation $observation
     * @return RedirectResponse
     */
    public function update(ObservationUpdateRequest $request, Course $course, Observation $observation) {
        DB::transaction(function () use ($request, $observation) {
            $data = $request->validated();
            $observation->update($data);

            $observation->requirements()->detach();
            $observation->requirements()->attach(array_filter(explode(',', $data['requirement_ids'])));

            $observation->categories()->detach();
            $observation->categories()->attach(array_filter(explode(',', $data['category_ids'])));
        });

        $request->session()->flash('alert-success', __('t.views.observations.edit_success'));

        return Redirect::to($request->session()->get('referer_before_edit',
            route('participants.detail', ['course' => $course->id, 'participant' => $observation->participants()->first()->id])));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param Course $course
     * @param Observation $observation
     * @return RedirectResponse
     */
    public function destroy(Request $request, Course $course, Observation $observation) {
        $observation->delete();
        $request->session()->flash('alert-success', __('t.views.participant_details.delete_observation_success'));
        return Redirect::route('participants.detail', ['course' => $course->id, 'participant' => $observation->participants()->first()->id]);
    }

    /**
     * Show an overview table with info about which user has made how many observations about which participant.
     *
     * @param Request $request
     * @param Course $course
     * @return Response
     */
    public function overview(Request $request, Course $course) {
        return view('overview', ['participants' => $course->participants->all(), 'participantManagementLink' => $this->participantManagementLink($course, 't.views.overview.here')]);
    }

    /**
     * Creates a link to the participants management page with the text given through a translation key.
     *
     * @param Course $course
     * @param $translationKey
     * @return HtmlString
     */
    protected function participantManagementLink(Course $course, $translationKey) {
        return (new HtmlString)
            ->s('<a href="' . route('admin.participants', ['course' => $course->id]) . '">')
            ->__($translationKey)
            ->s('</a>');
    }
}
