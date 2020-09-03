<?php

namespace App\Http\Controllers;

use App\Http\Requests\ParticipantGroupRequest;
use App\Models\Course;
use App\Models\ParticipantGroup;
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

class ParticipantGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('admin.participantGroups.index');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  ParticipantGroupRequest $request
     * @param Course $course

     * @return RedirectResponse
     */
    public function store(ParticipantGroupRequest $request, Course $course)
    {

        DB::transaction(function() use ($request, $course){
            $data = $request->validated();
            $participantGroup = ParticipantGroup::create(array_merge($data, ['course_id' => $course->id]));

            $participantIds = array_filter(explode(',', $data['participants']));
            $participantGroup->participants()->attach($participantIds);

            $request->session()->flash('alert-success', __('t.views.admin.participant_groups.create_success'));
        });

        return Redirect::route('admin.participantGroups.index', ['course' => $course->id]);


    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param Course $course
     * @param  \App\Models\ParticipantGroup  $participantGroup
     * @return \Illuminate\Http\Response
     */
    public function edit(Course $course, ParticipantGroup $participantGroup)
    {
        return view('admin.participantGroups.edit', ['participantGroup' => $participantGroup]);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  ParticipantGroupRequest $request
     * @param Course $course
     * @param  ParticipantGroup  $participantGroup
     * @return RedirectResponse
     */
    public function update(ParticipantGroupRequest $request, Course $course, ParticipantGroup $participantGroup)
    {
        DB::transaction(function () use ($request, $course, $participantGroup) {
            $data = $request->validated();
            $participantGroup->update($data);

            $participantGroup->participants()->detach(null);
            $participantGroup->participants()->attach(array_filter(explode(',', $data['participants'])));
            $request->session()->flash('alert-success', __('t.views.admin.participant_groups.edit_success'));
        });
        return Redirect::route('admin.participantGroups.index', ['course' => $course->id]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param Course $course
     * @param  ParticipantGroup  $participantGroup
     * @return RedirectResponse
     *
     */
    public function destroy(Request $request, Course $course, ParticipantGroup $participantGroup)
    {
        $participantGroup->delete();
        $request->session()->flash('alert-success', __('t.views.admin.participant_groups.delete_success'));
        return Redirect::route('admin.participantGroups.index', ['course' => $course->id]);
    }
}
