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
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return view('admin.participantGroups.index', ['participants' => $request->input('participant')]);
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

            $request->session()->flash('alert-success', __('t.views.admin.participantGroups.create_success'));        });

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
        return view('admin.participant_group.edit', ['participantGroup' => $participantGroup]);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ParticipantGroup  $participantGroup
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ParticipantGroup $participantGroup)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param Course $course
     * @param  \App\Models\ParticipantGroup  $participantGroup
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Course $course, ParticipantGroup $participantGroup)
    {
        //
    }
}
