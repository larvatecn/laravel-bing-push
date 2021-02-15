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
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Larva\Bing\Push\Models\BingPush;

/**
 * Class UpdateJob
 *
 * @author Tongle Xu <xutongle@gmail.com>
 */
class UpdateJob implements ShouldQueue
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
     * @param BingPush $bingPush
     */
    public function __construct(BingPush $bingPush)
    {
        $this->bingPush = $bingPush;
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
            $cacheKey = 'BingPush:ErrorCode';
            $lastErrorCode = Cache::get($cacheKey);
            if ($lastErrorCode == 2 || $lastErrorCode == 4) {
                $this->bingPush->setFailure('ERROR!!! You have exceeded your daily url submission quota.');
            } else {
                $response = Http::asJson()->post("https://ssl.bing.com/webmaster/api.svc/json/SubmitUrl?apikey={$this->token}", ['siteUrl' => $this->site, 'url' => $this->bingPush->url]);
                if (isset($response['ErrorCode'])) {
                    Cache::put($cacheKey, $response['ErrorCode'], now()->addDays());
                    $this->bingPush->setFailure($response['ErrorCode'] . ':' . $response['Message']);
                } else {
                    $this->bingPush->setSuccess();
                }
            }
        } catch (\Exception $e) {
            $this->bingPush->setFailure($e->getMessage());
        }
    }
}
