<?php

namespace App\Rules;

use App\Models\Cart;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidCart implements ValidationRule
{
    protected ?Cart $cart = null;

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $c = Cart::find($value);
        if (!$c) {
            $fail('The selected cart is invalid.');
            return;
        }

        $this->cart = $c;
    }

    public function getCart(): ?Cart
    {
        return $this->cart;
    }
    
}
