<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class StoreTutorRequest extends FormRequest
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
        $isTutor = $this->user()->role === 'tutor'; 

        return $isOwner && $isTutor;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'education' => [
                'nullable',
                'string',
            ],
            'salary' => [
                'nullable',
                'integer',
                'min:0',
            ],
            'price' => [
                'nullable',
                'integer',
                'min:0',
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'description' => [
                'nullable',
                'string',
            ],
        ];
    }
}
