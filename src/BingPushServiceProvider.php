<?php
/**
 * This is NOT a freeware, use is subject to license terms.
 *
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 */

namespace Larva\Bing\Push;

use Illuminate\Support\ServiceProvider;
use Larva\Bing\Push\Commands\Push;
use Larva\Bing\Push\Commands\PushRetry;

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
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register(): void
    {
        $this->registerCommand();
    }

    /**
     * Register the MNS queue command.
     * @return void
     */
    private function registerCommand(): void
    {
        $this->app->singleton('command.bing.push', function () {
            return new Push();
        });

        $this->app->singleton('command.bing.push.retry', function () {
            return new PushRetry();
        });
    }
}
