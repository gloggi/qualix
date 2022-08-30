<?php

namespace App\Http\Controllers;

use App\Http\Requests\RequirementStatusRequest;
use App\Models\Course;
use App\Models\Requirement;
use App\Models\RequirementStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class RequirementStatusController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {
        return view('admin.requirement_statuses.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param RequirementStatusRequest $request
     * @param Course $course
     * @return RedirectResponse
     */
    public function store(RequirementStatusRequest $request, Course $course) {
        DB::transaction(function () use ($request, $course) {
            $data = $request->validated();
            RequirementStatus::create(array_merge($data, ['course_id' => $course->id]));
            $request->session()->flash('alert-success', __('t.views.admin.requirement_statuses.create_success'));
        });
        return Redirect::route('admin.requirement_statuses', ['course' => $course->id]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Course $course
     * @param RequirementStatus $requirementStatus
     * @return Response
     */

    public function edit(Course $course, RequirementStatus $requirementStatus) {
        return view('admin.requirement_statuses.edit', ['requirementStatus' => $requirementStatus]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param RequirementStatusRequest $request
     * @param Course $course
     * @param RequirementStatus $requirementStatus
     * @return RedirectResponse
     */
    public function update(RequirementStatusRequest $request, Course $course, RequirementStatus $requirementStatus) {
        DB::transaction(function () use ($request, $course, $requirementStatus) {
            $data = $request->validated();
            $requirementStatus->update($data);
            $request->session()->flash('alert-success', __('t.views.admin.requirement_statuses.edit_success'));
        });
        return Redirect::route('admin.requirement_statuses', ['course' => $course->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param Course $course
     * @param Requirement $requirement
     * @return RedirectResponse
     */
    public function destroy(Request $request, Course $course, RequirementStatus $requirementStatus) {
        if ($course->requirement_statuses()->count() <= 1) {
            $request->session()->flash('alert-danger', __('t.views.admin.requirement_statuses.cannot_delete_the_last_requirement_status'));
        } else {
            $requirementStatus->delete();
            $request->session()->flash('alert-success', __('t.views.admin.requirement_statuses.delete_success'));
        }
        return Redirect::route('admin.requirement_statuses', ['course' => $course->id]);
    }
}
