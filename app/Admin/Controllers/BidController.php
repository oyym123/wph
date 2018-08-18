<?php

namespace App\Admin\Controllers;

use App\Models\Bid;

use App\Models\Common;
use App\Models\Product;
use App\Models\User;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class BidController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('投标记录');
            $content->description('列表');

            $content->body($this->grid());
        });
    }

    /**
     * Edit interface.
     *
     * @param $id
     * @return Content
     */
    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header('header');
            $content->description('description');

            // $content->body($this->form()->edit($id));
        });
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create()
    {
        return Admin::content(function (Content $content) {

            $content->header('header');
            $content->description('description');

            //  $content->body($this->form());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(Bid::class, function (Grid $grid) {
            $fillable = [
                'product_id',
                'period_id',
                'bid_price',
                'pay_amount',
                'pay_type',
                'user_id',
                'status',
                'bid_step',
                'nickname',
                'product_title',
                'end_time',
            ];
            $grid->filter(function ($filter) {
                // 在这里添加字段过滤器
                $filter->in('status', '状态')->select(Common::commonStatus());
                $filter->in('is_real', '是否真人')->select(\App\User::getIsReal());
                $filter->between('created_at', '创建时间')->datetime();
                $filter->between('updated_at', '修改时间')->datetime();
            });

            $grid->id('ID')->sortable();
            $grid->user_id('用户id')->sortable();
            $grid->nickname('昵称');
            $grid->is_real('身份')->display(function ($released) {
                return \App\User::getIsReal($released);
            });

            $grid->period_id('期数id')->sortable();
            $grid->pay_amount('支付的金额')->sortable();
            $grid->product_id('ID/产品')->sortable();
            $grid->product_title('产品');

            $grid->created_at('创建时间');
            $grid->updated_at('修改时间');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Bid::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }
}
