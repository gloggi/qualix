<?php

namespace App\Http\Controllers;

use App\Http\Requests\ParticipantGroupRequest;
use App\Models\Course;
use App\Models\ParticipantGroup;
use Illuminate\Http\Request;

class ParticipantGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return view('admin.participant_groups.index', ['participants' => $request->input('participant')]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  ParticipantGroupRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(ParticipantGroupRequest $request)
    {
        $data = $request->validated();

        dd($request);
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
