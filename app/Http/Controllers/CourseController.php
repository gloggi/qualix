<?php

namespace App\Http\Controllers;

use App\Http\Requests\CourseRequest;
use App\Models\Course;
use App\Models\Participant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller {

    /**
     * Redirect to a course-specific URL, based on stored state from the database
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function noCourse(Request $request) {
        /** @var User $user */
        $user = Auth::user();
        if (count($user->courses)) {
            $request->session()->reflash();
            return Redirect::route('index', ['course' => $user->lastAccessedCourse->id]);
        }
        return view('no-courses');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view('course-new');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CourseRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CourseRequest $request) {
        DB::transaction(function () use ($request) {
            Course::create($request->validated())->users()->attach(Auth::user()->getAuthIdentifier());
        });

        return Redirect::route('home');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit() {
        return view('admin.course-edit');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  CourseRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(CourseRequest $request, Course $course) {
        $course->update($request->validated());
        $request->session()->flash('alert-success', __('Kursdetails erfolgreich gespeichert.'));
        return Redirect::route('admin.course', ['course' => $course->id]);
    }

    /**
     * Permanently delete a resource and all its related entities from storage.
     *
     * @param Request $request
     * @param Course $course
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(Request $request, Course $course) {
        $participantImageUrls = $course->participants->map(function (Participant $participant) {
            return $participant->image_url;
        });
        // Because of the ON DELETE CASCADE on database constraints, this will also delete all related data
        $course->delete();
        // Perform the image deletion after database deletion, so that a failing image doesn't prevent the whole deletion operation.
        // This way, we risk having some stray images on the server in the worst case, which is better than preventing deletion of a course.
        foreach ($participantImageUrls as $participantImageUrl) {
            Storage::delete($participantImageUrl);
        }
        $request->session()->flash('alert-success', __('Kurs :name und alle damit verbundenen Daten wurden gelöscht.', ['name' => $course->name]));
        return Redirect::route('home');
    }

    /**
     * Permanently delete all related security sensitive data and mark the course as archived,
     * but leave categories, requirements and trainers in the course to be looked up in later courses.
     *
     * @param Request $request
     * @param Course $course
     * @return \Illuminate\Http\RedirectResponse
     */
    public function archive(Request $request, Course $course) {
        $participantImageUrls = $course->participants->map(function (Participant $participant) {
            return $participant->image_url;
        });
        // Because of the ON DELETE CASCADE on database constraints, this will also delete all related data like observations
        DB::transaction(function() use ($course) {
            $course->participants()->delete();
            $course->update(['archived' => true]);
        });
        // Perform the image deletion after database deletion, so that a failing image doesn't prevent the whole deletion operation.
        // This way, we risk having some stray images on the server in the worst case, which is better than preventing deletion of a course.
        foreach ($participantImageUrls as $participantImageUrl) {
            Storage::delete($participantImageUrl);
        }
        $request->session()->flash('alert-success', __('Kurs :name wurde archiviert.', ['name' => $course->name]));
        return Redirect::route('home');
    }
}
