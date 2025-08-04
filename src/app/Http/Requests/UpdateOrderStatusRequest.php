<?php

namespace App\Http\Requests;

use App\Rules\ValidOrder;
use App\Utils\Constants\ConstantEnums;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateOrderStatusRequest extends FormRequest
{
    protected ValidOrder    $validOrder;
    public function __construct()
    {
        $this->validOrder = new ValidOrder();
    }
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
            'user_id' => 'required|exists:users,id',
            'order_id' => [$this->validOrder],
            'status' => 'required|string|in:' . implode(',', array_keys(ConstantEnums::statuses())),
        ];
    }


    public function getOrder()
    {
        return $this->validOrder->getOrder();
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
