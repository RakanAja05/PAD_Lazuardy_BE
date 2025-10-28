<?php

namespace App\Http\Requests;

use App\Enums\GenderEnum;
use App\Enums\ReligionEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateStudentProfileRequest extends FormRequest
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
        return [
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'telephone_number' => ['required','string','max:15'],
            'profile_photo_url' => ['required', 'string'],
            'gender' => ['required', new Enum(GenderEnum::class)],
            'date_of_birth' => [
                'required', 
                'date', 
                'date_format:Y-m-d', 
                'before_or_equal:today'
            ],
            'religion' => ['required', new Enum(ReligionEnum::class)],
            
            'province' => ['required', 'string', 'min:2', 'max:255'],
            'regency' => ['required', 'string', 'min:2', 'max:255'],
            'district' => ['required', 'string', 'min:2', 'max:255'],
            'subdistrict' => ['required', 'string', 'min:2', 'max:255'],
            'street' => ['required', 'string', 'min:2', 'max:255'],
            
            'school' =>  ['required', 'string', 'min:2', 'max:255'],
            'class_id' =>  ['nullable', 'integer', 'exists:classes,id'],
            'curriculum_id' =>  ['nullable', 'integer', 'exists:curriculums,id'],
            'parent' => ['nullable', 'string', 'min:2', 'max:255'],
            'parent_telephone_number' => ['nullable', 'string', 'max:15',]
        ];
    }
}
