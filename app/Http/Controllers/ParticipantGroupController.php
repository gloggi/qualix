<?php

namespace App\Http\Controllers;

use App\Http\Requests\MultiParticipantGroupRequest;
use App\Http\Requests\ParticipantGroupRequest;
use App\Models\Course;
use App\Models\ParticipantGroup;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

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
            $participantGroup->participants()->sync(array_filter(explode(',', $data['participants'])));
            $request->session()->flash('alert-success', __('t.views.admin.participant_groups.create_success'));
        });

        return Redirect::route('admin.participantGroups', ['course' => $course->id]);
    }

    /**
     * Display a UI for generating participant groups.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function generate()
    {
        return view('admin.participantGroups.generate');
    }

    /**
     * Store multiple newly created resources in storage.
     *
     * @param  MultiParticipantGroupRequest $request
     * @param Course $course

     * @return RedirectResponse
     */
    public function storeMany(MultiParticipantGroupRequest $request, Course $course)
    {
        DB::transaction(function() use ($request, $course){
            $data = $request->validated();
            foreach($data['participantGroups'] as $groupData) {
                $participantGroup = ParticipantGroup::create(array_merge($groupData, ['course_id' => $course->id]));
                $participantGroup->participants()->sync(array_filter(explode(',', $groupData['participants'])));
            }
            $request->session()->flash('alert-success', __('t.views.admin.participant_groups.create_many_success'));
        });

        return Redirect::route('admin.participantGroups', ['course' => $course->id]);
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
            $participantGroup->participants()->sync(array_filter(explode(',', $data['participants'])));
            $request->session()->flash('alert-success', __('t.views.admin.participant_groups.edit_success'));
        });
        return Redirect::route('admin.participantGroups', ['course' => $course->id]);
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
        return Redirect::route('admin.participantGroups', ['course' => $course->id]);
    }
}
