<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Configuration as ConfigurationModel;
use Illuminate\Contracts\Cache\Factory;

class SettingsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @param \Illuminate\Contracts\Cache\Factory $cache
     * @param \App\Setting                        $settings
     *
     * @return void
     */
    public function boot(Factory $cache, ConfigurationModel $settings)
    {
       $cache->forget('settings');
       $settings = $cache->remember('settings', 60, function() use ($settings)
       {
           return $settings->lists('value', 'name')->all();
       });
       config()->set('settings', $settings);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
