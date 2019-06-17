<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redirect;

class CourseMustNotBeArchived
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $course = $request->route('course');
        if ($course->archived) {
            $request->session()->flash('alert-danger', __('Dieser Kurs ist archiviert, die gewählte Aktion kann nicht durchgeführt werden.'));
            return Redirect::route('admin.course', ['course' => $course->id]);
        }
        return $next($request);
    }
}
