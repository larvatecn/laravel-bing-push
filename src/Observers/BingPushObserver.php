<?php
/**
 * This is NOT a freeware, use is subject to license terms
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 * @link http://www.larva.com.cn/
 */

namespace Larva\Bing\Push\Observers;

use Larva\Bing\Push\Jobs\PushJob;
use Larva\Bing\Push\Jobs\DeleteJob;
use Larva\Bing\Push\Models\BingPush;

/**
 * 模型观察者
 *
 * @author Tongle Xu <xutongle@gmail.com>
 */
class BingPushObserver
{
    /**
     * Handle "created" event.
     *
     * @param BingPush $bingPush
     * @return void
     */
    public function created(BingPush $bingPush)
    {
        PushJob::dispatch($bingPush);
    }

    /**
     * 处理「删除」事件
     *
     * @param BingPush $bingPush
     * @return void
     */
    public function deleted(BingPush $bingPush)
    {
        DeleteJob::dispatch($bingPush);
    }
}