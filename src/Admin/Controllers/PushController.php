<?php
/**
 * This is NOT a freeware, use is subject to license terms
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 * @link http://www.larva.com.cn/
 * @license http://www.larva.com.cn/license/
 */

namespace Larva\Bing\Push\Admin\Controllers;

use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Grid;
use Larva\Bing\Push\Admin\Actions\BatchRetry;
use Larva\Bing\Push\Admin\Actions\PushRetry;
use Larva\Bing\Push\Models\BingPush;

/**
 * 必应推送控制器
 * @author Tongle Xu <xutongle@gmail.com>
 */
class PushController extends AdminController
{
    protected function title()
    {
        return '必应推送';
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new BingPush(), function (Grid $grid) {
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
                $filter->equal('status','推送状态')->select([
                    BingPush::STATUS_PENDING => '待推送',
                    BingPush::STATUS_SUCCESS => '推送成功',
                    BingPush::STATUS_FAILURE => '推送失败',
                ]);
                //顶部筛选
                $filter->scope('failure', '推送失败')->where('status',BingPush::STATUS_FAILURE);
                $filter->scope('pending', '待推送')->where('status',BingPush::STATUS_PENDING);
            });
            $grid->quickSearch(['id']);
            $grid->model()->orderBy('id', 'desc');

            $grid->column('id', 'ID')->sortable();
            $grid->column('url', 'Url')->link();
            $grid->column('status', '状态')->using([
                BingPush::STATUS_PENDING => '待推送',
                BingPush::STATUS_SUCCESS => '推送成功',
                BingPush::STATUS_FAILURE => '推送失败',
            ])->dot([
                BingPush::STATUS_PENDING => 'info',
                BingPush::STATUS_SUCCESS => 'success',
                BingPush::STATUS_FAILURE => 'warning',
            ], 'info');
            $grid->column('msg', '');
            $grid->column('failures', '失败次数');
            $grid->column('pending', '重试')->action(PushRetry::make());
            $grid->column('push_at', '推送时间');
            $grid->column('created_at', '创建时间')->sortable();

            $grid->disableCreateButton();
            $grid->disableViewButton();
            $grid->disableEditButton();
            $grid->paginate(12);

            $grid->batchActions([
                new BatchRetry('重试'),
            ]);
        });
    }
}
