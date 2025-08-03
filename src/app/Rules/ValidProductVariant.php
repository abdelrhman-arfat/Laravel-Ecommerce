<?php

namespace App\Rules;

use App\Models\ProductVariant;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidProductVariant implements ValidationRule
{
    public ProductVariant|null $variant = null;

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $this->variant = ProductVariant::find($value);

        if (!$this->variant) {
            $fail('The selected product variant is invalid.');
        }
    }
}
