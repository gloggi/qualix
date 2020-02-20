<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use App\Models\Course;
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
        return view('admin.categories.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CategoryRequest $request
     * @param Course $course
     * @return RedirectResponse
     */
    public function store(CategoryRequest $request, Course $course) {
        $category = Category::create(array_merge($request->validated(), ['course_id' => $course->id]));
        $request->session()->flash('alert-success', __('t.views.admin.categories.create_success', ['name' => $category->name]));
        return Redirect::route('admin.categories', ['course' => $course->id]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Course $course
     * @param Category $category
     * @return Response
     */
    public function edit(Course $course, Category $category) {
        return view('admin.categories.edit', ['category' => $category]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param CategoryRequest $request
     * @param Course $course
     * @param Category $category
     * @return RedirectResponse
     */
    public function update(CategoryRequest $request, Course $course, Category $category) {
        $category->update($request->validated());
        $request->session()->flash('alert-success', __('t.views.admin.categories.edit_success', ['name' => $category->name]));
        return Redirect::route('admin.categories', ['course' => $course->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param Course $course
     * @param Category $category
     * @return RedirectResponse
     */
    public function destroy(Request $request, Course $course, Category $category) {
        $category->delete();
        $request->session()->flash('alert-success', __('t.views.admin.categories.delete_success', ['name' => $category->name]));
        return Redirect::route('admin.categories', ['course' => $course->id]);
    }
}
