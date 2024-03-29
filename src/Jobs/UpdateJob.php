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
use Illuminate\Support\Carbon;
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
    public int $tries = 2;

    /**
     * @var BingPush
     */
    protected BingPush $bingPush;

    /**
     * @var string
     */
    protected string $token;

    /**
     * Create a new job instance.
     *
     * @param BingPush $bingPush
     */
    public function __construct(BingPush $bingPush)
    {
        $this->bingPush = $bingPush;
        if (function_exists('settings')) {
            $this->onQueue(settings('bing.queue', 'default'));
            $this->token = settings('bing.api_key');
        } else {
            $this->onQueue(config('services.bing.queue', 'default'));
            $this->token = config('services.bing.site_token');
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        try {
            $cacheKey = 'BingPush:ErrorCode';
            $lastErrorCode = Cache::get($cacheKey);
            if ($lastErrorCode == 2 || $lastErrorCode == 4) {
                $this->bingPush->setFailure('ERROR!!! You have exceeded your daily url submission quota.');
            } else {
                $response = Http::acceptJson()
                    ->asJson()
                    ->post("https://ssl.bing.com/webmaster/api.svc/json/SubmitUrl?apikey={$this->token}", ['siteUrl' => $this->bingPush->site, 'url' => $this->bingPush->url]);
                if (isset($response['ErrorCode'])) {
                    Cache::put($cacheKey, $response['ErrorCode'], Carbon::now()->addDays());
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
