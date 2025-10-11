<?php

namespace App\Http\Requests;

use App\Enums\Gender;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateUserRequest extends FormRequest
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
        return $isOwner;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            
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

            'religion' => [
                'required',
                'string',
                'min:2',
                'max:30'
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
