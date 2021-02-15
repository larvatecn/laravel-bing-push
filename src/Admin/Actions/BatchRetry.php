<?php
/**
 * This is NOT a freeware, use is subject to license terms
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 * @link http://www.larva.com.cn/
 * @license http://www.larva.com.cn/license/
 */

namespace Larva\Bing\Push\Admin\Actions;

use Dcat\Admin\Grid\BatchAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Larva\Bing\Push\Jobs\BatchPushJob;
use Larva\Bing\Push\Models\BingPush;

/**
 * 批量重试选中
 * @author Tongle Xu <xutongle@gmail.com>
 */
class BatchRetry extends BatchAction
{
    /**
     * 确认弹窗信息
     * @return string|void
     */
    public function confirm()
    {
        return '您确定要重试已选中吗？';
    }

    // 处理请求
    public function handle(Request $request)
    {
        // 获取选中的文章ID数组
        $keys = $this->getKey();
        Cache::forget('BingPush:ErrorCode');
        $ids = [];
        $urls = [];
        foreach (BingPush::find($keys) as $item) {
            $ids[] = $item->id;
            $urls[] = $item->url;
        }
        BingPush::query()->whereIn('id', $ids)->update(['status' => BingPush::STATUS_PENDING, 'msg' => '']);
        BatchPushJob::dispatch($ids, $urls);
        return $this->response()->success('委派队列成功！')->refresh();
    }
}