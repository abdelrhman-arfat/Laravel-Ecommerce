<?php

namespace App\Http\Requests;

use App\Rules\ValidProductVariant;
use App\Services\JsonResponseService;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Collection;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreatePaymentRequest extends FormRequest
{


    public ValidProductVariant $productVariantRule;
    public Collection $validVariants;

    public function __construct()
    {
        $this->productVariantRule = new ValidProductVariant();
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
            "variants" => ["required", "array"],
            'variants.*.product_variant_id' => [$this->productVariantRule],
            'variants.*.quantity' => "required|integer|min:1",
        ];
    }

    public function passedValidation()
    {
        $variantInputs = $this->input("variants");
        $allVariants = $this->getVariants();

        $variantsById = collect($allVariants)->keyBy("id");

        $this->validVariants = collect();

        foreach ($variantInputs as $variantInput) {
            $variant = $variantsById->get($variantInput["product_variant_id"]);

            if (!$variant || !$variant->is_active) {
                continue;
            }

            if ($variant->quantity < $variantInput["quantity"]) {
                $productName = $variant->product->name ?? 'Unknown Product';
                throw new HttpResponseException(JsonResponseService::errorResponse(400, "Not enough quantity for product: {$productName}"));
            }

            $variant->requested_quantity = $variantInput["quantity"];
            $variant->price = $variant->product->price * $variantInput["quantity"];

            $this->validVariants[] = $variant;
        }
    }

    public function getVariants()
    {
        return $this->productVariantRule->getVariants();
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
