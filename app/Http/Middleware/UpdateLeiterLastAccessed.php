<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class UpdateLeiterLastAccessed
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
        if ($course) {
            /** @var User $user */
            $user = Auth::user();
            if ($course->id !== $user->lastAccessedCourse->id) {
                $user->courses()->updateExistingPivot($course->id, ['last_accessed' => Carbon::now()]);
            }
        }

        return $next($request);
    }
}
