<?php

namespace Tests\Feature;

use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PaymentServiceTest extends TestCase
{
    use RefreshDatabase;

    protected PaymentService $paymentService;
    protected array $productVariantData;
    protected array $productVariantUpdateData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->paymentService = $this->app->make(PaymentService::class);
    }
}
