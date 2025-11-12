<?php

namespace App\Http\Requests;

use App\Enums\DayEnum;
use App\Enums\RoleEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateTutorLessonMethodRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->role === RoleEnum::TUTOR->value;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'course_mode' => ['required', 'string', 'max:50'],
            'description' => ['required', 'string', 'max:1000'],
            'qualification' => ['required', 'string', 'max:500'],
            'learning_method' => ['required', 'string', 'max:50'],

            'schedules' => ['required', 'array', 'min:0'],
            'schedules.*.day' => [
                'required', 
                'string',
                new Enum(DayEnum::class)
            ],
            'schedules.*.time' => ['required', 'date_format:H:i'], 
        ];
    }
}
