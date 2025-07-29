<?php

namespace App\Providers;

use App\Services\CartService;
use App\Services\Interfaces\CartInterface;
use App\Services\Interfaces\OrderInterface;
use App\Services\Interfaces\OrderItemInterface;
use App\Services\Interfaces\PaymentInterface;
use App\Services\Interfaces\ProductInterface;
use App\Services\Interfaces\ProductVariantInterface;
use App\Services\Interfaces\UserServiceInterface;
use App\Services\OrderItemService;
use App\Services\OrderService;
use App\Services\PaymentService;
use App\Services\ProductService;
use App\Services\ProductVariantService;
use App\Services\UserService;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserServiceInterface::class, UserService::class);

        $this->app->bind(OrderInterface::class, OrderService::class);
        $this->app->bind(OrderItemInterface::class, OrderItemService::class);

        $this->app->bind(ProductInterface::class, ProductService::class);
        $this->app->bind(ProductVariantInterface::class, ProductVariantService::class);

        $this->app->bind(PaymentInterface::class, PaymentService::class);

        $this->app->bind(CartInterface::class, CartService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        VerifyEmail::createUrlUsing(function ($notifiable) {
            $temporarySignedURL = URL::temporarySignedRoute(
                'verification.verify',
                now()->addMinutes(60),
                ['id' => $notifiable->getKey(), 'hash' => sha1($notifiable->getEmailForVerification())]
            );
            return str_replace('/email/verify', '/api/verify-email', $temporarySignedURL);
        });
    }
}
