<?php

namespace App\Http\Requests;

use App\Enums\CourseMode;
use App\Enums\Gender;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreTutorRegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        
        // $userToUpdate = $this->route('user'); 

        // if (!$userToUpdate instanceof User) {
        //     return false;
        // }

        // $isOwner = $this->user()->id === $userToUpdate->id;
        // $isTutor = $this->user()->role === Role::TUTOR->value; 

        // return $isOwner && $isTutor;
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Update User
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'gender' => ['required', new Enum(Gender::class)],

            'date_of_birth' => [
                'required', 
                'date', 
                'date_format:Y-m-d', 
                'before_or_equal:today'
            ],
            
            'telephone_number' => [
                'required', 
                'string', 
                'max:15',
            ],
            
            'profile_photo_url' => [
                'nullable',
                'url',
            ],

            'religion' => [
                'nullable',
                'string',
                'min:2',
                'max:30'
            ],
            
            'province' => ['required', 'string', 'min:2', 'max:255'],
            'regency' => ['required', 'string', 'min:2', 'max:255'],
            'district' => ['required', 'string', 'min:2', 'max:255'],
            'subdistrict' => ['required', 'string', 'min:2', 'max:255'],
            'street' => ['required', 'string', 'min:2', 'max:255'],
            
            'latitude' => [
                'nullable', 
                'numeric', 
                'between:-90,90', 
            ],

            'longitude' => [
                'nullable', 
                'numeric', 
                'between:-180,180', 
            ],

            // Create Tutor
            'experience' => [
                'nullable',
                'string',
            ],

            'organization' => [
                'nullable',
                'array',
            ],

            'course_mode' => ['string', new Enum(CourseMode::class)],

            'description' => [
                'nullable',
                'string',
            ],
            
            'qualification' => [
                'nullable',
                'array',
            ],

            'learning_method' => [
                'nullable',
                'string',
            ],

            // Tutor-subject
            'subject_ids' => [
                'required',
                'array',
            ],

            'subject_ids.*' => [
                'exists:subjects,id'
            ],

            // files
            'cv' => ['nullable', 'array'],
            'cv.*.name' => ['required', 'string', 'max:255'],
            'cv.*.path_url' => ['required', 'string', 'url'],
            
            'ktp' => ['nullable', 'array'],
            'ktp.*.name' => ['required', 'string', 'max:255'],
            'ktp.*.path_url' => ['required', 'string', 'url'],
            
            'ijazah' => ['nullable', 'array'],
            'ijazah.*.name' => ['required', 'string', 'max:255'],
            'ijazah.*.path_url' => ['required', 'string', 'url'],
            
            'certificate' => ['nullable', 'array'],
            'certificate.*.name' => ['required', 'string', 'max:255'],
            'certificate.*.path_url' => ['required', 'string', 'url'],
            
            'portofolio' => ['nullable', 'array'],
            'portofolio.*.name' => ['required', 'string', 'max:255'],
            'portofolio.*.path_url' => ['required', 'string', 'url'],

            // Schedule
            'schedules' => ['nullable', 'array'],
            'schedules.*.day' => ['required', 'string'],
            'schedules.*.time' => ['required', 'date_format:H:i'],
        ];
    }
}
