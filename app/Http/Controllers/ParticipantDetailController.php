<?php

namespace App\Http\Controllers;

use App\Models\Observation;
use App\Models\Course;
use App\Models\Requirement;
use App\Models\Category;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ParticipantDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param Course $course
     * @param Participant $tn
     * @return Response
     */
    public function index(Request $request, Course $course, Participant $tn)
    {
        $observations = $tn->observations;

        $ma = $request->input('ma');
        if ($ma != null) {
            if ($ma === '0') {
                $observations = $observations->filter(function (Observation $observation) {
                    return $observation->requirements->isEmpty();
                });
            } else {
                $observations = $observations->filter(function (Observation $observation) use ($ma) {
                    return $observation->requirements->map(function (Requirement $ma) {
                        return $ma->id;
                    })->contains($ma);
                });
            }
        }

        $qk = $request->input('qk');
        if ($qk != null) {
            if ($qk === '0') {
                $observations = $observations->filter(function (Observation $observation) {
                    return $observation->categories->isEmpty();
                });
            } else {
                $observations = $observations->filter(function (Observation $observation, $key) use ($qk) {
                    return $observation->categories->map(function (Category $qk) {
                        return $qk->id;
                    })->contains($qk);
                });
            }
        }

        $observations = $observations->sortBy(function (Observation $observation) { return $observation->block->block_date->timestamp . '.' . $observation->block->day_number . '.' . $observation->block->block_number . '.' . $observation->block->name . '.' . $observation->block->id; });

        return view('tn-detail', ['tn' => $tn, 'observations' => $observations, 'ma' => $ma, 'qk' => $qk]);
    }
}
