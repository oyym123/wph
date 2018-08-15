<?php
/**
 * Created by PhpStorm.
 * User: Alienware
 * Date: 2018/8/10
 * Time: 15:43
 */

namespace App\Api\Controllers;


use App\Api\components\WebController;
use App\Models\Order;
use App\Models\Pay;
use Illuminate\Support\Facades\Request;

class PayController extends WebController
{

    /**
     * @SWG\Post(path="/api/pay/wx-pay",
     *   tags={"支付"},
     *   summary="微信支付",
     *   description="Author: OYYM",
     *   @SWG\Parameter(name="token", in="header", default="1", description="用户token" ,required=true,
     *     type="string",
     *   ),
     *   @SWG\Parameter(name="amount", in="formData", default="1", description="金额", required=true,
     *     type="string",
     *   ),
     *   @SWG\Response(
     *       response=200,description="successful operation"
     *   )
     * )
     */
    public function WxPay()
    {
        $this->auth();
        $user = $this->userIdent;
        $request = $this->request;
        $fee = $request->amount;
        $details = '充值';//商品的详情，比如iPhone8，紫色
        // $fee = 0.01;//举例充值0.01
        $appid = config('bid.wx_app_id');//appid
        $body = $details;// '金邦汇商城';//'【自己填写】'
        $mch_id = config('bid.wx_mch_id');//'你的商户号【自己填写】'
        $nonce_str = $this->nonce_str();//随机字符串
        $notify_url = $_SERVER["HTTP_HOST"] . '/api/newbie-guide';//回调的url【自己填写】';
        $total_fee = $fee * 100;//因为充值金额最小是1 而且单位为分 如果是充值1元所以这里需要*100
        $order = new Order();
        $orderInfo = [
            'sn' => $order->createSn(),
            'pay_type' => Pay::TYPE_WEI_XIN,
            'pay_amount' => $total_fee,
            'status' => Order::STATUS_WAIT_PAY,
            'type' => Order::TYPE_BID, //表示竞拍类型订单
            'buyer_id' => $this->userId,
        ];
        
        $order = $order->createOrder($orderInfo);
        $openid = $user->open_id;//'用户的openid【自己填写】';
        $out_trade_no = $this->order_number($openid);//商户订单号
        $spbill_create_ip = '116.62.212.29';//'服务器的ip【自己填写】';

        $trade_type = 'JSAPI';//交易类型 默认
        //这里是按照顺序的 因为下面的签名是按照顺序 排序错误 肯定出错
        $post['appid'] = $appid;
        $post['body'] = $body;

        $post['mch_id'] = $mch_id;

        $post['nonce_str'] = $nonce_str;//随机字符串

        $post['notify_url'] = $notify_url;

        $post['openid'] = $openid;

        $post['out_trade_no'] = $out_trade_no;

        $post['spbill_create_ip'] = $spbill_create_ip;//终端的ip

        $post['total_fee'] = $total_fee;//总金额 最低为一块钱 必须是整数

        $post['trade_type'] = $trade_type;
        $sign = $this->sign($post);//签名
        $post_xml = '<xml>
           <appid>' . $appid . '</appid>
           <body>' . $body . '</body>
           <mch_id>' . $mch_id . '</mch_id>
           <nonce_str>' . $nonce_str . '</nonce_str>
           <notify_url>' . $notify_url . '</notify_url>
           <openid>' . $openid . '</openid>
           <out_trade_no>' . $out_trade_no . '</out_trade_no>
           <spbill_create_ip>' . $spbill_create_ip . '</spbill_create_ip>
           <total_fee>' . $total_fee . '</total_fee>
           <trade_type>' . $trade_type . '</trade_type>
           <sign>' . $sign . '</sign>
        </xml> ';
        //统一接口prepay_id
        $url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
        $xml = $this->http_request($url, $post_xml);

        $array = $this->xml($xml);//全要大写
        //  print_r($array);exit;
        if ($array['return_code'] == 'SUCCESS' && $array['result_code'] == 'SUCCESS') {
            $time = time();
            $tmp = [];//临时数组用于签名
            $tmp['appId'] = $appid;
            $tmp['nonceStr'] = $nonce_str;
            $tmp['package'] = 'prepay_id=' . $array['prepay_id'];
            $tmp['signType'] = 'MD5';
            $tmp['timeStamp'] = "$time";

            $data['state'] = 1;
            $data['timeStamp'] = "$time";//时间戳
            $data['nonceStr'] = $nonce_str;//随机字符串
            $data['signType'] = 'MD5';//签名算法，暂支持 MD5
            $data['package'] = 'prepay_id=' . $array['prepay_id'];//统一下单接口返回的 prepay_id 参数值，提交格式如：prepay_id=*
            $data['paySign'] = $this->sign($tmp);//签名,具体签名方案参见微信公众号支付帮助文档;
            $data['out_trade_no'] = $out_trade_no;
        } else {
            $data['state'] = 0;
            $data['text'] = "错误";
            $data['return_code'] = $array['return_code'];
            $data['result_msg'] = $array['result_msg'];
        }
        self::showMsg($data);
    }

    //随机32位字符串
    private function nonce_str()
    {
        $result = '';
        $str = 'QWERTYUIOPASDFGHJKLZXVBNMqwertyuioplkjhgfdsamnbvcxz';
        for ($i = 0; $i < 32; $i++) {
            $result .= $str[rand(0, 48)];
        }
        return $result;
    }

//生成订单号
    private function order_number($openid)
    {
        //date('Ymd',time()).time().rand(10,99);//18位
        return md5($openid . time() . rand(10, 99));//32位
    }

//签名 $data要先排好顺序
    public function sign($data)
    {
        $stringA = '';
        foreach ($data as $key => $value) {
            if (!$value) continue;
            if ($stringA) $stringA .= '&' . $key . "=" . $value;
            else $stringA = $key . "=" . $value;
        }
        $wx_key = '3173259eA0d5E5d12e3a9b2c90N0Qdb0';//申请支付后有给予一个商户账号和密码，登陆后自己设置key
        $stringSignTemp = $stringA . '&key=' . $wx_key;//申请支付后有给予一个商户账号和密码，登陆后自己设置key
        return strtoupper(md5($stringSignTemp));
    }

//curl请求啊
    function http_request($url, $data = null, $headers = array())
    {
        $curl = curl_init();
        if (count($headers) >= 1) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        }
        curl_setopt($curl, CURLOPT_URL, $url);

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);

        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }

    //将XML转为array
    private function xml($xml)
    {
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $values;
    }
//微信支付结束
}