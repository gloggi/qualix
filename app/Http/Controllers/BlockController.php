<?php

namespace App\Http\Controllers;

use App\Exceptions\ECamp2BlockOverviewParsingException;
use App\Exceptions\UnsupportedFormatException;
use App\Http\Requests\BlockGenerateRequest;
use App\Http\Requests\BlockImportRequest;
use App\Http\Requests\BlockRequest;
use App\Models\Block;
use App\Models\Course;
use App\Models\User;
use App\Util\HtmlString;
use Carbon\CarbonPeriod;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class BlockController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index() {
        return view('admin.blocks.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param BlockRequest $request
     * @param Course $course
     * @return RedirectResponse
     */
    public function store(BlockRequest $request, Course $course) {
        DB::transaction(function () use ($request, $course) {
            $data = $request->validated();
            $block = Block::create(array_merge($data, ['course_id' => $course->id]));
            $block->requirements()->sync(array_filter(explode(',', $data['requirements'])));
            $this->rememberBlockDate($data['block_date'], $course);
            $request->session()->flash('alert-success', __('t.views.admin.blocks.create_success', ['name' => $block->name]));
        });

        return Redirect::route('admin.blocks', ['course' => $course->id]);
    }

    /**
     * Display a form for uploading a list of blocks.
     *
     * @param Request $request
     * @param Course $course
     * @return View
     */
    public function upload(Request $request, Course $course) {
        if ($course->blocks()->exists() && !$request->session()->has('alert-warning')) {
            $request->session()->now('alert-warning', trans('t.views.admin.block_import.warning_existing_blocks'));
        }

        $ecamp2BlockOverviewLink = (new HtmlString)
            ->s('<a href="https://ecamp.pfadiluzern.ch/index.php?app=aim" target="_blank">')
            ->__('t.views.admin.block_import.ecamp2.name')
            ->s('</a>');
        return view('admin.blocks.import', ['ecamp2Link' => $ecamp2BlockOverviewLink]);
    }

    /**
     * Store an uploaded list of blocks in storage.
     *
     * @param BlockImportRequest $request
     * @param Course $course
     * @return RedirectResponse
     * @throws ValidationException if parsing the uploaded file fails
     */
    public function import(BlockImportRequest $request, Course $course) {
        $request->validated();

        try {
            $imported = $request->getImporter()->import($request->file('file')->getRealPath(), $course);
        } catch (ECamp2BlockOverviewParsingException $e) {
            throw ValidationException::withMessages(['file' => $e->getMessage()]);
        } catch (UnsupportedFormatException $e) {
            throw ValidationException::withMessages(['file' => trans('t.views.admin.block_import.error_unsupported_format')]);
        } catch (Exception $e) {
            report($e);
            return Redirect::back()->with('alert-danger', trans('t.views.admin.block_import.unknown_error'));
        }

        return Redirect::route('admin.blocks', ['course' => $course->id])->with('alert-success', trans_choice('t.views.admin.block_import.import_success', $imported));
    }

    /**
     * Display a form to generate blocks.
     *
     * @return View
     */
    public function generate(): View
    {
        return view('admin.blocks.generate');
    }

    /**
     * Store an generated list of blocks in storage.
     *
     * @param BlockGenerateRequest $request
     * @param Course $course
     * @return RedirectResponse
     */
    public function generateStore(BlockGenerateRequest $request, Course $course): RedirectResponse
    {
        $generated = DB::transaction(function () use ($request, $course) {
            $request->validated();
            $startDate = $request->date('blocks_startdate');
            $endDate = $request->date('blocks_enddate');
            $days = $startDate->diffInDays($endDate);
            if($days > 370) {
                throw ValidationException::withMessages(['blocks_enddate' => trans('t.views.admin.block_generate.error_too_many_blocks')]);
            }
            $result = collect([]);
            $data = $request->validated();

            foreach (CarbonPeriod::create($startDate, $endDate) as $date) {
                $block = Block::create(array_merge($data, [
                    'course_id' => $course->id,
                    'block_date' => $date,
                    'name' => $data['name'] . " - " . $date->format("d.m.y"),
                ]));
                $block->requirements()->sync(array_filter(explode(',', $data['requirements'])));
                $result->push($block);
            }
            $this->rememberBlockDate($endDate, $course);
            return $result;
        });

        return Redirect::route('admin.blocks', ['course' => $course->id])->with('alert-success', trans_choice('t.views.admin.block_generate.generate_success', $generated));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Course $course
     * @param Block $block
     * @return View
     */
    public function edit(Course $course, Block $block) {
        return view('admin.blocks.edit', ['block' => $block]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param BlockRequest $request
     * @param Course $course
     * @param Block $block
     * @return RedirectResponse
     */
    public function update(BlockRequest $request, Course $course, Block $block) {
        DB::transaction(function () use ($request, $course, $block) {
            $data = $request->validated();
            $block->update($data);
            $block->requirements()->sync(array_filter(explode(',', $data['requirements'])));
            $this->rememberBlockDate($data['block_date'], $course);
            $request->session()->flash('alert-success', __('t.views.admin.blocks.edit_success', ['name' => $block->name]));
        });
        return Redirect::route('admin.blocks', ['course' => $course->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param Course $course
     * @param Block $block
     * @return RedirectResponse
     */
    public function destroy(Request $request, Course $course, Block $block) {
        $block->delete();
        $request->session()->flash('alert-success', __('t.views.admin.blocks.delete_success', ['name' => $block->name]));
        return Redirect::route('admin.blocks', ['course' => $course->id]);
    }

    protected function rememberBlockDate($date, $course) {
        /** @var User $user */
        $user = Auth::user();
        $user->setLastUsedBlockDate($date, $course);
    }
}
