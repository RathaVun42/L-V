<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CanteenSettingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'pickup_start_time' => [
                'required',
                'date_format:H:i',
            ],

            'pickup_end_time' => [
                'required',
                'date_format:H:i',
                'after:pickup_start_time',
            ],

            'slot_duration' => [
                'required',
                'integer',
                'min:10',
            ],

            'slot_capacity' => [
                'required',
                'integer',
                'min:10',
            ],

            'monday' => ['boolean'],
            'tuesday' => ['boolean'],
            'wednesday' => ['boolean'],
            'thursday' => ['boolean'],
            'friday' => ['boolean'],
            'saturday' => ['boolean'],
            'sunday' => ['boolean'],
        ];
    }
}
