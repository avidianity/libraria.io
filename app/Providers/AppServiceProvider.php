<?php

namespace App\Providers;

use App\Services\Service;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment('local')) {
            $this->app->register(TelescopeServiceProvider::class);
        }

        $this->registerServices();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('fileOrString', function ($attribute, $value) {
            return is_string($value) || $value instanceof UploadedFile;
        });
    }

    /**
     * Register all services as singletons.
     */
    public function registerServices()
    {
        $this->app->singleton(Service::class, function () {
            return new Service();
        });
    }
}
