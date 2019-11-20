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
     * @return Response
     */
    public function index()
    {
        return view('blocks');
    }

    /**
     * Display the crib page which visualizes connections between blocks and requirements.
     *
     * @return Response
     */
    public function crib(Course $course)
    {
        return view('crib', ['blockManagementLink' => $this->blockManagementLink($course)]);
    }

    private function blockManagementLink(Course $course) {
        return (new HtmlString())
            ->s('<a href="' . route('admin.blocks', ['course' => $course->id]) . '">')
            ->e(__('t.views.crib.here'))
            ->s('</a>');
    }
}
