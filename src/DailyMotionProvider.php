<?php

namespace Bakcay\DailyMotion;

use Illuminate\Support\ServiceProvider;
use Bakcay\DailyMotion\Facades\DailyMotionFacade;
use Bakcay\DailyMotion\Services\DailyMotion;

class DailyMotionProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            $this->configPath() => config_path('dailymotion.php'),
        ]);
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom($this->configPath(), 'dailymotion');

        $this->app->booting(function () {
            $loader = \Illuminate\Foundation\AliasLoader::getInstance();
            $loader->alias('DailyMotion', DailyMotionFacade::class);
        });

        $this->app->bind('dailymotion', function ($app) {
            $config = $app['config'];
            $daily = new DailyMotion($config);
            $daily->setOptions([
                'headers'  => [
                    'Authorization' => 'Bearer '.$daily->getAccessToken(),
                ],
            ]);

            return $daily;
        });
    }

    /**
     * @return string
     */
    protected function configPath()
    {
        return __DIR__.'/../config/dailymotion.php';
    }
}
