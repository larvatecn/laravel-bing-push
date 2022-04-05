<?php
/**
 * This is NOT a freeware, use is subject to license terms.
 *
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 */

namespace Larva\Bing\Push\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Larva\Bing\Push\Models\BingPush;

/**
 * 删除推送
 *
 * @author Tongle Xu <xutongle@gmail.com>
 */
class DeleteJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * 任务可以尝试的最大次数。
     *
     * @var int
     */
    public $tries = 2;

    /**
     * @var BingPush
     */
    protected $bingPush;

    /**
     * @var string
     */
    protected $site;

    /**
     * @var string
     */
    protected $token;

    /**
     * Create a new job instance.
     *
     * @param BingPush $push
     */
    public function __construct(BingPush $push)
    {
        $this->bingPush = $push;
        if (function_exists('settings')) {
            $this->site = config('app.url');
            $this->token = settings('system.bing_api_key');
        } else {
            $this->site = config('services.bing.site');
            $this->token = config('services.bing.site_token');
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            Http::acceptJson()
                ->asJson()
                ->post("https://ssl.bing.com/webmaster/api.svc/json/SubmitUrl?apikey={$this->token}", ['siteUrl' => $this->site, 'url' => $this->bingPush->url]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
