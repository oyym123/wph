<?php
/**
 * Created by PhpStorm.
 * User: Alienware
 * Date: 2017/12/4
 * Time: 0:07
 */

namespace App\Api\Controllers;

use App\Api\components\WebController;
use App\Invite;
use Qiniu\Http\Request;
use TheSeer\Tokenizer\Exception;
use Illuminate\Support\Facades\DB;

class InviteController extends WebController
{
    /** 我的推广 */
    public function index()
    {
        list($info, $status) = $this->userInfo();
        if (!$status) {
            return redirect()->action('UserController@registerView');
        }
        // $userInfo = DB::table('user_info')->where('user_id', 1)->first(); // 测试
        return view('api.invite.my_invite', [
            'user_info' => $info
        ]);
    }

    /** 推广详情页 */
    public function view()
    {

        list($info, $status) = $this->userInfo();
        if (!$status) {
            return redirect()->action('UserController@register-view');
        }
        //$userInfo = DB::table('user_info')->where('user_id', 1)->first(); // 测试

        //$x = DB::table('user_point_card')->where('user_id', $userInfo->user_id)
        // ->where('type', $_GET['type'])->get();
//        var_dump($x);
//        foreach ($x as $a) {
//            print_r($a->id);
//        }
//        exit;
        return view('api.invite.invite_view', [
            'user_info' => $info,
            'point_list' => DB::table('user_point_card')->where('user_id', $info->user_id)->get()
        ]);
    }

    /** 推广详情页 */
    public function qrcode()
    {
        list($info, $status) = $this->userInfo();
        if (!$status) {
            return redirect()->action('UserController@registerView');
        }
        $data = [
            'user_photo' => $info->user_photo,
            'nick_name' => $info->nickname
        ];
        $result = self::weixin()->qrcode->forever(session('user_id'));
        $imageLink = self::weixin()->qrcode->url($result->ticket);
        return view('api.invite.invite_qrcode', [
            'image_link' => $imageLink,
            'data' => $data
        ]);
    }

}