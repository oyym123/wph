<?php

namespace App\Admin\Controllers;

use Encore\Admin\Widgets\InfoBox;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;

class HomeController extends Controller
{
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('管理中心');
            // $content->description('Description...');

            $content->row(Dashboard::title());


            $content->row(function (Row $row) {

                $row->column(4, function (Column $column) {
                    $infoBox1 = new InfoBox('今日会员总数', 'user', 'aqua', '/admin/users', '1024');

                    $column->append($infoBox1);

                });

                $row->column(4, function (Column $column) {
                    $infoBox2 = new InfoBox('今日订单总数', 'shopping-cart', 'blue', '/admin/users', '1024');
                    $column->append($infoBox2);
                });

                $row->column(4, function (Column $column) {
                    $infoBox3 = new InfoBox('今日访问量', 'eye', 'purple', '', '1024');
                    $column->append($infoBox3);
                });

            });

            $content->row(function (Row $row) {

                $row->column(4, function (Column $column) {
                    $infoBox1 = new InfoBox('会员总数', 'users', 'green', '/admin/users', '6624');

                    $column->append($infoBox1);

                });

                $row->column(4, function (Column $column) {
                    $infoBox2 = new InfoBox('商品数量', 'th-large', 'yellow', '/admin/users', '1024');
                    $column->append($infoBox2);
                });

                $row->column(4, function (Column $column) {
                    $infoBox3 = new InfoBox('待处理订单', 'file-text-o', 'red', '', '1024');
                    $column->append($infoBox3);
                });

            });

            $content->row(function (Row $row) {
                $row->column(6, function (Column $column) {
                    $column->append(Dashboard::dependencies());
                });
                $row->column(6, function (Column $column) {

                    $column->append(Dashboard::environment());
                });

            });

        });
    }
}
