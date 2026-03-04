<?php

namespace App\Http\Requests;

use App\Enums\ClassEnum;
use App\Enums\GenderEnum;
use App\Enums\ReligionEnum;
use App\Models\ClassModel;
use App\Models\Curriculum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreStudentRegisterRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $classValue = $this->input('class_id');
        if (!is_null($classValue)) {
            if (is_numeric($classValue)) {
                $classId = ClassModel::whereKey($classValue)->value('id');
                if (!$classId) {
                    $classList = ClassEnum::list();
                    $index = (int) $classValue - 1;
                    if (isset($classList[$index])) {
                        $classId = ClassModel::whereRaw('LOWER(name) = ?', [strtolower($classList[$index])])
                            ->value('id');
                    }
                }
                if ($classId) {
                    $this->merge(['class_id' => $classId]);
                }
            } else {
                $classId = ClassModel::whereRaw('LOWER(name) = ?', [strtolower($classValue)])
                    ->value('id');
                if ($classId) {
                    $this->merge(['class_id' => $classId]);
                }
            }
        }

        $curriculumValue = $this->input('curriculum_id');
        if (!is_null($curriculumValue)) {
            if (is_numeric($curriculumValue)) {
                $curriculumId = Curriculum::whereKey($curriculumValue)->value('id');
                if (!$curriculumId) {
                    $curriculumId = Curriculum::orderBy('id')
                        ->skip(((int) $curriculumValue) - 1)
                        ->value('id');
                }
                if ($curriculumId) {
                    $this->merge(['curriculum_id' => $curriculumId]);
                }
            } else {
                $curriculumId = Curriculum::whereRaw('LOWER(name) = ?', [strtolower($curriculumValue)])
                    ->value('id');
                if ($curriculumId) {
                    $this->merge(['curriculum_id' => $curriculumId]);
                }
            }
        }
    }

    public function authorize(): bool
    {
        // Since the route no longer includes {user}, authorize only checks authentication.
        return $this->user() === null;
    }

    public function rules(): array
    {
        return
        [
            // tabel user
            'email' => ['required','string','email','max:255','unique:users'],
            'password' => ['required', 'string', 'min:8'],
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

            'profile_photo' => [
                'nullable',
                'file',
                'mimes:png,jpg, pdf, svg, webp',
            ],

            'province' => ['required', 'string', 'min:2', 'max:255'],
            'regency' => ['required', 'string', 'min:2', 'max:255'],
            'district' => ['required', 'string', 'min:2', 'max:255'],
            'subdistrict' => ['required', 'string', 'min:2', 'max:255'],
            'street' => ['required', 'string', 'min:2', 'max:255'],
            'religion' => ['nullable', new Enum(ReligionEnum::class)],

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
