<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Domain\Auth\Contracts\UserRepositoryInterface;
use App\Domain\Auth\Contracts\TokenManagerInterface;
use App\Infrastructure\Auth\UserRepository;
use App\Infrastructure\Auth\SanctumTokenManager;
use App\Application\Payments\Services\Contracts\CreatePaymentAttemptServiceInterface;
use App\Application\Payments\Services\CreatePaymentAttemptService;
use App\Application\Payments\Services\Contracts\PaymentReadServiceInterface;
use App\Application\Payments\Services\PaymentReadService;
use App\Application\Payments\Services\Contracts\HandlePaymentWebhookServiceInterface;
use App\Application\Payments\Services\HandlePaymentWebhookService;
use App\Domain\Payments\Contracts\PaymentRepositoryInterface;
use App\Infrastructure\Payments\Persistence\PaymentRepository;
use App\Application\Payments\Routing\PaymentRoutingStrategyInterface;
use App\Application\Payments\Routing\DefaultPaymentRoutingStrategy;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
          $this->app->bind(PaymentRepositoryInterface::class, PaymentRepository::class);
          $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
          $this->app->bind(TokenManagerInterface::class, SanctumTokenManager::class);
          $this->app->bind(CreatePaymentAttemptServiceInterface::class, CreatePaymentAttemptService::class);
          $this->app->bind(PaymentReadServiceInterface::class, PaymentReadService::class);
          $this->app->bind(HandlePaymentWebhookServiceInterface::class, HandlePaymentWebhookService::class);
          $this->app->bind(PaymentRoutingStrategyInterface::class, DefaultPaymentRoutingStrategy::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
