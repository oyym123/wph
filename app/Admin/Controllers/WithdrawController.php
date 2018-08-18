<?php

namespace App\Admin\Controllers;

use App\Models\Common;
use App\Models\User;
use App\Models\Withdraw;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class WithdrawController extends Controller
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

            $content->header('提现申请');
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

            //   $content->body($this->form());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(Withdraw::class, function (Grid $grid) {
            $grid->actions(function ($actions) {
                // 没有`delete-image`权限的角色不显示删除按钮
                if (!Admin::user()->can('delete-image')) {
                    $actions->disableDelete();
                }
            });
            $grid->filter(function ($filter) {
                // 在这里添加字段过滤器
                $filter->in('status', '状态')->select(Withdraw::getStatus());
                $filter->between('created_at', '创建时间')->datetime();
                $filter->between('updated_at', '修改时间')->datetime();
            });

            $grid->id('ID')->sortable();
            $grid->user_id('ID/用户昵称')->display(function ($released) {
                return $released . '【' . User::find($released)->nickname . '】';
            });
            $grid->amount('提现金额')->sortable();
            $grid->account('账号');
            $grid->status('状态')->display(function ($released) {
                return Withdraw::getStatus($released);
            });

            $grid->withdraw_at('提现时间');
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

        return Admin::form(Withdraw::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->select('status', '状态')->options(Withdraw::getStatus());
            $form->display('created_at', '创建时间');
            $form->display('updated_at', '修改时间');
        });
    }
}
