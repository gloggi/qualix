<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Util\HtmlString;
use Illuminate\Http\Response;

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
     * Display the crib page which visualizes connections between blocks and requirements.
     *
     * @param Course $course
     * @return Response
     */
    public function crib(Course $course)
    {
        return view('crib', ['blockManagementLink' => $this->blockManagementLink($course, 't.views.crib.here')]);
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
            ->e(__($translationKey))
            ->s('</a>');
    }
}
