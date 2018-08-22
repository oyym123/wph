<?php
/**
 * Created by PhpStorm.
 * User: Alienware
 * Date: 2018/8/17
 * Time: 19:37
 */

namespace App\Admin\Controllers;


use App\Models\Common;
use App\Models\Order;
use App\Models\Product;
use App\User;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
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

            $content->header('订单管理');
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
        return Admin::grid(Order::class, function (Grid $grid) {
            $grid->id('ID')->sortable();

            $grid->buyer_id('买家')->display(function ($released) {
                $user = User::find($released);
                return '<a href="users?id=' . $user->id . '" target="_blank" ><img src="' .
                    Common::getImg($user->avatar) . '?imageView/1/w/65/h/45" ></a>';
            });

            $grid->pay_amount('支付金额')->sortable();
            $grid->period_id('期数id')->sortable();
            $grid->product_id('产品图片')->display(function ($released) {
                $product = Product::find($released);
                if ($product) {
                    return '<a href="product?id=' . $product->id . '" target="_blank" ><img src="' .
                        $product->getImgCover() . '?imageView/1/w/65/h/45" ></a>';
                } else {
                    return '';
                }
            });
            $grid->status('状态')->display(function ($released) {
                return Order::getStatus($released);
            });

            $grid->type('类型')->display(function ($released) {
                return Order::getType($released);
            });

            $grid->str_address('收货人地址');
            $grid->shipping_company('快递公司');
            $grid->shipping_number('快运单号');
            $grid->seller_shipped_at('发货时间');
            $grid->created_at('创建时间');
            $grid->updated_at('修改时间');
            $grid->filter(function ($filter) {
                $filter->like('sn', '订单号');
                $filter->in('status', '状态')->select(Order::getStatus());
                $filter->in('type', '类型')->select(Order::getType());
                $filter->between('created_at', '创建时间')->datetime();
                $filter->between('updated_at', '修改时间')->datetime();
            });
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Order::class, function (Form $form) {
            $form->display('id', 'ID');
            $form->display('sn', '订单号');
            $form->display('pay_amount', '支付金额');
            $form->select('status', '状态')->options(Order::getStatus());
            $form->text('str_address', '收货人地址');
            $form->text('shipping_company', '快递公司');
            $form->text('shipping_number', '快运单号');
            $form->display('created_at', '创建时间');
            $form->display('updated_at', '修改时间');
            $form->saved(function (Form $form) {
                if ($form->model()->shipping_number) {
                    DB::table('order')->where(['id' => $form->model()->id])->update([
                        'seller_shipped_at' => date('Y-m-d H:i:s', time()),
                        'status' => Order::STATUS_SHIPPED
                    ]);
                }
            });
        });
    }
}
