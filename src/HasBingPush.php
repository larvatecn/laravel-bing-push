<?php
/**
 * This is NOT a freeware, use is subject to license terms.
 *
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 */

namespace Larva\Bing\Push;

use Illuminate\Database\Eloquent\Model;

/**
 * 使用 Bing 推送
 *
 * @property Model $this
 * @author Tongle Xu <xutongle@gmail.com>
 */
trait HasBingPush
{
    /**
     * Boot the trait.
     *
     * Listen for the deleting event of a model, then remove the relation between it and tags
     */
    protected static function bootHasBingPush(): void
    {
        static::created(function ($model) {
            BingPush::push($model->url);
        });
        static::updated(function ($model) {
            BingPush::update($model->url);
        });
        static::deleted(function ($model) {
            BingPush::delete($model->url);
        });
    }
}
