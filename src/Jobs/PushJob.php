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
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Larva\Bing\Push\Models\BingPush;

/**
 * 推送 Url 给百度
 *
 * @author Tongle Xu <xutongle@gmail.com>
 */
class PushJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public const CACHE_KEY = 'BingPush:ErrorCode';

    /**
     * 任务可以尝试的最大次数。
     *
     * @var int
     */
    public $tries = 25;

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
            $this->token = settings('bing.api_key');
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
        $lastErrorCode = Cache::get(static::CACHE_KEY);

        if (in_array($lastErrorCode, [2, 4, 5, 17])) {
            if ($lastErrorCode == 4) {
                $this->bingPush->setFailure('ERROR!!! ThrottleUser');
            } elseif ($lastErrorCode == 17) {
                $this->bingPush->setFailure('ERROR!!! ThrottleIP');
            } else {
                $this->bingPush->setFailure('ERROR!!! You have exceeded your daily url submission quota.');
            }
        } else {
            try {
                $response = Http::acceptJson()
                    ->asJson()
                    ->post("https://ssl.bing.com/webmaster/api.svc/json/SubmitUrl?apikey={$this->token}", ['siteUrl' => $this->site, 'url' => $this->bingPush->url]);
                if (isset($response['ErrorCode'])) {
                    Cache::put(static::CACHE_KEY, $response['ErrorCode'], now()->addHours());
                    $this->bingPush->setFailure($response['ErrorCode'] . ':' . $response['Message']);
                } else {
                    $this->bingPush->setSuccess();
                }
            } catch (\Exception $e) {
                $this->release(5);
            }
        }
    }
}
