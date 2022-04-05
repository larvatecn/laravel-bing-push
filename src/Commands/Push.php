<?php
/**
 * This is NOT a freeware, use is subject to license terms.
 *
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 */

namespace Larva\Bing\Push\Commands;

use Illuminate\Console\Command;
use Larva\Bing\Push\Jobs\PushJob;
use Larva\Bing\Push\Models\BingPush;

/**
 * 推送
 *
 * @author Tongle Xu <xutongle@gmail.com>
 */
class Push extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'push:bing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bing push';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = BingPush::pending()->count();
        $bar = $this->output->createProgressBar($count);
        BingPush::pending()->orderBy('push_at', 'asc')->chunk(100, function ($results) use ($bar) {
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
