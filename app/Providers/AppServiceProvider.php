<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Pagination\Paginator;

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
        Paginator::useBootstrapFive();

        // Share theme configuration variables globally to all views
        View::share([
            'pc_dark_layout' => config('theme.dark_layout', 'light'),
            'pc_sidebar_theme' => config('theme.sidebar_theme', 'dark'),
            'pc_box_container' => config('theme.box_container', 'false'),
            'pc_caption_show' => config('theme.caption_show', 'true'),
            'pc_rtl_layout' => config('theme.rtl_layout', 'false'),
            'pc_preset_theme' => config('theme.preset_theme', 'preset-1'),
            'pc_theme_layout' => config('theme.theme_layout', 'vertical'),
        ]);
    }
}
