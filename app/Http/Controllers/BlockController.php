<?php

namespace App\Http\Controllers;

use App\Http\Requests\BlockRequest;
use App\Models\Block;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class BlockController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {
        return view('admin.blocks');
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

            $block->requirements()->attach(array_filter(explode(',', $data['requirement_ids'])));

            /** @var User $user */
            $user = Auth::user();
            $user->setLastUsedBlockDate($data['block_date'], $course);
        });

        return Redirect::route('admin.blocks', ['course' => $course->id]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Course $course
     * @param Block $block
     * @return Response
     */
    public function edit(Course $course, Block $block) {
        return view('admin.block-edit', ['block' => $block]);
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

            $block->requirements()->detach(null);
            $block->requirements()->attach(array_filter(explode(',', $data['requirement_ids'])));

            /** @var User $user */
            $user = Auth::user();
            $user->setLastUsedBlockDate($data['block_date'], $course);

            $request->session()->flash('alert-success', __('Block erfolgreich gespeichert.'));
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
        $request->session()->flash('alert-success', __('Block erfolgreich gelöscht.'));
        return Redirect::route('admin.blocks', ['course' => $course->id]);
    }
}
