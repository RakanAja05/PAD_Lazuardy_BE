<?php

namespace App\Http\Requests;

use App\Enums\OtpTypeEnum;
use Illuminate\Foundation\Http\FormRequest;

class UpdateOtpRequest extends FormRequest
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
            'identifier' => 'nullable|string',
            'identifier_type' => 'required|in:' . implode(',', OtpTypeEnum::list()),
            'verification_type' => 'required|string',
            'code' => 'required|string',
        ];
    }
}
