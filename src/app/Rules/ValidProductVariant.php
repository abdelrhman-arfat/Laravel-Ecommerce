<?php

namespace App\Rules;

use App\Models\Product;
use App\Models\ProductVariant;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidProductVariant implements ValidationRule
{
    /**
     * Holds valid ProductVariant models.
     *
     * @var ProductVariant[]
     */
    protected array $validVariants = [];

    /**
     * Validate each product_variant_id.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $variant = ProductVariant::find($value);

        if (!$variant) {
            $fail('The selected product variant is invalid.');
        } else {
            $this->validVariants[] = $variant;
        }
    }

    public function getVariants(): array
    {
        return $this->validVariants;
    }
}
