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

        // Auto-create required storage & cache directories with safe permissions
        foreach ([
            storage_path('fonts'),
            storage_path('app/public/exports'),
            storage_path('app/public/ttd'),
            storage_path('app/public/inventaris_foto'),
            storage_path('framework/cache/data'),
            storage_path('framework/views'),
            storage_path('framework/sessions'),
            storage_path('logs'),
        ] as $dir) {
            if (!is_dir($dir)) {
                @mkdir($dir, 0775, true);
            }
        }

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
