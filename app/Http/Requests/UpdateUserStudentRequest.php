<?php

namespace App\Http\Requests;

use App\Enums\Gender;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateUserStudentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        $userId = $this->route('user')->id;

        return [
            
            'name' => ['required', 'string', 'min:2', 'max:255'],

            // 'class_id' => [
            //     'required', 
            //     'integer', 
            //     'exists:classes,id'
            // ],

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

            'parent' => ['required', 'string', 'min:2', 'max:255'],

            'parent_telephone_number' => [
                'required', 
                'string', 
                'max:15',
                // 'regex:/^(\+62|0)\d{9,15}$/',
            ],

            'province' => ['required', 'string', 'min:2', 'max:255'],
            'city' => ['required', 'string', 'min:2', 'max:255'],
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
        ];
    }
}
