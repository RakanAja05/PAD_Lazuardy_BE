<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $userToUpdate = $this->route('user'); 

        if (!$userToUpdate instanceof User) {
            return false;
        }

        $isOwner = $this->user()->id === $userToUpdate->id;
        $isStudent = $this->user()->role === 'student'; 

        return $isOwner && $isStudent;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [            
            'class_id' => [
                'nullable', 
                'integer', 
                'exists:classes,id'
            ],
            
            'major_id' => [
                'nullable', 
                'integer', 
                'exists:majors,id'
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
                // 'regex:/^(\+62|0)\d{9,15}$/',
            ],
        ];
    }
}
