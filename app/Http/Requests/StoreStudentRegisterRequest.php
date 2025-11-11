<?php

namespace App\Http\Requests;

use App\Enums\GenderEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreStudentRegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Since the route no longer includes {user}, authorize only checks authentication.
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return 
        [
            // tabel user
            'email' => ['required','string','email','max:255','unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'gender' => ['required', new Enum(GenderEnum::class)],

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
            
            'province' => ['required', 'string', 'min:2', 'max:255'],
            'regency' => ['required', 'string', 'min:2', 'max:255'],
            'district' => ['required', 'string', 'min:2', 'max:255'],
            'subdistrict' => ['required', 'string', 'min:2', 'max:255'],
            'street' => ['required', 'string', 'min:2', 'max:255'],
            
            'latitude' => [
                'required', 
                'numeric', 
                'between:-90,90', 
            ],

            'longitude' => [
                'required', 
                'numeric', 
                'between:-180,180', 
            ],

            // Tabel Student
            'class_id' => [
                'nullable', 
                'integer', 
                'exists:classes,id'
            ],

            'curriculum_id' => [
                'nullable',
                'integer',
                'exists:curriculums,id'
            ],

            'school' => ['nullable', 'string', 'min:2', 'max:100'],
            'parent' => ['nullable', 'string', 'min:2', 'max:255'],

            'parent_telephone_number' => [
                'nullable', 
                'string', 
                'max:15',
            ],
        ];
    }
}
