<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\ObservationOrder;
use App\Http\Requests\ObservationOrderRequest;
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
        return view('admin.observationOrders');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  ObservationOrderRequest $request
     * @param Course $course

     * @return RedirectResponse
     */
    public function store(ObservationOrderRequest $request, Course $course)
    {
        $data = $request->validated();

        DB::transaction(function() use ($request,$course, $data){

            $observationOrder = ObservationOrder::create(array_merge($data, ['course_id' => $course->id]));

            $observationOrder->participants()->attach(array_filter(explode(',', $data['participants'])));
            $observationOrder->blocks()->attach(array_filter(explode(',', $data['block'])));
            $observationOrder->users()->attach(array_filter(explode(',', $data['user'])));

            $request->session()->flash('alert-success', __('t.views.admin.observation_orders.create_success'));
        });

        return Redirect::route('admin.observationOrders.index', ['course' => $course->id]);

    }


    /**
     * Show the form for editing the specified resource.
     * @param Course $course
     * @param  \App\Models\ObservationOrder  $observationOrder
     * @return \Illuminate\Http\Response
     */
    public function edit(Course $course ,ObservationOrder $observationOrder)
    {
        return view('admin.observationOrders.edit', ['observationOrder' => $observationOrder]);
    }

    /**
     * Update the specified resource in storage.
     *
     *
     * @param  ObservationOrderRequest  $request
     * @param  Course $course
     * @param  \App\Models\ObservationOrder  $observationOrder
     * @return RedirectResponse
     */
    public function update(ObservationOrderRequest $request, Course $course, ObservationOrder $observationOrder)
    {
        DB::transaction(function () use ($request, $course, $observationOrder) {
            $data = $request->validated();
            dd($observationOrder);

            $observationOrder->update($data);
            $observationOrder->participants()->detach(null);
            $observationOrder->participants()->attach(array_filter(explode(',', $data['participants'])));
            $observationOrder->blocks()->detach(null);
            $observationOrder->blocks()->attach(array_filter(explode(',', $data['block'])));
            $observationOrder->users()->detach(null);
            $observationOrder->users()->attach(array_filter(explode(',', $data['user'])));
            $request->session()->flash('alert-success', __('t.views.admin.observation_order.edit_success'));
        });
        return Redirect::route('admin.observationOrders.index', ['course' => $course->id]);

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
        return Redirect::route('admin.observationOrders', ['course' => $course->id]);

    }
}
