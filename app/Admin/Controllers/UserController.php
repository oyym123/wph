<?php

namespace App\Admin\Controllers;

use App\User;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class UserController extends Controller
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

            $content->header('用户管理');
            $content->description('用户列表');
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

            $content->header('会员管理');
            $content->description('修改');

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
            $content->header('会员管理');
            $content->description('创建会员');
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
        return Admin::grid(User::class, function (Grid $grid) {

            $grid->actions(function ($actions) {

                // 没有`delete-image`权限的角色不显示删除按钮
                if (!Admin::user()->can('delete-image')) {
                    $actions->disableDelete();
                }
            });

            $grid->id('ID')->sortable();

            $grid->name('名字');
            $grid->email('邮箱');
            $grid->avatar('头像');
            $grid->role_id('角色');
            $grid->created_at('创建时间');
            $grid->updated_at('修改时间');
            // filter($callback)方法用来设置表格的简单搜索框
            $grid->filter(function ($filter) {

                $filter->like('name', '名字');
                // 设置created_at字段的范围查询
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
        return Admin::form(User::class, function (Form $form) {
            $form->display('id', 'ID');
            $form->text('name', '名字');
            $form->text('email', '邮箱');
            $form->password('password', '密码')->rules('required', [
                'required' => '密码不能为空',
            ]);
            $form->password('remember_token', '密码')->rules('required', [
                'required' => '密码不能为空',
            ]);

            $form->image('avatar','头像');
            $disk = \Storage::disk('qiniu');
            $disk->getDriver()->downloadUrl('file.jpg');
            $form->text('role_id', '角色');
           // $form->text('created_at', '创建时间');
            //$form->text('updated_at', '修改时间');
        });
    }
}
