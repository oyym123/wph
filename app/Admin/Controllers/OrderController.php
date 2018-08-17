<?php
/**
 * Created by PhpStorm.
 * User: Alienware
 * Date: 2018/8/17
 * Time: 19:37
 */

namespace App\Admin\Controllers;


use App\Models\Order;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

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
        return Admin::grid(Order::class, function (Grid $grid) {
            $fillable = [
                'sn',
                'pay_type',
                'pay_amount',
                'period_id',
                'product_id',
                'product_amount',
                'discount_amount', //折扣的价格
                'status',
                'buyer_id',
                'evaluation_status', //评价状态
                'address_id', //收货人地址
                'shipping_number', //快运单号
                'shipping_company', //快递公司拼音
                'seller_shipped_at', //卖家发货时间
                'str_address', //收货地址
                'str_username', //收货人姓名
                'str_phone_number', //手机号
                'expired_at', //过期时间
                'type', //类型
                'ip', //ip
                'signed_at', //签收时间
                'recharge_card_id', //充值卡id
                'gift_amount', //赠送的金额
            ];
            $grid->id('ID')->sortable();
            $grid->pay_amount('支付金额')->sortable();
            $grid->period_id('期数id')->sortable();
            $grid->product_id('产品id')->sortable();
            $grid->status('状态')->display(function ($released) {
                return Order::getStatus($released);
            });

            $grid->type('类型')->display(function ($released) {
                return Order::getType($released);
            });
            $grid->buyer_id('买家id');
            $grid->str_address('收货人地址');
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
            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }
}
