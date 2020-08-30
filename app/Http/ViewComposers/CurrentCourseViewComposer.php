<?php

namespace App\Http\ViewComposers;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class CurrentCourseViewComposer {

    /**
     * Bind data to the view.
     *
     * @param View $view
     * @return void
     */
    public function compose(View $view) {
        $course = request()->route('course');

        if (!$course) {
            // This is accessed on routes like newcourse, which don't have a course id in the URL but still need the $course
            // in the views for displaying navigation etc.
            /** @var User $user */
            $user = Auth::user();
            if ($user && $user->courses()->count()) {
                $course = $user->lastAccessedCourse;
            }
        }

        $view->with('course', $course);
    }
}
