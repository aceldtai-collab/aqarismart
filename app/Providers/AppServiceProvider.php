<?php

namespace App\Providers;

use App\Providers\TelescopeServiceProvider;
use App\Services\NativePHP\MigrationHelper;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Laravel\Telescope\TelescopeApplicationServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->environment('local') && function_exists('gethostname')) {
            if (class_exists(TelescopeApplicationServiceProvider::class)) {
                $this->app->register(TelescopeServiceProvider::class);
            }
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // NativePHP SQLite: run missing migrations on every boot
        if (\DB::getDriverName() === 'sqlite') {
            try {
                MigrationHelper::runMigrations();
            } catch (\Throwable $e) {
                \Log::error('NativePHP migration failed: ' . $e->getMessage());
            }
            // Seed ad_durations if the table now exists but is empty
            try {
                if (Schema::hasTable('ad_durations') && \DB::table('ad_durations')->count() === 0) {
                    \Illuminate\Support\Facades\Artisan::call('db:seed', [
                        '--class' => 'AdDurationSeeder',
                        '--force' => true,
                    ]);
                }
            } catch (\Throwable $e) {
                \Log::error('NativePHP ad_durations seed failed: ' . $e->getMessage());
            }
        }

        // Query logging for performance monitoring
        if (config('app.debug')) {
            \DB::listen(function ($query) {
                if ($query->time > 1000) {
                    \Log::warning('Slow query detected', [
                        'sql' => $query->sql,
                        'time' => $query->time . 'ms',
                        'bindings' => $query->bindings
                    ]);
                }
            });
        }

        Blade::directive('num', function ($expression) {
            return "<?php \$__val = (string)($expression); if(app()->getLocale()==='ar'){ \$__map=['0'=>'٠','1'=>'١','2'=>'٢','3'=>'٣','4'=>'٤','5'=>'٥','6'=>'٦','7'=>'٧','8'=>'٨','9'=>'٩',','=>'٬','.'=>'٫']; echo strtr(\$__val, \$__map); } else { echo \$__val; } ?>";
        });
    }
}
