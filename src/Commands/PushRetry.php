<?php
/**
 * This is NOT a freeware, use is subject to license terms
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 * @link http://www.larva.com.cn/
 */

namespace Larva\Bing\Push\Commands;

use Illuminate\Console\Command;
use Larva\Bing\Push\Jobs\PushJob;
use Larva\Bing\Push\Models\BingPush;

/**
 * PushRetry
 *
 * @author Tongle Xu <xutongle@gmail.com>
 */
class PushRetry extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bing:push-retry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bing push retry.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = BingPush::failure()->count();
        $bar = $this->output->createProgressBar($count);
        BingPush::failure()->orderByDesc('push_at')->chunk(100, function ($results) use ($bar) {
            /** @var BingPush $push */
            foreach ($results as $push) {
                PushJob::dispatch($push);
                $bar->advance();
            }
        });
        $bar->finish();
        $this->line('');
    }
}
