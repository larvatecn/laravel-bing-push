<?php
/**
 * This is NOT a freeware, use is subject to license terms
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 * @link http://www.larva.com.cn/
 * @license http://www.larva.com.cn/license/
 */

namespace Larva\Bing\Push\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Larva\Bing\Push\Models\BingPush;

/**
 * Class BatchPushJob
 * @author Tongle Xu <xutongle@gmail.com>
 */
class BatchPushJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * 任务可以尝试的最大次数。
     *
     * @var int
     */
    public $tries = 5;

    /**
     * @var string
     */
    protected $site;

    /**
     * @var string
     */
    protected $token;

    /**
     * @var array
     */
    protected $ids;

    /**
     * @var array
     */
    protected $urls;

    /**
     * Create a new job instance.
     *
     * @param array $ids
     * @param array $urls
     */
    public function __construct($ids, $urls)
    {
        $this->ids = $ids;
        $this->urls = $urls;
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
            $response = Http::asJson()->post("https://ssl.bing.com/webmaster/api.svc/json/SubmitUrlbatch?apikey={$this->token}", ['siteUrl' => $this->site, 'urlList' => $this->urls]);
            if (isset($response['ErrorCode'])) {
                BingPush::batchSetFailure($this->ids, $response['ErrorCode'] . ':' . $response['Message']);
            } else {
                BingPush::batchSetSuccess($this->ids);
            }
        } catch (\Exception $e) {
            $this->release(5);
        }
    }
}
