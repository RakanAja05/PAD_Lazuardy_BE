<?php

namespace App\Http\Controllers;

use App\Models\TakenSchedule;
use App\Http\Requests\StoreTakenScheduleRequest;
use App\Http\Requests\UpdateTakenScheduleRequest;

class TakenScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTakenScheduleRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(TakenSchedule $takenSchedule)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTakenScheduleRequest $request, TakenSchedule $takenSchedule)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TakenSchedule $takenSchedule)
    {
        //
    }
}
