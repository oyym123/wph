<?php

namespace App\Api\Controllers;

use App\Helpers\Helper;
use App\Helpers\QiniuHelper;
use App\Helpers\WXBizDataCrypt;
use App\Http\Requests\BindMobilePost;
use App\Models\Bid;
use App\Models\City;
use App\Models\Evaluate;
use App\Models\Expend;
use App\Models\Income;
use App\Models\Order;
use App\Models\Pay;
use App\Models\Period;
use App\Models\UserAddress;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\RegisterUserPost;
use App\User;
use App\Api\components\WebController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Ramsey\Uuid\Uuid;

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

    public function login()
    {

    }


    /**
     * @param Request $request
     * @SWG\Get(path="/api/user/info",
     *   tags={"用户中心"},
     *   summary="",
     *   description="Author: OYYM",
     *   @SWG\Parameter(name="code", in="query", default="1", description="code", required=true,
     *     type="string",
     *   ),
     *   @SWG\Parameter(name="nickname", in="query", default="佚名", description="用户昵称", required=true,
     *     type="string",
     *   ),
     *   @SWG\Parameter(name="avatar", in="query", default="default_user_photo10.png", description="头像地址", required=true,
     *     type="string",
     *   ),
     *   @SWG\Response(
     *       response=200,description="successful operation"
     *   )
     * )
     */
    public function info()
    {
        $request = $this->request;
        $address = Helper::ipToAddress($request->getClientIp());
        $res = $this->weixin($request->code);
        if (!empty($res)) {
            $result = json_decode($res, true);
            $model = DB::table('users')->where(['open_id' => $result['openid'], 'is_real' => User::TYPE_REAL_PERSON])->first();
            $token = md5(md5($result['openid'] . $result['session_key']));
            list($province, $city) = City::simplifyCity($address['region'], $address['city']);
            $avatar = QiniuHelper::fetchImg($request->avatar)[0]['key'];
            $data = [
                'session_key' => $result['session_key'],
                'open_id' => $result['openid'],
                'email' => rand(1000000, 999999999) . '@163.com',
                'nickname' => $request->nickname ?: '佚名',
                'name' => $request->nickname ?: '佚名',
                'avatar' => $avatar ?: $this->getImage('default_user_photo10.png'),
                'is_real' => USER::TYPE_REAL_PERSON,
                'token' => $token,
                'ip' => $address['ip'],
                'country' => $address['country'],
                'province' => $province,
                'city' => $city,
            ];
            if ($model) {
                Redis::hdel('token', $model->token);
                DB::table('users')->where('id', $model->id)->update($data);
            } else {
                $model = new User();
                $model->saveData($data);
            }
            Redis::hset('token', $token, $model->id);
            self::showMsg(['token' => $token]);
        } else {
            self::showMsg(['token' => '']);
        }
    }

    /**
     * 批量注册用户
     */
    public function batchRegister()
    {
        $model = new User();
        for ($i = 0; $i < 1000; $i++) {
            $model->rebotRegister();
        }
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

    /**
     * /**
     * @SWG\Get(path="/api/user/center",
     *   tags={"用户中心"},
     *   summary="用户中心",
     *   description="Author: OYYM",
     *   @SWG\Parameter(name="token", in="header", default="1", description="用户token" ,required=true,
     *     type="string",
     *   ),
     *   @SWG\Response(
     *       response=200,description="
     *                  [avatar] => 头像
     *                  [nickname] => 佚名
     *                  [created_at] => 2018-07-21 00:42:40
     *                  [status] => 状态
     *                  [register_type] => 注册类型
     *                  [bid_currency] => 254.00 （拍币）
     *                  [gift_currency] => 0.00   （赠币）
     *                  [shopping_currency] => 256.00  （购物币）
     *     "
     *   )
     * )
     */
    public function center()
    {
        $this->auth();
        $user = $this->userIdent;
        $data = array(
            'avatar' => $user->getAvatar(),
            'nickname' => $user->nickname,
            'created_at' => $user->created_at,
            'status' => $user->status,
            'register_type' => User::REGISTER_TYPE_WEI_XIN,
            'bid_currency' => $this->userIdent->bid_currency,
            'gift_currency' => $this->userIdent->gift_currency,
            'shopping_currency' => $this->userIdent->shopping_currency,
        );
        self::showMsg($data);
    }

    /**
     * @SWG\Get(path="/api/user/shopping-currency",
     *   tags={"用户中心"},
     *   summary="我的购物币",
     *   description="Author: OYYM",
     *   @SWG\Parameter(name="token", in="header", default="1", description="用户token" ,required=true,
     *     type="string",
     *   ),
     *   @SWG\Response(
     *       response=200,description="successful operation"
     *   )
     * )
     */
    public function shoppingCurrency()
    {
        $this->auth();
        $income = new Income();
        self::showMsg($income->shoppingCurrency($this->userId));
    }



    /**
     * @SWG\Get(path="/api/user/property",
     *   tags={"用户中心"},
     *   summary="我的财产",
     *   description="Author: OYYM",
     *   @SWG\Parameter(name="token", in="header", default="1", description="用户token" ,required=true,
     *     type="string",
     *   ),
     *   @SWG\Parameter(name="limit", in="query", default="20", description="个数",
     *     type="string",
     *   ),
     *   @SWG\Parameter(name="pages", in="query", default="0", description="页数",
     *     type="string",
     *   ),
     *   @SWG\Response(
     *       response=200,description="successful operation"
     *   )
     * )
     */
    public function property()
    {
        $this->auth();
        $expend = new Expend();
        $expend->limit = $this->limit;
        $expend->offset = $this->offset;

        $income = new Income();
        $income->limit = $this->limit;
        $income->offset = $this->offset;
        $data = [
            'balance_desc' => [
                'id' => 1,
                'title' => '余额说明',
                'img' => '',
                'function' => 'html',
                'params' => [
                    'key' => 'url',
                    'type' => 'String',
                    'value' => $_SERVER["HTTP_HOST"] . '/api/balance-desc',
                ],
            ],
            'bid_currency' => $this->userIdent->bid_currency,
            'gift_currency' => $this->userIdent->gift_currency,
            'shopping_currency' => $this->userIdent->shopping_currency,
            'expend' => $expend->detail($this->userId),
            'income' => $income->detail($this->userId),
        ];
        self::showMsg($data);
    }

    /**
     * @SWG\Post(path="/api/user/address",
     *   tags={"用户中心"},
     *   summary="收货地址",
     *   description="Author: OYYM",
     *   @SWG\Parameter(name="token", in="header", default="1", description="用户token" ,required=true,
     *     type="string",
     *   ),
     *    @SWG\Parameter(name="address_id", in="formData", default="1", description="地址id,当传过来时表示修改",
     *     type="string",
     *   ),
     *   @SWG\Parameter(name="user_name", in="formData", default="王小明", description="", required=true,
     *     type="string",
     *   ),
     *   @SWG\Parameter(name="telephone", in="formData", default="18779284935", description="", required=true,
     *     type="string",
     *   ),
     *   @SWG\Parameter(name="province", in="formData", default="吉林省", description="", required=true,
     *     type="string",
     *   ),
     *   @SWG\Parameter(name="city", in="formData", default="通化市", description="", required=true,
     *     type="string",
     *   ),
     *   @SWG\Parameter(name="area", in="formData", default="东昌区", description="", required=true,
     *     type="string",
     *   ),
     *   @SWG\Parameter(name="detail_address", in="formData", default="西路103号", description="详细地址", required=true,
     *     type="string",
     *   ),
     *   @SWG\Parameter(name="postal", in="formData", default="134200", description="邮编", required=true,
     *     type="string",
     *   ),
     *   @SWG\Parameter(name="is_default", in="formData", default="1", description="是否默认 1=是 ，0= 否", required=true,
     *     type="string",
     *   ),
     *   @SWG\Response(
     *       response=200,description="successful operation"
     *   )
     * )
     */
    public function address()
    {
        $this->auth();
        $request = $this->request;
        $data = [
            'user_id' => $this->userId,
            'is_default' => $request->is_default,
            'user_name' => $request->user_name,
            'telephone' => $request->telephone,
            'postal' => $request->postal,
            'detail_address' => $request->detail_address,
            'str_address' => $request->province . '||' . $request->city . '||' . $request->area,
        ];
        if ($request->address_id) {
            $data['id'] = $request->address_id;
            if ((new UserAddress())->updateData($data)) {
                self::showMsg('保存成功！');
            } else {
                self::showMsg('保存失败！', 2);
            }
        } else {
            if ((new UserAddress())->saveData($data)) {
                self::showMsg('保存成功！');
            } else {
                self::showMsg('保存失败！', 2);
            }
        }
    }


    /**
     * @SWG\Get(path="/api/user/evaluate",
     *   tags={"用户中心"},
     *   summary="我的晒单",
     *   description="Author: OYYM",
     *   @SWG\Parameter(name="token", in="header", default="1", description="用户token" ,required=true,
     *     type="string",
     *   ),
     *   @SWG\Response(
     *       response=200,description="successful operation"
     *   )
     * )
     */
    public function evaluate()
    {
        $this->auth();
        self::showMsg((new Evaluate())->getList(['user_id' => $this->userId]));
    }
}
