<?php

namespace App\Admin\Controllers;

use App\Base;
use App\Coupon;
use Illuminate\Support\MessageBag;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class CouponController extends Controller
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

            $content->header('header');
            $content->description('description');

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
            $content->header('优惠券');
            $content->description(trans('admin.edit'));
            $content->body($this->form()->edit($id));
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
            $content->header('优惠券');
            $content->description(trans('admin.create'));
            $content->body($this->form());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(Coupon::class, function (Grid $grid) {

            $grid->id('ID')->sortable();
            $grid->name('优惠券名称')->color('');
            $grid->money('优惠券金额');
            $grid->condition('使用需满金额');
            $grid->createnum('发放数量');
            $grid->send_num('已领取数量');
            $grid->use_num('已使用数量');
            $grid->use_end_time('使用截止时间');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Coupon::class, function (Form $form) {
            $form->display('id', 'ID');
            $form->text('name', '优惠券名称')->rules('required', [
                'required' => '请填写优惠券名称',
            ]);

            $form->currency('money', '优惠券金额')->symbol('￥')->rules('required', [
                'required' => '请填写优惠券金额',
            ]);

            $form->currency('condition', '使用条件')->symbol('￥')->rules('required', [
                'required' => '请填写优惠券金额',
            ])->help('订单需满足的最低消费金额(必需为整数)才能使用');

            $form->number('createnum', '发放数量')->rules('required', [
                'required' => '请填写发放数量',
            ]);

            $form->display('send_num', '已领取数量')->default(0);
            $form->display('use_num', '已使用数量')->default(0);

            $form->datetimeRange('send_start_time', 'send_end_time', $label = '发放开始与结束时间')->rules('required', [
                'required' => '请填写发放开始与结束时间',
            ]);

            $form->datetimeRange('use_start_time', 'use_end_time', '使用开始与结束时间')->rules('required', [
                'required' => '请填写使用开始与结束时间',
            ]);

            $form->radio('use_type', '使用范围')->options(Coupon::$scope)->default('0');
            $form->switch('status', '状态')->states(Base::getStates());
            $form->display('created_at', '创建时间');
            $form->display('updated_at', '修改时间');
//            // 抛出错误信息
//            $form->saving(function ($form) {
//                $error = new MessageBag([
//                    'title' => 'title...',
//                    'message' => 'message....',
//                ]);
//                return back()->with(compact('error'));
//            });
        });
    }
}