<?php

namespace App\Http\ViewComposers;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class CurrentKursViewComposer {

    /**
     * Bind data to the view.
     *
     * @param View $view
     * @return void
     */
    public function compose(View $view) {
        $kurs = request()->route('kurs');

        if (!$kurs) {
            /** @var User $user */
            $user = Auth::user();
            if ($user && count($user->kurse)) {
                $kurs = $user->lastAccessedKurs;
            }
        }

        $view->with('kurs', $kurs);
    }
}
