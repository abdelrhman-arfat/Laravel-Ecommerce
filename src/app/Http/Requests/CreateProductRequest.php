<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'variants'    => 'required|array|min:1',
            'variants.*.size'     => 'required|string',
            'variants.*.color'    => 'required|string',
            'variants.*.quantity' => 'required|integer|min:0',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status'  => 'error',
            'data'    => null,
            'message' => $validator->errors()->first(),
        ], 400));
    }
}
