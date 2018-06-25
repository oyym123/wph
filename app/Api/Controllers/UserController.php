<?php

namespace App\Api\Controllers;

use App\Http\Requests\BindMobilePost;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\RegisterUserPost;
use App\User;
use App\Api\components\WebController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class UserController extends WebController
{
    public function index()
    {
        $data = [
            'title' => '注册成功',
            'desc' => '您已成功注册成为爱谛沉香会员!',
            'btn' => '观看视频',
            'url' => '../article'
        ];
        self::showMsg($data);
    }

    /** 注册 */
    public function registerView(Request $request)
    {

        list($info, $status) = $this->userInfo();
        if ($status) {
            return redirect()->action('UserController@center');
        }
        $invite = DB::table('invite')->where('user_id', session('user_id'))->first();
        if (!empty($invite)) {
            $inviteUserInfo = DB::table('user_info')->where('user_id', $invite->level_1)->first();
        }
        return view('api.user.register', [
            'data' => session('_old_input'),
            'invite_user_mobile' => empty($inviteUserInfo->bind_mobile) ? '' : $inviteUserInfo->bind_mobile,
            'codeError' => $request->input('codeError')
        ]);
    }

    /** 注册 */
    public function registerSuccess()
    {
        return view('api.user.register_success');
    }

    /** 注册 */
    public function register(RegisterUserPost $request)
    {
        //  var_dump($request->session()->all());exit;
        $this->weixinWebOauth(); // 需要网页授权登录
        // file_put_contents('/tmp/test.log', '授权登录成功' . PHP_EOL, FILE_APPEND);

        $user = new User();
        $openId = session('wechat_user')['id'];
        list($msg, $status) = $user->userRegister($request->input(), session('user_id'));
        if ($status < 0) {
            return redirect()->action('UserController@registerView', ['data' => $request->input(), 'codeError' => $msg]);
        }
        return view('api.user.register_success');
    }


    /** 修改用户信息 */
    public function update()
    {
        list($info, $status) = $this->userInfo();
        foreach ($info as $key => $item) {
            $data[$key] = $item;
        }
        $user = DB::table('users')->where('id', $info->user_id)->first();
        $data['email'] = $user->email;
        return view('api.user.update', ['data' => $data]);
    }

    /** 修改用户信息表单提交 */
    public function updatePost(Request $request)
    {
        $this->weixinWebOauth(); // 需要网页授权登录
        // file_put_contents('/tmp/test.log', '授权登录成功' . PHP_EOL, FILE_APPEND);
        $user = new User();
        list($info, $status) = $this->userInfo();
        $openId = session('wechat_user')['id'];
        if ($user->userUpdate($request->input(), session('user_id'))) {
            foreach ($info as $key => $item) {
                $data[$key] = $item;
            }
            $user = DB::table('users')->where('id', $info->user_id)->first();
            $data['email'] = $user->email;
            return view('api.user.update', ['status' => 1, 'data' => $data]);
        }
    }

    /** 绑定手机号 */
    public function binddingMobile()
    {
        list($info, $status) = $this->userInfo();
        $data = [
            'bind_mobile' => $info->bind_mobile

        ];
        return view('api.user.binding_mobile', ['oldPut' => session('_old_input'), 'data' => $data]);
    }

    /** 绑定手机号提交表单 */
    public function binddingMobilePost(BindMobilePost $request)
    {
        $user = new User();
        list($msg, $status) = $user->bindMobile($request->input(), session('user_id'));
        if ($status < 0) {
            return redirect()->action('UserController@binddingMobile', ['data' => $request->input(), 'codeError' => $msg]);
        }
        return view('api.user.bindmobile_success');
    }


    /** 用户中心 */
    public function center()
    {
        list($info, $status) = $this->userInfo();
        if (!$status) {
            return redirect()->action('UserController@registerView');
        }
        $data = [
            'user_photo' => $info->user_photo,
            'nick_name' => $info->nickname,
            'sex' => $info->sex
        ];
        return view('api.user.center', ['data' => $data]);
    }

    /** 会员卡 */
    public function memberCard()
    {
        list($info, $status) = $this->userInfo();
        $data = [
            'mobile' => $info->bind_mobile
        ];
        return view('api.user.member_card', ['data' => $data]);
    }

    public function upload($params)
    {
        // 传七牛
        list($ret, $err) = UploadForm::uploadImgToQiniu($params['file_path'],
            Yii::$app->params['qiniu_bucket_images'], $params['file_name']);
        if ($err) {
            Helper::writeLog($err);
            throw new Exception('上传七牛出错');
        } else {
            $saveParams = [
                'name' => $params['file_name'],
                'type' => Image::TYPE_PRODUCT_SHARE,
                'type_id' => $params['product_id'],
                'url' => $params['file_name'],
                'size_type' => Image::SIZE_MEDIUM,
                'status' => Base::STATUS_ENABLE,
                'sort' => 0,
                //'capacity_audit' => 1, //开启智能审核
            ];
        }
    }

    /** 生成永久二维码 并保存ticket到user_info表, ticket是取二维码的凭证 */
    function getInviteQrCode()
    {
        $result = self::weixin()->qrcode->forever(10);
        $ticket = $result->ticket;
//        echo $this->weixin->qrcode->url($ticket);
        print_r(self::weixin()->qrcode->url($ticket));
        exit;
        Redirect::to($this->weixin->qrcode->url($ticket));
        exit;
        // 文档地址 https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1443433542
        if (用户有ticket) {
            $ticket = user_info表里的ticket;
        } else {
            $result = $this->weixin->qrcode->forever($userInfo->user_id);
            $ticket = $result->ticket;
            // $url = $result->url; //二维码图片解析后的地址，开发者可根据该地址自行生成需要的二维码图片
            // 保存ticket到user_info表

        }

        return $this->weixin->qrcode->url($ticket); // 二维码图片地址
    }
}
