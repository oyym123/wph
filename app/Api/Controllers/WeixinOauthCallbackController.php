<?php
/**
 * Created by PhpStorm.
 * User: lixinxin
 * Date: 2017/12/03
 * Time: 12:03
 */

namespace App\Api\Controllers;

use App\Api\components\WebController;
use Illuminate\Support\Facades\Redirect;
use League\Flysystem\Exception;
use Illuminate\Http\Request;

class WeixinOauthCallbackController extends WebController
{
    function index()
    {
        /*
        $user->toArray() = array (
            'id' => 'oEmcq1Yfbyugr07M1-2e5WLaykGo',
            'name' => '李新新',
            'nickname' => '李新新',
            'avatar' => 'http://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTLTQnQ7ZKZvsYdnRQhOEltsCBBJicyMAfAewZ3iclN0artAmvUQ5cpM7WI5xzKS3ImZ6PWg4dicAaS3Q/0',
            'email' => NULL,
            'original' =>
                array (
                    'openid' => 'oEmcq1Yfbyugr07M1-2e5WLaykGo',
                    'nickname' => '李新新',
                    'sex' => 0,
                    'language' => 'zh_CN',
                    'city' => '',
                    'province' => '',
                    'country' => '',
                    'headimgurl' => 'http://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTLTQnQ7ZKZvsYdnRQhOEltsCBBJicyMAfAewZ3iclN0artAmvUQ5cpM7WI5xzKS3ImZ6PWg4dicAaS3Q/0',
                    'privilege' =>
                        array (
                        ),
                ),
            'token' =>
                Overtrue\Socialite\AccessToken::__set_state(array(
                    'attributes' =>
                        array (
                            'access_token' => '4_Ni_S86WKLAkg1139dYWyraYFBn_krg8MxjkTJe0QvzoAHA_XRmUL2QaXJzYue8j6IXE-DejHt-9zKPmarxShiFKfFoOv1kTv1cZ4I_y2xKY',
                            'expires_in' => 7200,
                            'refresh_token' => '4_nWVCO6MSl2_PfmyAiYHg0sdwY-RL3r2vfGP7FIu0r-jYFnfwEvtxupp9uebPL0a8IE_13H557kMEYXha7ZzYANdLV4y8AVX0Q3v96K6-cTo',
                            'openid' => 'oEmcq1Yfbyugr07M1-2e5WLaykGo',
                            'scope' => 'snsapi_userinfo',
                        ),
                )),
            'provider' => 'WeChat',
        )*/
        try {
            // file_put_contents('/tmp/test.log', var_export($_REQUEST, 1) . PHP_EOL, FILE_APPEND);
            // 获取 OAuth 授权结果用户信息
            $user = self::weixin()->oauth->user();
            $wechatUser = $user->toArray();
            session([
                'wechat_user' => $wechatUser,
//                'bind_mobile' => empty($userInfo->bind_mobile) ? '' : $userInfo->bind_mobile,
//                'user_id' => empty($userInfo->user_id) ? 0 : $userInfo->user_id,
            ]);
            session()->save();
            file_put_contents('/tmp/test.log', var_export($user->toArray(), 1) . PHP_EOL, FILE_APPEND);
            return Redirect::to($_GET['redirect']);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}