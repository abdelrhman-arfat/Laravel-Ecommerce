<?php

namespace App\Http\Requests;

use App\Utils\Constants\ConstantEnums;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class SearchOrderByStatusRequest extends FormRequest
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
            'status' => 'required|string|in:' . implode(',', array_keys(ConstantEnums::statuses())),
        ];
    }
    protected function prepareForValidation()
    {
        if ($this->query('status')) {
            $this->merge([
                'status' => $this->query('status'),
            ]);
        }
    }

    public function messages()
    {
        return [
            'status.required' => 'The status field is required.',
            'status.in' => 'The status must be one of the following: ' . implode(',', array_keys(ConstantEnums::statuses())),
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
