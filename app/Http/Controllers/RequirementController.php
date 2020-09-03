<?php

namespace App\Http\Controllers;

use App\Http\Requests\RequirementRequest;
use App\Models\Course;
use App\Models\Requirement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class RequirementController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {
        return view('admin.requirements.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param RequirementRequest $request
     * @param Course $course
     * @return RedirectResponse
     */
    public function store(RequirementRequest $request, Course $course) {
        DB::transaction(function () use ($request, $course) {
            $data = $request->validated();
            $requirement = Requirement::create(array_merge($data, ['course_id' => $course->id]));

            $requirement->blocks()->attach(array_filter(explode(',', $data['blocks'])));

            $request->session()->flash('alert-success', __('t.views.admin.requirements.create_success'));
        });
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
        return view('admin.requirements.edit', ['requirement' => $requirement]);
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
        DB::transaction(function () use ($request, $course, $requirement) {
            $data = $request->validated();
            $requirement->update($data);

            $requirement->blocks()->detach(null);
            $requirement->blocks()->attach(array_filter(explode(',', $data['blocks'])));
            $request->session()->flash('alert-success', __('t.views.admin.requirements.edit_success'));
        });
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
