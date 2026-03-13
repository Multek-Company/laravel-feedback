<?php

namespace Multek\LaravelFeedback;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class FeedbackServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/feedback.php', 'feedback');

        $this->app->singleton(FeedbackManager::class);
    }

    public function boot(): void
    {
        $this->publishConfig();
        $this->publishMigrations();
        $this->registerRoutes();
    }

    protected function publishConfig(): void
    {
        $this->publishes([
            __DIR__.'/../config/feedback.php' => config_path('feedback.php'),
        ], 'feedback-config');
    }

    protected function publishMigrations(): void
    {
        $this->publishesMigrations([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'feedback-migrations');
    }

    protected function registerRoutes(): void
    {
        if (! config('feedback.route.enabled')) {
            return;
        }

        Route::prefix(config('feedback.route.prefix', 'api/feedback'))
            ->middleware(config('feedback.route.middleware', ['api']))
            ->group(function () {
                Route::get('/', [Http\Controllers\FeedbackController::class, 'index']);
                Route::post('/', [Http\Controllers\FeedbackController::class, 'store']);
            });
    }
}
