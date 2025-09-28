<?php

namespace App\Http\Controllers;

use App\Models\ClassStudent;
use App\Http\Requests\StoreClassStudentRequest;
use App\Http\Requests\UpdateClassStudentRequest;

class ClassStudentController extends Controller
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
    public function store(StoreClassStudentRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(ClassStudent $classStudent)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClassStudentRequest $request, ClassStudent $classStudent)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ClassStudent $classStudent)
    {
        //
    }
}
