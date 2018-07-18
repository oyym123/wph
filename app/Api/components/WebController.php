<?php
namespace App\Api\components;

use App\Helpers\Helper;
use EasyWeChat\Foundation\Application;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\UserInfo;

class WebController extends Controller
{

    public $enableCsrfValidation = false;
    public $offset = 0;
    public $skip = 0;
    public $psize = 10;
    public $userId = 0;
    public $userIdent = 0;
    public $token;
    public $weixin;

    /** 判断操作系统是不是windows，方便测试和开发 */
    public function isWindows()
    {
        if (PHP_OS == 'WINNT') {
            return true;
        };
    }

    /** 获取用户信息 */
    public function userInfo()
    {
        if (!$this->isWindows()) {  //本地的时候不需要网页授权
            $this->weixinWebOauth(); // 需要网页授权登录
        } else {
            session(['user_id' => 1]); //本地环境模拟用户id为1
            session()->save();
        }
        $userInfo = DB::table('user_info')->where('user_id', session('user_id'))->first();
    }

    /**
     * 解析并送出JSON
     * 200101
     * @param  array $res 资源数组，如果是一个字符串则当成错误信息输出
     * @param  int $state 状态值，默认为0
     * @param  int $msg 是否直接输出,1为返回值
     * @return array
     **/
    public static function showMsg($res, $code = 0, $msg = '成功')
    {
        //header("Content-type: application/json; charset=utf-8");

        if (empty($res)) {
            if ($res == []) {
                $res = [];
            } else {
                $res = '';
            }
        }
        // 构造数据
        $item = array('code' => $code, 'message' => $msg, 'data' => null);

        if (is_array($res) && !empty($res)) {
            $item['data'] = self::int2String($res); // 强制转换为string类型下放
        } elseif (is_string($res)) {
            $item['message'] = $res;
        }

        // 是否需要送出get
        if (isset($_GET['isget']) && $_GET['isget'] == 1) {
            $item['pars'] = !empty($_GET) ? $_GET : null;
        }

        //   if ((isset($_GET['debug']) && $_GET['debug'] == '1') || strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome') !== false) {
        if ((isset($_GET['debug']) && $_GET['debug'] == '1') || strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome') == true) {
            echo "<pre>";
            print_r($_REQUEST);
            print_r($item);
            //编码
            echo json_encode($item);
        } else {
            //编码
            $item = json_encode($item);
            // 送出信息
            echo "{$item}";
        }
        exit;
    }

    public static function int2String($arr)
    {
        foreach ($arr as $k => $v) {
            if (is_int($v)) {
                $arr[$k] = (string)$v;
            } else if (is_array($v)) { //若为数组，则再转义.
                $arr[$k] = self::int2String($v);
            }
        }
        return $arr;
    }

    public static function post($url, $post_data)
    {

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // post数据

        curl_setopt($ch, CURLOPT_POST, 1);
        // post的变量

        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

        $output = curl_exec($ch);

        curl_close($ch);
        //打印获得的数据

        return $output;
    }

    /** 根据user-agent取手机类型 */
    public static function getAppTypeByUa()
    {
        $tmp = 0;
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone') || strpos($_SERVER['HTTP_USER_AGENT'], 'iPad')) {
            $tmp = 1;
        } else if (strpos($_SERVER['HTTP_USER_AGENT'], 'Android')) {
            $tmp = 2;
        }
        return $tmp;
    }

    /** 缓存用户提交的数据（优化） */
    public function postSessionCache(Request $request, $name, $params)
    {
        //所有提交数据存在“用户ip+名称”字段里
        session([$request->getClientIp() . $name => $params]);
    }


    /** 获取微信授权 */
    static function weixin($code)
    {
        //声明CODE，获取小程序传过来的CODE
        $appid = env('WEIXIN_APP_ID');
        $secret = env('WEIXIN_SECRET');
        //api接口
        $api = "https://api.weixin.qq.com/sns/jscode2session?appid={$appid}&secret={$secret}&js_code={$code}&grant_type=authorization_code";
        $res = json_decode(Helper::get($api), true);
        session_start();
        session(['']);
        print_r($res);
        exit;
        return [
            $res['session_key'],
            $res['openid']
        ];
    }
}


