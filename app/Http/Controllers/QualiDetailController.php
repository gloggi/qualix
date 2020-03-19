<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Participant;
use App\Models\Quali;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QualiDetailController extends Controller {
    /**
     * @param Request $request
     * @param Course $course
     * @param Participant $participant
     * @param Quali $quali
     * @return View
     */
    public function index(Request $request, Course $course, Participant $participant, Quali $quali) {
        return view('quali-detail', ['participant' => $participant, 'quali' => $quali]);
    }
}
