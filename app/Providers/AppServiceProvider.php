<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Storage;
use App\Models\AppSetting;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Carbon::setLocale('id');

        View::composer('*', function ($view) {
            $logoPath = AppSetting::getValue('app_logo');
            if ($logoPath && Storage::disk('public')->exists($logoPath)) {
                $view->with('appLogoUrl', asset('public/storage/' . $logoPath));
            } else {
                $view->with('appLogoUrl', null);
            }
        });
    }
}
