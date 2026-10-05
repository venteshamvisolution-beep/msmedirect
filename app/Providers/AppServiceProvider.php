<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->usePublicPath(base_path('public'));

        $this->app->singleton(\Livewire\Mechanisms\HandleRequests\HandleRequests::class, function ($app) {
            return new class extends \Livewire\Mechanisms\HandleRequests\HandleRequests {
                public function getUpdateUri()
                {
                    $basePath = parse_url(config('app.url'), PHP_URL_PATH) ?: '';
                    if (! $this->updateRoute) {
                        return $basePath . '/livewire/update';
                    }
                    $uri = parent::getUpdateUri();
                    if ($basePath && ! str_starts_with($uri, $basePath)) {
                        return $basePath . $uri;
                    }
                    return $uri;
                }
            };
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $basePath = parse_url(config('app.url'), PHP_URL_PATH) ?: '';
        if ($basePath) {
            config(['livewire.asset_url' => $basePath . '/livewire/livewire.js']);
        }
    }
}
