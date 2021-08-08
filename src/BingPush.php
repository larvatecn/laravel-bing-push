<?php
/**
 * This is NOT a freeware, use is subject to license terms
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 * @link http://www.larva.com.cn/
 */

namespace Larva\Bing\Push;

use Larva\Bing\Push\Jobs\UpdateJob;

/**
 * Bing推送快捷方法
 *
 * @author Tongle Xu <xutongle@gmail.com>
 */
class BingPush
{
    /**
     * 推送 Url 给Bing
     * @param string $url
     * @return \Larva\Bing\Push\Models\BingPush
     */
    public static function push($url)
    {
        return \Larva\Bing\Push\Models\BingPush::firstOrCreate(['url' => $url]);
    }

    /**
     * 推送 Url 给Bing
     * @param string $url
     */
    public static function update($url)
    {
        if (($ping = \Larva\Bing\Push\Models\BingPush::query()->where('url', '=', $url)->first()) != null) {
            $ping->update(['status' => \Larva\Bing\Push\Models\BingPush::STATUS_PENDING]);
            UpdateJob::dispatch($ping);
        } else {
            static::push($url);
        }
    }

    /**
     * 推送 Url 给Bing
     * @param string $url
     * @throws \Exception
     */
    public static function delete($url)
    {
        if (($ping = \Larva\Bing\Push\Models\BingPush::query()->where('url', '=', $url)->first()) != null) {
            $ping->delete();
        }
    }
}
