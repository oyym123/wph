<?php

namespace App\Admin\Controllers;

use App\Base;
use App\Models\Period;
use App\Models\Product;

use App\Models\ProductType;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class ProductController extends Controller
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
        return Admin::grid(Product::class, function (Grid $grid) {
            $grid->filter(function ($filter) {
                // 在这里添加字段过滤器
                $filter->like('title', '标题');
                // 设置created_at字段的范围查询
                $filter->between('created_at', '创建时间')->datetime();
                $filter->between('updated_at', '修改时间')->datetime();
            });

            $grid->id('ID')->sortable();
            $grid->img_cover('产品封面图')->display(function ($released) {
                return '<a href="' . env('QINIU_URL_IMAGES') . $released . '" target="_blank" ><img src="' .
                    env('QINIU_URL_IMAGES') . $released . '?imageView/1/w/65/h/45" ></a>';
            });
            $grid->title('标题')->color('');
            $grid->sell_price('市场价');
            $grid->bid_step('每次竞拍价');
            $grid->type('产品类型')->display(function ($released) {
                return ProductType::getOne($released, ['name'])->name;
            });
            $grid->buy_by_diff('是否可以差价购')->display(function ($released) {
                return $released ? '是' : '否';
            });
            $grid->status('状态')->display(function ($released) {
                return $released ? '有效' : '无效';
            });
            $grid->created_at('创建时间');
            $grid->updated_at('修改时间');
        });
    }

    public function period()
    {
        $model = new Period();
        $model->saveData(2);
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Product::class, function (Form $form) {
            $form->display('id', 'ID');
            $form->text('title', '标题')->rules('required', [
                'required' => '请填写产品标题',
            ]);
            $form->text('short_title', '短标题')->rules('required', [
                'required' => '请填写产品短标题',
            ]);
            $form->currency('sell_price', '市场价')->symbol('￥')->rules('required', [
                'required' => '请填写市场价',
            ]);
            $form->currency('init_price', '初始价格')->symbol('￥')->rules('required', [
                'required' => '请填写优惠券金额',
            ])->default(0);
            $form->currency('bid_step', '每次竞拍价格')->symbol('￥')->rules('required', [
                'required' => '请填写市场价',
            ])->default(0.1);
            $form->select('type', '产品类型')->options(ProductType::getList(1));
            $form->image('img_cover', '产品封面图');
            $form->image('imgs', '产品子图');

            $form->switch('buy_by_diff', '是否可以差价购买')->states(Product::$buyByDiff)->default(1);
            $form->switch('status', '状态')->states(Base::getStates())->default(1);
            $form->display('created_at', '创建时间');
            $form->display('updated_at', '修改时间');
            $form->saved(function (Form $form) {
                $diff = strtotime($form->model()->updated_at) - strtotime($form->model()->created_at);
                if (abs($diff) < 3) { //表示只在创建的时候增加期数
                    $period = new Period();
                    $period->saveData($form->model()->id);
                }
            });
        });

    }
}
