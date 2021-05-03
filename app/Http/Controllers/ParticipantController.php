<?php

namespace App\Http\Controllers;


use App\Exceptions\Handler;
use App\Exceptions\MiDataParticipantsListsParsingException;
use App\Exceptions\UnsupportedFormatException;
use App\Http\Requests\ParticipantImportRequest;
use App\Http\Requests\ParticipantRequest;
use App\Models\Course;
use App\Models\Participant;
use App\Util\HtmlString;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\RouteCollectionInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ParticipantController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return view('admin.participants.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ParticipantRequest $request
     * @param Course $course
     * @return RedirectResponse
     */
    public function store(ParticipantRequest $request, Course $course)
    {
        /** @var Participant $participant */
        $participant = Participant::create(array_merge($request->validated(), ['course_id' => $course->id]));

        $request->session()->flash('alert-success', __('t.views.admin.participants.add_success', ['name' => $participant->scout_name]));

        return Redirect::route('admin.participants', ['course' => $course->id]);
    }
    /**
     * Display a form for uploading a list of participants.
     *
     * @param Request $request
     * @param Course $course
     * @return View
     */
    public function upload(Request $request, Course $course) {
        if ($course->participants()->exists() && !$request->session()->has('alert-warning')) {
            $request->session()->now('alert-warning', trans('t.views.admin.participant_import.warning_existing_participants'));
        }


        $MiDataParticipantListLink = (new HtmlString)
            ->s('<a href="https://db.scout.ch/" target="_blank">')
            ->__('t.views.admin.participant_import.MiData.name')
            ->s('</a>');
        return view('admin.participants.import', ['MiDataLink' => $MiDataParticipantListLink]);
    }

    /**
     * Store an uploaded list of participants in storage.
     *
     * @param ParticipantImportRequest $request
     * @param Course $course
     * @return RedirectResponse
     * @throws ValidationException if parsing the uploaded file fails
     */
    public function import(ParticipantImportRequest $request, Course $course) {

        $request->validated();
        try {
            $imported = $request->getImporter()->import($request->file('file')->getRealPath(), $course);
        } catch (MiDataParticipantsListsParsingException $e) {
            throw ValidationException::withMessages(['file' => $e->getMessage()]);
        } catch (UnsupportedFormatException $e) {
            throw ValidationException::withMessages(['file' => trans('t.views.admin.participant_import.error_unsupported_format')]);
        } catch (Exception $e) {
            app(Handler::class)->report($e);
            return Redirect::back()->with('alert-danger', trans('t.views.admin.participant_import.unknown_error'));
        }

        return Redirect::route('admin.participants', ['course' => $course->id])->with('alert-success', trans_choice('t.views.admin.participant_import.import_success', $imported));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Course $course
     * @param Participant $participant
     * @return Response
     */
    public function edit(Request $request, Course $course, Participant $participant)
    {
        $this->rememberPreviouslyActiveView($request);
        return view('admin.participants.edit', ['participant' => $participant]);
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
    protected function redirectToPreviouslyActiveView(Request $request, Course $course) {
        if ($request->session()->has('return_url')) return Redirect::to($request->session()->get('return_url'));

        $returnTo = $request->session()->get('participant_before_edit');
        if ($returnTo) return Redirect::to(route('participants.detail', ['course' => $course->id, 'participant' => $returnTo]));

        return Redirect::route('admin.participants', ['course' => $course->id]);
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
     * @param ParticipantRequest $request
     * @param Course $course
     * @param Participant $participant
     * @return RedirectResponse
     */
    public function update(ParticipantRequest $request, Course $course, Participant $participant)
    {
        if ($request->file('image') && $participant->image_url) {
            Storage::delete($participant->image_url);
        }

        $participant->update($request->validated());

        $request->session()->flash('alert-success', __('t.views.admin.participants.edit_success', ['name' => $participant->scout_name]));
        return $this->redirectToPreviouslyActiveView($request, $course);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param Course $course
     * @param Participant $participant
     * @return RedirectResponse
     */
    public function destroy(Request $request, Course $course, Participant $participant)
    {
        if ($participant->image_url) {
            Storage::delete($participant->image_url);
        }
        $participant->delete();
        $request->session()->flash('alert-success', __('t.views.admin.participants.remove_success', ['name' => $participant->scout_name]));
        return Redirect::route('admin.participants', ['course' => $course->id]);
    }
}
