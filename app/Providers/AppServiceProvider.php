<?php

namespace App\Providers;

use App\Support\Cms\AdminNavigation;
use App\Support\Cms\DatabaseTranslationLoader;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Interface text edited in the admin dashboard overrides the lang/*.json files.
        $this->app->extend('translation.loader', fn ($loader) => new DatabaseTranslationLoader($loader));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Each public form has its own limit per visitor, so mistakes on one form never block another.
        foreach (['contact-form' => 10, 'careers-form' => 5, 'newsletter-form' => 6] as $name => $perMinute) {
            RateLimiter::for($name, fn (Request $request) => Limit::perMinute($perMinute)->by($name.'|'.$request->ip()));
        }
        RateLimiter::for('admin-login', fn (Request $request) => Limit::perMinute(5)->by('admin-login|'.mb_strtolower((string) $request->input('email')).'|'.$request->ip()));

        View::composer('admin.layout', function ($view) {
            $view->with('adminNav', AdminNavigation::build(request()));
        });
    }
}
