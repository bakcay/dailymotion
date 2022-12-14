<?php

namespace Bakcay\DailyMotion;

use Illuminate\Support\ServiceProvider;

class DailyMotionProvider extends ServiceProvider {
   /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot() {
        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole() {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__ . '/../config/dailymotion.php' => config_path('dailymotion.php'),
        ], 'dailymotion.config');
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register() {
        $this->mergeConfigFrom(__DIR__ . '/../config/dailymotion.php', 'dailymotion');

        // Register the service the package provides.
        $this->app->singleton('dailymotion', function ($app) {
            return new DailyMotion;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides() {
        return ['dailymotion'];
    }
}
