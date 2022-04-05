<?php
/**
 * This is NOT a freeware, use is subject to license terms.
 *
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 */

namespace Larva\Bing\Push;

use Illuminate\Support\ServiceProvider;

/**
 * Class BingPushServiceProvider
 * @author Tongle Xu <xutongle@gmail.com>
 */
class BingPushServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands('command.bing.push');
            $this->commands('command.bing.push.retry');
            $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        }

        \Larva\Bing\Push\Models\BingPush::observe(\Larva\Bing\Push\Observers\BingPushObserver::class);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerCommand();
    }

    /**
     * Register the MNS queue command.
     * @return void
     */
    private function registerCommand()
    {
        $this->app->singleton('command.bing.push', function () {
            return new \Larva\Bing\Push\Commands\Push();
        });

        $this->app->singleton('command.bing.push.retry', function () {
            return new \Larva\Bing\Push\Commands\PushRetry();
        });
        $this->app->singleton('command.bing.push.batch.retry', function () {
            return new \Larva\Bing\Push\Commands\BatchRetry();
        });
    }
}
