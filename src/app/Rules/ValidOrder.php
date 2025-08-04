<?php

namespace App\Rules;

use App\Models\Order;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidOrder implements ValidationRule
{
    protected ?Order $order = null;

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {

        $o = Order::find($value);
        if (!$o) {
            $fail('The selected cart is invalid.');
            return;
        }

        $this->order = $o;
    }
    public function getOrder()
    {
        return $this->order;
    }
}
