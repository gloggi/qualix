<?php

namespace App\Http\Middleware;

use App\Models\Leiter;
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
        $kurs = $request->route('kurs');
        if ($kurs) {
            /** @var User $user */
            $user = Auth::user();
            Leiter::where('kurs_id', '=', $kurs->id)->where('user_id', '=', $user->getAuthIdentifier())->firstOrFail();
            if ($kurs->id !== $user->lastAccessedKurs->id) {
                $user->kurse()->updateExistingPivot($kurs->id, ['last_accessed' => Carbon::now()]);
            }
        }

        return $next($request);
    }
}
