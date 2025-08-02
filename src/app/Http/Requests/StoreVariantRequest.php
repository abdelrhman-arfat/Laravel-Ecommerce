<?php

namespace App\Http\Requests;

use App\Utils\Constants\ConstantEnums;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreVariantRequest extends FormRequest
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
            'product_id' => 'required|exists:products,id',
            'color'      => 'required|string|in:' . implode(',', array_keys(ConstantEnums::colors())),
            'size'       => 'required|string|in:' . implode(',', array_keys(ConstantEnums::sizes())),
            'quantity' => 'required|integer|min:0',
            'is_active' => 'boolean'
        ];
    }

    public function messages()
    {
        return [
            'color.in' => 'The color must be one of the following: ' . implode(',', array_keys(ConstantEnums::colors())),
            'size.in' => 'The size must be one of the following: ' . implode(',', array_keys(ConstantEnums::sizes())),
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'data' => null,
            'message' => $validator->errors()->first(),
        ], 400));
    }
}
