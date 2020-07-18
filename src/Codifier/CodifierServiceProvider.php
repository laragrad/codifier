<?php
namespace Laragrad\Codifier;

use Illuminate\Support\ServiceProvider;
use Laragrad\Codifier\CodifierService;

class CodifierServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang/', 'laragrad/codifier');

        $this->publishes([
            __DIR__ . '/../resources/lang/' => resource_path('lang/vendor/laragrad/codifier')
        ]);

        $this->publishes([
            __DIR__ . '/../config' => config_path('laragrad/codifier')
        ]);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(CodifierService::class, function () {
            return new CodifierService();
        });
    }
}