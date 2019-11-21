<?php

namespace App\Http\Controllers;

use App\Http\Requests\RequirementRequest;
use App\Models\Course;
use App\Models\Requirement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redirect;

class RequirementController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {
        return view('admin.requirements');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param RequirementRequest $request
     * @param Course $course
     * @return RedirectResponse
     */
    public function store(RequirementRequest $request, Course $course) {
        Requirement::create(array_merge($request->validated(), ['course_id' => $course->id]));
        $request->session()->flash('alert-success', __('t.views.admin.new_course.create_success'));
        return Redirect::route('admin.requirements', ['course' => $course->id]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Course $course
     * @param Requirement $requirement
     * @return Response
     */
    public function edit(Course $course, Requirement $requirement) {
        return view('admin.requirement-edit', ['requirement' => $requirement]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param RequirementRequest $request
     * @param Course $course
     * @param Requirement $requirement
     * @return RedirectResponse
     */
    public function update(RequirementRequest $request, Course $course, Requirement $requirement) {
        $requirement->update($request->validated());
        $request->session()->flash('alert-success', __('t.views.admin.requirements.edit_success'));
        return Redirect::route('admin.requirements', ['course' => $course->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param Course $course
     * @param Requirement $requirement
     * @return RedirectResponse
     */
    public function destroy(Request $request, Course $course, Requirement $requirement) {
        $requirement->delete();
        $request->session()->flash('alert-success', __('t.views.admin.requirements.delete_success'));
        return Redirect::route('admin.requirements', ['course' => $course->id]);
    }
}
