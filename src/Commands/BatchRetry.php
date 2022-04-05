<?php
/**
 * This is NOT a freeware, use is subject to license terms.
 *
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 */

namespace Larva\Bing\Push\Commands;

use Illuminate\Console\Command;
use Larva\Bing\Push\Jobs\BatchPushJob;
use Larva\Bing\Push\Models\BingPush;

/**
 * 批量推送
 *
 * @author Tongle Xu <xutongle@gmail.com>
 */
class BatchRetry extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'push:bing-batch-retry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bing batch retry.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = BingPush::failure()->count();
        $bar = $this->output->createProgressBar($count);
        BingPush::failure()->orderByDesc('push_at')->chunk(50, function ($results) use ($bar) {
            $ids = [];
            $urls = [];
            /** @var BingPush $push */
            foreach ($results as $push) {
                $ids[] = $push->id;
                $urls[] = $push->url;
                $bar->advance();
            }
            BatchPushJob::dispatch($ids, $urls);
        });
        $bar->finish();
        $this->line('');
    }
}
