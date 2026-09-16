<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MenuRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'date' => [
                'required',
                'date',
                Rule::unique('menus','date')->ignore($this->menu)
            ],

            'categories' => [
                'required',
                'array',
                'min:1',
            ],

            'categories.*' => [
                'integer',
                'exists:categories,id',
            ],

            'products' => [
                'required',
                'array',
                'min:1',
            ],

            'products.*.product_id' => [
                'required',
                'integer',
                'exists:products,id',
            ],

            'products.*.price' => [
                'required',
                'numeric',
                'min:0',
            ],

            'products.*.is_available' => [
                'sometimes',
                'boolean',
            ],
        ];
    }
}
