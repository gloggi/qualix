<?php

namespace App\Http\Controllers;

use App\Http\Requests\ObservationAssignmentRequest;
use App\Models\Course;
use App\Models\ObservationAssignment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;


class ObservationAssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('admin.observationAssignments.index');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  ObservationAssignmentRequest $request
     * @param Course $course

     * @return RedirectResponse
     */
    public function store(ObservationAssignmentRequest $request, Course $course)
    {
        $data = $request->validated();

        DB::transaction(function() use ($request,$course, $data){

            $observationAssignment = ObservationAssignment::create(array_merge($data, ['course_id' => $course->id]));

            $observationAssignment->participants()->attach(array_filter(explode(',', $data['participants'])));
            $observationAssignment->blocks()->attach(array_filter(explode(',', $data['blocks'])));
            $observationAssignment->users()->attach(array_filter(explode(',', $data['users'])));

            $request->session()->flash('alert-success', __('t.views.admin.observation_assignments.create_success'));
        });

        return Redirect::route('admin.observationAssignments', ['course' => $course->id]);

    }


    /**
     * Show the form for editing the specified resource.
     * @param Course $course
     * @param ObservationAssignment $observationAssignment
     * @return \Illuminate\Http\Response
     */
    public function edit(Course $course, ObservationAssignment $observationAssignment)
    {
        return view('admin.observationAssignments.edit', ['observationAssignment' => $observationAssignment]);
    }

    /**
     * Update the specified resource in storage.
     *
     *
     * @param  ObservationAssignmentRequest  $request
     * @param  Course $course
     * @param ObservationAssignment $observationAssignment
     * @return RedirectResponse
     */
    public function update(ObservationAssignmentRequest $request, Course $course, ObservationAssignment $observationAssignment)
    {
        DB::transaction(function () use ($request, $course, $observationAssignment) {
            $data = $request->validated();

            $observationAssignment->update($data);
            $observationAssignment->participants()->detach(null);
            $observationAssignment->participants()->attach(array_filter(explode(',', $data['participants'])));
            $observationAssignment->blocks()->detach(null);
            $observationAssignment->blocks()->attach(array_filter(explode(',', $data['blocks'])));
            $observationAssignment->users()->detach(null);
            $observationAssignment->users()->attach(array_filter(explode(',', $data['users'])));
            $request->session()->flash('alert-success', __('t.views.admin.observation_assignments.edit_success'));
        });
        return Redirect::route('admin.observationAssignments', ['course' => $course->id]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param Course $course
     * @param  ObservationAssignment  $observationAssignment
     * @return RedirectResponse
     *
     */
    public function destroy(Request $request, Course $course, ObservationAssignment $observationAssignment)
    {
        $observationAssignment->delete();
        $request->session()->flash('alert-success', __('t.views.admin.observation_assignments.delete_success'));
        return Redirect::route('admin.observationAssignments', ['course' => $course->id]);

    }
}
