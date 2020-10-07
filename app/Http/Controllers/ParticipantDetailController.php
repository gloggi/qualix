<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Observation;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ParticipantDetailController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param Course $course
     * @param Participant $participant
     * @return Response
     */
    public function index(Request $request, Course $course, Participant $participant) {
        $observations = $participant->observations->sortBy(function (Observation $observation) {
            return $observation->block->block_date->timestamp . '.' . $observation->block->day_number . '.' . $observation->block->block_number . '.' . $observation->block->name . '.' . $observation->block->id;
        })->values();

        return view('participant-detail', ['participant' => $participant, 'observations' => $observations, 'qualis_using_observations' => $course->qualis_using_observations]);
    }
}
