<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use App\Util\HtmlString;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class BlockListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Course $course
     * @return Response
     */
    public function index(Course $course)
    {
        return view('blocks', ['blockManagementLink' => $this->blockManagementLink($course, 't.views.blocks.here')]);
    }

    /**
     * Display the crib page which visualizes connections between blocks and requirements, as well as observation assignments.
     *
     * @param Course $course
     * @param User $user
     * @return Response
     */
    public function crib(Course $course, User $user)
    {
        $userId = $user->id ?? Auth::id();
        return view('crib', [
            'blockManagementLink' => $this->blockManagementLink($course, 't.views.crib.here'),
            'showObservationAssignments' => $course->observationAssignments()->count(),
            'userId' => $userId,
            'trainerObservationAssignments' => $course->observationAssignmentsPerUserAndPerBlock()[$userId] ?? [],
            'neededObservations' => 1,
        ]);
    }

    /**
     * Creates a link to the block management page with the text given through a translation key.
     *
     * @param Course $course
     * @param string $translationKey
     * @return HtmlString
     */
    private function blockManagementLink(Course $course, string $translationKey) {
        return (new HtmlString)
            ->s('<a href="' . route('admin.blocks', ['course' => $course->id]) . '">')
            ->__($translationKey)
            ->s('</a>');
    }
}
