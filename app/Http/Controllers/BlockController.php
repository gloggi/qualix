<?php

namespace App\Http\Controllers;

use App\Http\Requests\BlockRequest;
use App\Models\Block;
use App\Models\Kurs;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class BlockController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {
        return view('admin.bloecke');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param BlockRequest $request
     * @param Kurs $kurs
     * @return RedirectResponse
     */
    public function store(BlockRequest $request, Kurs $kurs) {
        $data = $request->validated();
        Block::create(array_merge($data, ['kurs_id' => $kurs->id]));

        /** @var User $user */
        $user = Auth::user();
        $user->setLastUsedBlockDate($data['datum'], $kurs);

        return Redirect::route('admin.bloecke', ['kurs' => $kurs->id]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Kurs $kurs
     * @param Block $block
     * @return Response
     */
    public function edit(Kurs $kurs, Block $block) {
        return view('admin.block-edit', ['block' => $block]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param BlockRequest $request
     * @param Kurs $kurs
     * @param Block $block
     * @return RedirectResponse
     */
    public function update(BlockRequest $request, Kurs $kurs, Block $block) {
        $data = $request->validated();
        $block->update($data);

        /** @var User $user */
        $user = Auth::user();
        $user->setLastUsedBlockDate($data['datum'], $kurs);

        $request->session()->flash('alert-success', __('Block erfolgreich gespeichert.'));
        return Redirect::route('admin.bloecke', ['kurs' => $kurs->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param Kurs $kurs
     * @param Block $block
     * @return RedirectResponse
     */
    public function destroy(Request $request, Kurs $kurs, Block $block) {
        $block->delete();
        $request->session()->flash('alert-success', __('Block erfolgreich gelöscht.'));
        return Redirect::route('admin.bloecke', ['kurs' => $kurs->id]);
    }
}
