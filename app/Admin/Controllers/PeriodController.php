<?php

namespace App\Admin\Controllers;

use App\Models\Auctioneer;
use App\Models\Common;
use App\Models\Period;

use App\Models\Product;
use App\User;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class PeriodController extends Controller
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

            $content->header('产品期数');
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
        return Admin::grid(Period::class, function (Grid $grid) {
            $grid->filter(function ($filter) {
                // 在这里添加字段过滤器
                $filter->in('status', '状态')->select(Period::getStatus());
                $filter->in('real_person', '是否有真人参与')->select(User::getIsReal());
                $filter->between('created_at', '创建时间')->datetime();
                $filter->between('updated_at', '修改时间')->datetime();
            });
            $grid->id('ID')->sortable();
            $grid->code('期数代码');
            $grid->product_id('产品图片')->display(function ($released) {
               $product= Product::find($released);
                return '<a href="product?id='.$product->id.'" target="_blank" ><img src="' .
                    $product->getImgCover(). '?imageView/1/w/65/h/45" ></a>';
            });
            $grid->user_id('中标者ID');
            $grid->bid_price('中标者价格');
            $grid->bid_id('中标id');
            $grid->order_id('订单id');
            $grid->real_bid('是否让机器人退出')->display(function ($released) {
                return $released ? '是' : '否';
            });
            $grid->real_person('是否有真人参与')->display(function ($released) {
                return User::getIsReal($released);
            });
            $grid->status('状态')->display(function ($released) {
                return Period::getStatus($released);
            });
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

        return Admin::form(Period::class, function (Form $form) {
            $form->display('id', 'ID');
            $form->display('code', '期数代码');
            $form->display('product_id', '产品ID');
            $form->select('status', '状态')->options(Period::getStatus());
            $form->switch('real_bid', '是否让机器人退出')->states(Common::getStates('是', '否'))->default(0);
            $form->select('auctioneer_id', '拍卖师')->options(Auctioneer::getName());
            $form->display('created_at', '创建时间');
            $form->display('updated_at', '修改时间');
            $form->saved(function (Form $form) {
                $redis = app('redis')->connection('first');
                //竞拍开关
                if($form->model()->status == Period::STATUS_IN_PROGRESS){
                    $redis->setex('realPersonBid@periodId' . $form->model()->id, 86400 * 10, $form->model()->id);
                }
            });
        });

    }
}
