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
use App\Models\Period;
use App\Models\Product;
use App\Models\RechargeCard;
use App\Models\UserAddress;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class PayController extends WebController
{
    /**
     * @SWG\Get(path="/api/pay/recharge-center",
     *   tags={"支付"},
     *   summary="充值中心",
     *   description="Author: OYYM",
     *   @SWG\Response(
     *       response=200,description="successful operation"
     *   )
     * )
     */
    public function rechargeCenter()
    {
        self::showMsg((new RechargeCard())->lists());
    }

    /**
     * @SWG\Post(path="/api/pay/recharge",
     *   tags={"支付"},
     *   summary="充值",
     *   description="Author: OYYM",
     *   @SWG\Parameter(name="token", in="header", default="1", description="用户token" ,required=true,
     *     type="string",
     *   ),
     *   @SWG\Parameter(name="id", in="formData", default="1", description="充值卡id", required=true,
     *     type="string",
     *   ),
     *   @SWG\Response(
     *       response=200,description="
     *              [state] => 1
     *              [timeStamp] => 1534418590
     *              [nonceStr] => kREaNmSVurnZgUiegWmYkpkSfftTvvyK
     *              [signType] => MD5
     *              [package] => prepay_id=wx16192310839885759ce8cb760225893217
     *              [paySign] => CFA72D66F71040AC73FF22FC7FDD1F95
     *              [out_trade_no] => 201808161923103569554853
     *     "
     *   )
     * )
     */
    public function recharge()
    {
        $this->auth();
        $request = $this->request;
        $rechargeCard = new RechargeCard();
        $recharge = $rechargeCard->getRechargeCard(['id' => $request->id]);
        $order = new Order();
        $orderInfo = [
            'sn' => $order->createSn(),
            'pay_type' => Pay::TYPE_WEI_XIN,
            'pay_amount' => number_format($recharge->amount),
            'status' => Order::STATUS_WAIT_PAY,
            'type' => Order::TYPE_RECHARGE,
            'buyer_id' => $this->userId,
            'ip' => $request->getClientIp(),
            'gift_amount' => $recharge->gift_amount,
            'recharge_card_id' => $recharge->id,
        ];
        $res = [];
        DB::beginTransaction();
        try {
            $order = $order->createOrder($orderInfo);
            $pay = new Pay();
            $data = [
                'details' => '充值',
                'open_id' => $this->userIdent->open_id,
                'sn' => $order->sn,
                'order_id' => $order->id,
                'amount' => $order->pay_amount
            ];
            $res = $pay->WxPay($data);
            if ($res['state'] == 0) {
                throw new \Exception($res['result_msg']);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            self::showMsg($e->getMessage(), 4); // 等待处理
        }
        self::showMsg($res);
    }

    /**
     * @SWG\Post(path="/api/pay/pay",
     *   tags={"支付"},
     *   summary="立即购买",
     *   description="Author: OYYM",
     *   @SWG\Parameter(name="token", in="header", default="1", description="用户token" ,required=true,
     *     type="string",
     *   ),
     *   @SWG\Parameter(name="amount", in="formData", default="1", description="金额", required=true,
     *     type="string",
     *   ),
     *   @SWG\Parameter(name="product_id", in="formData", default="2", description="产品id", required=true,
     *     type="string",
     *   ),
     *   @SWG\Response(
     *       response=200,description="
     *              [state] => 1
     *              [timeStamp] => 1534418590
     *              [nonceStr] => kREaNmSVurnZgUiegWmYkpkSfftTvvyK
     *              [signType] => MD5
     *              [package] => prepay_id=wx16192310839885759ce8cb760225893217
     *              [paySign] => CFA72D66F71040AC73FF22FC7FDD1F95
     *              [out_trade_no] => 201808161923103569554853
     *     "
     *   )
     * )
     */
    public function pay()
    {
        $this->auth();
        $request = $this->request;
        //生成一个订单
        $order = new Order();
        $product = (new Product())->getCacheProduct($request->product_id);
        $address = UserAddress::defaultAddress($this->userId);
        if (!$address) {
            self::showMsg('请选择一个默认的收货地址！', 4);
        }
        $res = [];

        $data = [
            'amount' => $product->sell_price,
            'product_id' => $product->id,
            'user_id' => $this->userId,
        ];

        DB::beginTransaction();
        try {
            $orderInfo = [
                'sn' => $order->createSn(),
                'pay_type' => Pay::TYPE_WEI_XIN,
                'pay_amount' => $order->getPayAmount($data),
                'product_amount' => $product->sell_price,
                'product_id' => $product->id,
                'period_id' => (new Period())->nextPeriod($product->id),
                'status' => Order::STATUS_WAIT_PAY,
                'type' => Order::TYPE_BUY_BY_DIFF, //表示差价购买
                'buyer_id' => $this->userId,
                'address_id' => $address->id, //收货人地址
                'str_address' => str_replace('||', ' ', $address->str_address) . $address->detail_address,
                'str_username' => $address->user_name, //收货人姓名
                'ip' => $request->getClientIp(),
                'str_phone_number' => $address->telephone, //手机号
                'expired_at' => config('bid.order_expired_at'), //过期时间
            ];
            $order = $order->createOrder($orderInfo);
            $pay = new Pay();
            $data = [
                'details' => $product->title,
                'order_id' => $order->id,
                'open_id' => $this->userIdent->open_id,
                'sn' => $order->sn,
                'amount' => $order->pay_amount
            ];
            $res = $pay->WxPay($data);
            if ($res['state'] == 0) {
                throw new \Exception($res['result_msg']);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            self::showMsg($e->getMessage(), 4); // 等待处理
        }
        self::showMsg($res);
    }
}