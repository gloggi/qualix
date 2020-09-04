<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\ObservationOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;


class ObservationOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('admin.observationOrders.index');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ObservationOrder  $observationOrder
     * @return \Illuminate\Http\Response
     */
    public function edit(ObservationOrder $observationOrder)
    {
        return view('admin.observationOrders.edit', ['observationOrder' => $observationOrder]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ObservationOrder  $observationOrder
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ObservationOrder $observationOrder)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param Course $course
     * @param  ObservationOrder  $observationOrder
     * @return RedirectResponse
     *
     */
    public function destroy(Request $request, Course $course, ObservationOrder $observationOrder)
    {
        $observationOrder->delete();
        $request->session()->flash('alert-success', __('t.views.admin.observation_orders.delete_success'));
        return Redirect::route('admin.observationOrders.index', ['course' => $course->id]);

    }
}
