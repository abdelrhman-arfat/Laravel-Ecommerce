<?php

namespace App\Services;

use App\Models\Payment;
use App\Services\Interfaces\PaymentInterface;

class PaymentService implements PaymentInterface
{
  public function create(string $paymentSession)
  {
    return Payment::create([
      'payment_session' => $paymentSession
    ]);
  }
  public function find(string $paymentSession)
  {
    return Payment::where('payment_session', $paymentSession)->first();
  }
}
