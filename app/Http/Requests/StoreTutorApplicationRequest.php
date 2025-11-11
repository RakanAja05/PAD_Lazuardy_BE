<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTutorApplicationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'experience' => [
                'required',
                'string',
            ],
            
            'organization' => [
                'required',
                'array',
            ],

            // Tutor-subject
            'subject_ids' => [
                'required',
                'array',
            ],

            'subject_ids.*' => [
                'exists:subjects,id'
            ],
            
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
        ];
    }
}
