<?php

namespace App\Http\Requests;

use App\Models\Cart;
use App\Models\Product;
use App\Rules\ValidCart;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateCartRequest extends FormRequest
{
    public ValidCart $validCart;

    public function __construct()
    {
        $this->validCart = new ValidCart();
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
            'id' => [$this->validCart],
            'quantity' => "sometimes|integer|min:1",
        ];
    }


    protected function prepareForValidation(): void
    {
        $this->merge([
            'id' => $this->route('id'),
        ]);
    }

    public function getCart(): ?Cart
    {
        return $this->validCart->getCart();
    }
    public function getProduct()
    {
        return $this->getCart()->product;
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
