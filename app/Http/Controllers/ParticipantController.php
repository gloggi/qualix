<?php

namespace App\Http\Controllers;


use App\Exceptions\Handler;
use App\Exceptions\MiDataParticipantsListsParsingException;
use App\Http\Requests\ParticipantImportRequest;
use App\Http\Requests\ParticipantRequest;
use App\Models\Course;
use App\Models\Participant;
use App\Util\HtmlString;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
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
    public function edit(Course $course, Participant $participant)
    {
        return view('admin.participants.edit', ['participant' => $participant]);
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
        return Redirect::route('admin.participants', ['course' => $course->id]);
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
