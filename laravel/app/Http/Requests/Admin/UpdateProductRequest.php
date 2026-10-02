<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
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
            'category_id' => [
                'sometimes',
                'exists:categories,id'
            ],
            'name' => [
                'string',
                'sometimes',
                Rule::unique('products', 'name')
                    ->ignore($this->product), // $this->product refer to $product inside controller
                                                //     public function update(
                                                //          UpdateProductRequest $request,
                                                //          Product $product
                                                //     ){}
            ],
            'description' => [
                'sometimes',
                'string'
            ],
            'image' => [
                'sometimes',
                'image',
                'file',
                'mimes:jpg,png,jpeg'
            ],
            'is_active' => [
                'sometimes',
                'boolean'
            ]
        ];
    }
}
