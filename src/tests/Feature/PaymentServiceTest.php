<?php

namespace Tests\Feature;

use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Stringable;
use Tests\TestCase;

class PaymentServiceTest extends TestCase
{
    use RefreshDatabase;

    protected PaymentService $paymentService;
    protected array $productVariantData;
    protected array $productVariantUpdateData;
    protected string $paymentSession;
    protected function setUp(): void
    {
        parent::setUp();
        $this->paymentSession = "xqk8c1IP2PbNap9Z6yJwhtZwoOAp5jsniQ";
        $this->paymentService = $this->app->make(PaymentService::class);
    }
    public function test_payment_service_create()
    {
        $payment = $this->paymentService->create($this->paymentSession);
        $this->assertEquals($payment->payment_session, $this->paymentSession);
    }
    public function test_payment_service_find_by_session_id()
    {
        Payment::create([
            'payment_session' => $this->paymentSession
        ]);
        $payment = $this->paymentService->find($this->paymentSession);
        $this->assertEquals($payment->payment_session, $this->paymentSession);
    }
}
