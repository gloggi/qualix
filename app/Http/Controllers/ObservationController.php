<?php

namespace App\Http\Controllers;

use App\Http\Requests\ObservationRequest;
use App\Models\Course;
use App\Models\Observation;
use App\Util\HtmlString;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\RouteCollectionInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

class ObservationController extends Controller {
    /**
     * Display a form to create a new resource.
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request) {
        $this->rememberPreviouslyActiveView($request);
        return view('observation.new', ['participants' => $request->input('participant'), 'block' => $request->input('block')]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ObservationRequest $request
     * @param Course $course
     * @return RedirectResponse
     */
    public function store(ObservationRequest $request, Course $course) {
        $data = $request->validated();
        DB::transaction(function() use ($request, $course, $data) {

            $observation = Observation::create(array_merge($data, ['course_id' => $course->id, 'user_id' => Auth::user()->getAuthIdentifier()]));

            $participantIds = array_filter(explode(',', $data['participants']));
            $observation->participants()->attach($participantIds);
            $observation->requirements()->attach(array_filter(explode(',', $data['requirements'])));
            $observation->categories()->attach(array_filter(explode(',', $data['categories'])));

            $flash = (new HtmlString)->__('t.views.observations.add_success');
            if (count($participantIds) == 1) {
                $participant = $observation->participants()->first();
                $route = route('participants.detail', ['course' => $course->id, 'participant' => $participant->id]);
                $flash->s(" <a href=\"{$route}\">")
                      ->__('t.views.observations.go_to_participant', ['name' => $participant->scout_name])
                      ->s(' <i class="fas fa-arrow-right"></i></a>');
            }

            $request->session()->flash('alert-success', $flash);
        });

        return $this->redirectToPreviouslyActiveView($request, $course, collect([]), route('observation.new', ['course' => $course->id, 'participant' => $data['participants'], 'block' => $data['block']]));
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
        $this->rememberPreviouslyActiveView($request);
        return view('observation.edit', ['observation' => $observation]);
    }

    /**
     * Stores the participant which the user was just looking at into the session, for restoring the same view later.
     *
     * @param Request $request
     */
    protected function rememberPreviouslyActiveView(Request $request) {
        $returnTo = $this->extractPathParameter(URL::previous(), 'participants.detail', 'participant');
        $request->session()->keep(['return_url']);
        $request->session()->flash('participant_before_edit', $request->session()->get('participant_before_edit', $returnTo));
    }

    /**
     * Redirects the user back to the view that was remembered in the session previously. If the previously viewed
     * participant is not in the passed options, falls back to the first viable option.
     *
     * @param Request $request
     * @param Course $course
     * @param Collection $returnOptions a collection of participant ids that are legal to be viewed
     * @param string|null $fallback
     * @return RedirectResponse
     */
    protected function redirectToPreviouslyActiveView(Request $request, Course $course, Collection $returnOptions, $fallback = null) {
        if ($request->session()->has('return_url')) return Redirect::to($request->session()->get('return_url'));

        $returnTo = $request->session()->get('participant_before_edit');
        if (!$returnOptions->contains($returnTo)) {
            $returnTo = $returnOptions->first();
        }

        if ($returnTo) return Redirect::to(route('participants.detail', ['course' => $course->id, 'participant' => $returnTo]));
        return Redirect::to($fallback ?? URL::previous());
    }

    /**
     * Parse a URL, interpret it as a route in our app and extract one of the parameters values.
     *
     * @param $url
     * @param $routeName
     * @param $parameterName
     * @return string|object|null
     */
    protected function extractPathParameter($url, $routeName, $parameterName) {
        /** @var RouteCollectionInterface $routes */
        $routes = Route::getRoutes();
        return $routes->getByName($routeName)->bind(new Request([], [], [], [], [], ['REQUEST_URI' => $url]))->parameter($parameterName);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ObservationRequest $request
     * @param Course $course
     * @param Observation $observation
     * @return RedirectResponse
     */
    public function update(ObservationRequest $request, Course $course, Observation $observation) {
        DB::transaction(function () use ($request, $course, $observation) {
            $data = $request->validated();
            $observation->update($data);

            $observation->participants()->sync(array_filter(explode(',', $data['participants'])));
            $observation->requirements()->sync(array_filter(explode(',', $data['requirements'])));
            $observation->categories()->sync(array_filter(explode(',', $data['categories'])));
        });

        $request->session()->flash('alert-success', __('t.views.observations.edit_success'));

        return $this->redirectToPreviouslyActiveView($request, $course, $observation->participants()->pluck('participants.id'));
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
        $participantId = $observation->participants()->first()->id;
        $observation->delete();
        $request->session()->flash('alert-success', __('t.views.participant_details.delete_observation_success'));
        return Redirect::route('participants.detail', ['course' => $course->id, 'participant' => $participantId]);
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
