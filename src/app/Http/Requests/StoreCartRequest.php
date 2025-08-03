<?php

namespace App\Http\Requests;

use App\Rules\ValidProductVariant;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreCartRequest extends FormRequest
{

    // use one query to get the product variant
    public ValidProductVariant $productVariantRule;

    public function __construct()
    {
        $this->productVariantRule = new ValidProductVariant();
    }


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
            'product_variant_id' => [$this->productVariantRule],
            'quantity' => "required|integer|min:1",
        ];
    }
    public function getProductVariant()
    {
        return $this->productVariantRule->variant;
    }


    public function messages()
    {
        return [
            'product_variant_id.exists' => 'The product variant does not exist.',
            'product_variant_id.required' => 'The product variant is required.',
            'quantity.required' => 'The quantity is required.',
            'quantity.integer' => 'The quantity must be an integer.',
            'quantity.min' => 'The quantity must be at least 1.',
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
