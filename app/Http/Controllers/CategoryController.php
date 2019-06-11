<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\Course;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redirect;

class CategoryController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {
        return view('admin.categories');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CategoryRequest $request
     * @param Course $course
     * @return RedirectResponse
     */
    public function store(CategoryRequest $request, Course $course) {
        Category::create(array_merge($request->validated(), ['course_id' => $course->id]));
        return Redirect::route('admin.categories', ['course' => $course->id]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Course $course
     * @param Category $qk
     * @return Response
     */
    public function edit(Course $course, Category $qk) {
        return view('admin.category-edit', ['qk' => $qk]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param CategoryRequest $request
     * @param Course $course
     * @param Category $qk
     * @return RedirectResponse
     */
    public function update(CategoryRequest $request, Course $course, Category $qk) {
        $qk->update($request->validated());
        $request->session()->flash('alert-success', __('Qualikategorie erfolgreich gespeichert.'));
        return Redirect::route('admin.categories', ['course' => $course->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param Course $course
     * @param Category $qk
     * @return RedirectResponse
     */
    public function destroy(Request $request, Course $course, Category $qk) {
        $qk->delete();
        $request->session()->flash('alert-success', __('Qualikategorie erfolgreich gelöscht.'));
        return Redirect::route('admin.categories', ['course' => $course->id]);
    }
}
