<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Util\HtmlString;
use Illuminate\Http\Response;

class ParticipantListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Course $course
     * @return Response
     */
    public function index(Course $course)
    {
        return view('participants', ['participantManagementLink' => $this->participantManagementLink($course, 't.views.participants.here')]);
    }

    /**
     * Creates a link to the participants management page with the text given through a translation key.
     *
     * @param Course $course
     * @param $translationKey
     * @return HtmlString
     */
    protected function participantManagementLink(Course $course, $translationKey) {
        return (new HtmlString)
            ->s('<a href="' . route('admin.participants', ['course' => $course->id]) . '">')
            ->__($translationKey)
            ->s('</a>');
    }
}
