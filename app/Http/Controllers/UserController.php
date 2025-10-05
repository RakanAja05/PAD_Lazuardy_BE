<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserStudentRequest;
use App\Models\Student;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateStudentRole(UpdateUserStudentRequest $request, User $user)
    {
        $validatedData = $request->validated();

        $userData = $request->only('name', 'gender', 'date_of_birth', 'telephone_number', 'profile_photo_url', 'latitude', 'longitude');
        $addressData = $request->only('province', 'city', 'subdistrict', 'street');
        $studentData = $request->only('parent', 'parent_telephone_number', );

        DB::beginTransaction();
        try {
            $user->update([
                'name' => $userData['name'],
                'gender' => $userData['gender'],
                'date_of_birth' => $userData['date_of_birth'],
                'telephone_number' => $userData['telephone_number'],
                'home_address' => $addressData,
                'profile_photo_url' => $userData['profile_photo_url'],
                'latitude' => $userData['latitude'],
                'longitude' => $userData['longitude'],
            ]);

            Student::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'parent' => $studentData['parent'],
                    'parent_telephone_number' => $studentData['parent_telephone_number'],
                ]
            );

            DB::commit();
        } catch(Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    public function updateTutorRole(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
