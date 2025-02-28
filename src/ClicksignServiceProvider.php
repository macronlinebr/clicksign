<?php

namespace Macronlinebr\Clicksign;

use Illuminate\Support\ServiceProvider;

class ClicksignServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        /*
         * Optional methods to load your package assets
         */
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'clicksign');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'clicksign');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('clicksign.php'),
            ], 'config');

            // Publishing the views.
            /*$this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/clicksign'),
            ], 'views');*/

            // Publishing assets.
            /*$this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/clicksign'),
            ], 'assets');*/

            // Publishing the translation files.
            /*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/clicksign'),
            ], 'lang');*/

            $this->publishes([
                __DIR__ . '/../database/migrations/create_api_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_api_table.php'),
                // you can add any number of migrations here
            ], 'migrations');

            // Registering package commands.
            // $this->commands([]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'clicksign');

        // Register the main class to use with the facade
        $this->app->singleton('clicksign', function () {
            return new Clicksign;
        });
    }
}
