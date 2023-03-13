<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Blade;

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
        // Fix for migrations on older versions of mysql and mariadb
        Schema::defaultStringLength(191);

        // Add the lang blade directive for multi-language support
        Blade::directive('lang', function($expression) {
            return "<?php echo Language::select($expression); ?>";
        });
    }
}
