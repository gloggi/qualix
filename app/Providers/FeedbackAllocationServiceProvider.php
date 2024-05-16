<?php

namespace App\Providers;

use App\Services\FeedbackAllocation\DefaultFeedbackAllocator;
use App\Services\FeedbackAllocation\FeedbackAllocator;
use Illuminate\Support\ServiceProvider;

class FeedbackAllocationServiceProvider extends ServiceProvider
{

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(FeedbackAllocator::class, DefaultFeedbackAllocator::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
    }
}
