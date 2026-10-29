<?php

namespace App\Providers;

use App\Contracts\AuthServiceInterface;
use App\Contracts\RegistrationOtpInterface;
use App\Contracts\UserOtpInterface;
use App\Services\OtpService;
use App\Models\SystemSetting;
use App\Repositories\Contracts\FaceScanRepositoryInterface;
use App\Repositories\FaceScanRepository;
use App\Services\AuthService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(RegistrationOtpInterface::class, OtpService::class);
        $this->app->bind(UserOtpInterface::class, OtpService::class);
        $this->app->bind(AuthServiceInterface::class, AuthService::class);

        $this->app->bind(
            FaceScanRepositoryInterface::class,
            FaceScanRepository::class
        );

    }
    public function boot(): void
    {
        view()->composer('*', function ($view) {
            $setting = SystemSetting::first();
            $view->with('setting', $setting);
        });
    }
}
