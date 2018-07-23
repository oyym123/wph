<?php

namespace App\Admin\Controllers;

use App\Models\Auctioneer;
use App\Models\Period;

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
        return Admin::grid(Period::class, function (Grid $grid) {

            $grid->id('ID')->sortable();
            $grid->code('期数代码');
            $grid->product_id('产品id')->sortable();
            $grid->product_id('产品id')->sortable();
            $grid->status('状态');

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
            $form->select('auctioneer_id', '拍卖师')->options(Auctioneer::getName());
            $form->display('created_at', '创建时间');
            $form->display('updated_at', '修改时间');
        });
    }
}
