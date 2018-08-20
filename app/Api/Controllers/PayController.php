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
     * @SWG\Get(path="/api/pay/confirm",
     *   tags={"支付"},
     *   summary="订单提交页面",
     *   description="Author: OYYM",
     *   @SWG\Parameter(name="token", in="header", default="1", description="用户token" ,required=true,
     *     type="string",
     *   ),
     *   @SWG\Parameter(name="product_id", in="query", default="1", description="产品id" ,required=true,
     *     type="string",
     *   ),
     *   @SWG\Parameter(name="period_id", in="query", default="1", description="期数id【从我的竞拍】" ,
     *     type="string",
     *   ),
     *   @SWG\Parameter(name="sn", in="query", default="1", description="订单号" ,
     *     type="string",
     *   ),
     *   @SWG\Response(
     *       response=200,description="successful operation"
     *   )
     * )
     */
    public function confirm()
    {


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
     *   @SWG\Parameter(name="product_id", in="formData", default="2", description="产品id", required=true,
     *     type="string",
     *   ),
     *   @SWG\Parameter(name="period_id", in="formData", default="396【有的话就传，没有不传】", description="期数id",
     *     type="string",
     *   ),
     *   @SWG\Parameter(name="sn", in="formData", default="2", description="订单号【有的话就传，没有不传】",
     *     type="string",
     *   ),
     *   @SWG\Parameter(name="used_shopping", in="formData", default="2", description="是否使用购物币【1=使用,0=不使用】", required=true,
     *     type="string",
     *   ),
     *   @SWG\Parameter(name="message", in="formData", default="2", description="留言", required=true,
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
        $orderObj = new Order();
        $product = (new Product())->getCacheProduct($request->product_id);
        $address = UserAddress::defaultAddress($this->userId);
        if (!$address) {
            self::showMsg('请选择一个默认的收货地址！', 4);
        }
        $res = [];
        $payAmount = $product->sell_price;
        $isShop = 0;

        if ($request->period_id) { //当有期数id,表示参与过竞拍
            $period = (new Period())->getPeriod([
                'id' => $request->period_id,
                'product_id' => $product->id,
                'status' => Period::STATUS_OVER
            ]);
            if ($period->user_id == $this->userId) {
                $type = Order::TYPE_BID;
                $payAmount = $period->bid_price;
            } else {
                $type = Order::TYPE_BUY_BY_DIFF;
            }
        } else {  //否则都当成差价购处理
            $type = Order::TYPE_SHOP;
            if ($product->is_shop) {
                $isShop = $product->is_shop;
            }
        }

        $data = [
            'amount' => $payAmount,
            'product_id' => $product->id,
            'shopping_currency' => $this->userIdent->shopping_currency,
            'user_id' => $this->userId,
            'is_shop' => $isShop //当is_shop = 1时表示全购物币购买
        ];

        $order = null;
        $amount = $product->sell_price;

        if ($request->sn) {
            $order = Order::where([
                'sn' => $request->sn,
                'buyer_id' => $this->userId
            ])->first();
        }

        if ($request->used_shopping) {
            $amount = $orderObj->getPayAmount($data);
        }

        DB::beginTransaction();
        try {
            $orderInfo = [
                'pay_type' => Pay::TYPE_WEI_XIN,
                'pay_amount' => $amount,
                'product_amount' => $product->sell_price,
                'product_id' => $product->id,
                'period_id' => (new Period())->nextPeriod($product->id),
                'status' => Order::STATUS_WAIT_PAY,
                'type' => $type,
                'buyer_id' => $this->userId,
                'address_id' => $address->id, //收货人地址
                'str_address' => str_replace('||', ' ', $address->str_address) . $address->detail_address,
                'str_username' => $address->user_name, //收货人姓名
                'ip' => $request->getClientIp(),
                'str_phone_number' => $address->telephone, //手机号
                'expired_at' => config('bid.order_expired_at'), //过期时间
            ];

            if ($order) {
                $order = Order::where(['id' => $order->id])->update($orderInfo);
            } else {
                $orderInfo['sn'] = $orderObj->createSn();
                $order = $orderObj->createOrder($orderInfo);
            }

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