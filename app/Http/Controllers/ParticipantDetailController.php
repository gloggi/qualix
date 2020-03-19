<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Course;
use App\Models\Observation;
use App\Models\Participant;
use App\Models\Quali;
use App\Models\Requirement;
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
        $observations = $participant->observations;

        $requirement = $request->input('requirement');
        if ($requirement != null) {
            if ($requirement === '0') {
                $observations = $observations->filter(function (Observation $observation) {
                    return $observation->requirements->isEmpty();
                });
            } else {
                $observations = $observations->filter(function (Observation $observation) use ($requirement) {
                    return $observation->requirements->map(function (Requirement $observationRequirement) {
                        return $observationRequirement->id;
                    })->contains($requirement);
                });
            }
        }

        $category = $request->input('category');
        if ($category != null) {
            if ($category === '0') {
                $observations = $observations->filter(function (Observation $observation) {
                    return $observation->categories->isEmpty();
                });
            } else {
                $observations = $observations->filter(function (Observation $observation, $key) use ($category) {
                    return $observation->categories->map(function (Category $observationCategory) {
                        return $observationCategory->id;
                    })->contains($category);
                });
            }
        }

        $observations = $observations->sortBy(function (Observation $observation) {
            return $observation->block->block_date->timestamp . '.' . $observation->block->day_number . '.' . $observation->block->block_number . '.' . $observation->block->name . '.' . $observation->block->id;
        });

        return view('participant-detail', ['participant' => $participant, 'observations' => $observations, 'requirement' => $requirement, 'category' => $category]);
    }
}
