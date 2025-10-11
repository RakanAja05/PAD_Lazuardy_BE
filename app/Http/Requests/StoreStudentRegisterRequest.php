<?php

namespace App\Http\Requests;

use App\Enums\Gender;
use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreStudentRegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        $userToUpdate = $this->route('user'); 

        if (!$userToUpdate instanceof User) {
            return false;
        }

        $isOwner = $this->user()->id === $userToUpdate->id;
        $isStudent = $this->user()->role === Role::STUDENT->value; 

        return $isOwner && $isStudent;
    }

    public function rules(): array
    {
        return 
        [
            // tabel user
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
                // 'regex:/^(\+62|0)\d{9,15}$/',
            ],
            
            'profile_photo_url' => [
                'nullable',
                'url',
                // 'regex:/^(http(s)?:\/\/)?([\w-]+\.)+[\w-]+(\/[\w-.\/?%&=]*)?\.(jpg|jpeg|png|gif|webp)$/i',
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
                // 'regex:/^-?\d{1,2}\.\d{8}$/',
            ],

            'longitude' => [
                'required', 
                'numeric', 
                'between:-180,180', 
                // 'regex:/^-?\d{1,3}\.\d{8}$/', 
            ],

            // Tabel Student
            'class_id' => [
                'nullable', 
                'integer', 
                'exists:classes,id'
            ],

            'school' => ['nullable', 'string', 'min:2', 'max:100'],
            'parent' => ['nullable', 'string', 'min:2', 'max:255'],

            'parent_telephone_number' => [
                'nullable', 
                'string', 
                'max:15',
                // 'regex:/^(\+62|0)\d{9,15}$/',
            ],
        ];
    }
}
