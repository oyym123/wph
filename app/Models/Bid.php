<?php

namespace App\Models;

use App\Jobs\BidTask;
use App\User;
use Carbon\Carbon;
use EasyWeChat\Core\Exception;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Bid extends Common
{
    use SoftDeletes;

    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [
        'product_id',
        'period_id',
        'bid_price',
        'pay_amount',
        'pay_type',
        'user_id',
        'status',
        'bid_step',
        'nickname',
        'product_title',
        'end_time',
    ];

    const STATUS_SUCCESS = 1; //成功
    const STATUS_FAIL = 0;    //失败

    const TYPE_OUT = 0; //出局
    const TYPE_LEAD = 1; //领先

    protected $table = 'bid';

    /** 真人竞拍 */
    public function personBid($periodId, $auto = 0)
    {
        $periods = new Period();
        $period = $periods->getPeriod(['id' => $periodId, 'status' => Period::STATUS_IN_PROGRESS]);
        $products = new Product();
        $redis = app('redis')->connection('first');
        DB::table('period')->where(['id' => $periodId])->increment('bid_price', 0.1);//自增0.1
        DB::table('period')->where(['id' => $periodId])->update(['real_person' => Period::REAL_PERSON_YES]);//有真人参与
        $product = $products->getCacheProduct($period->product_id);
        $countdown = $redis->ttl('period@countdown' . $period->id);
        $rate = $period->bid_price / $product->sell_price;
        $time = date('Y-m-d H:i:s', time());
        $data = [
            'product_id' => $period->product_id,
            'period_id' => $periodId,
            'bid_price' => round($period->bid_price + $product->bid_step, 2),
            'user_id' => $this->userId,
            'status' => $this->isCanWinBid($period, $rate, $redis),
            'bid_step' => $product->bid_step,
            'pay_amount' => $product->pay_amount, //判断是否是10元专区
            'pay_type' => $this->amount_type ?: Income::TYPE_BID_CURRENCY,
            'nickname' => $this->userIdent->nickname,
            'product_title' => $product->title,
            'created_at' => $time,
            'updated_at' => $time,
            'end_time' => $time,
            'is_real' => User::TYPE_REAL_PERSON
        ];

        if ($auto == 0) { //表示没有自动竞拍，则记录支出,进行正常收费
            //判断消耗的金额类型
            if ($this->userIdent->gift_currency >= $data['pay_amount']) {
                $data['pay_type'] = self::TYPE_GIFT_CURRENCY; //当有赠币时，优先使用
                DB::table('users')->where(['id' => $this->userId])->decrement('gift_currency', $data['pay_amount']);
            } elseif ($this->userIdent->bid_currency >= $data['pay_amount']) {
                DB::table('users')->where(['id' => $this->userId])->decrement('bid_currency', $data['pay_amount']);
            } else {
                return [
                    'status' => 30, //余额不足，需要充值
                    'pay_amount' => $data['pay_amount'] - $this->userIdent->bid_currency
                ];
            }

            $expend = [
                'type' => $data['pay_type'],
                'user_id' => $this->userId,
                'amount' => $data['pay_amount'],
                'pay_amount' => $data['pay_amount'],
                'name' => '竞拍消费',
                'product_id' => $product->id,
                'period_id' => $periodId,
            ];
            (new Expend())->bidPay($expend);
        }

        if ($data['status'] == self::STATUS_FAIL) {
            //重置倒计时
            if ($countdown <= 10) {
                $redis->setex('period@countdown' . $period->id, 10, $data['bid_price']);
            }
            $data['id'] = DB::table('bid')->insertGetId($data);
            $this->setLastPersonId($redis, $data);
            //加入竞拍队列，进入数据库Bid表,暂时不启用redis队列
            //dispatch(new BidTask($redis, $data));
            $res = [
                'status' => 10,
            ];
            return $res;
        }
    }

    /** 设置最后一个竞拍人的id */
    public function setLastPersonId($redis, $data)
    {
        $redis->hset('bid@lastPersonId', $data['period_id'], json_encode($data));
    }

    /** 获取最后一个竞拍人的id */
    public function getLastBidInfo($redis, $periodId, $type = false)
    {
        $lastPersonIds = json_decode($redis->hget('bid@lastPersonId', $periodId));
        if (!empty($lastPersonIds)) {
            if (!empty($type)) {
                return $lastPersonIds->$type;
            } else {
                return $lastPersonIds;
            }
        } else {
            return 0;
        }
    }

    /** 每3秒检验，是否有中标的用户 */
    public function checkoutBid()
    {
        $redis = app('redis')->connection('first');
        $periods = new Period();
        $products = new Product();
        foreach ($periods->getAll() as $period) {
            $bid = $this->getLastBidInfo($redis, $period->id);
            if ($bid) {
                $product = $products->getCacheProduct($period->product_id);
                //当投标的价格小于售价时 , 则一直都不能竞拍成功
                if ($this->getLastBidInfo($redis, $period->id, 'bid_price') / $product->sell_price < 1) {
                    continue;
                }
                //到达平均售价时，机器人将不再参与竞拍,设置一个一年时间的key,当机器人参与的时候，判断是不是存在这个
                $redis->setex('realPersonBid@periodId' . $period->id, 60 * 24 * 365, $period->id);
                //当竞拍结束时
                if ($redis->ttl('period@countdown' . $period->id) < 0) {
                    $redis->setex('period@countdown' . $period->id, 10000, 'success');
                    //竞拍成功则立即保存
                    DB::table('bid')->where([
                        'id' => $bid->id
                    ])->update(['status' => self::STATUS_SUCCESS]);
                    //redis缓存也改变
                    $bid->status = self::STATUS_SUCCESS;
                    $this->setLastPersonId($redis, json_decode(json_encode($bid), true));
                    //转换状态
                    DB::table('period')->where(['id' => $period->id])->update([
                        'status' => Period::STATUS_OVER,
                        'user_id' => $bid->user_id,
                        'bid_end_time' => date('Y-m-d H:i:s', time()),
                        'bid_id' => $bid->id
                    ]);
                    //新增该产品新的期数
                    $periods->saveData($period->product_id);
                    //同时清除期数缓存
                    $this->delCache('period@allInProgress' . Period::STATUS_IN_PROGRESS);

                    if ($period->real_person == User::TYPE_REAL_PERSON) { //有真人参与则结算
                        //购物币返还结算
                        Income::settlement($period->id, $bid->user_id);
                    }

                    if ($bid->is_real == User::TYPE_REAL_PERSON) { //只有真人才需要走结算、订单流程
                        //自动拍币返还
                        (new AutoBid())->back($period->id, $bid->user_id);
                        //生成一个订单
                        $order = new Order();
                        $address = UserAddress::defaultAddress($bid->user_id);
                        $orderInfo = [
                            'sn' => $order->createSn(),
                            'pay_type' => Pay::TYPE_WEI_XIN,
                            'pay_amount' => $bid->bid_price,
                            'product_amount' => $product->sell_price,
                            'product_id' => $product->id,
                            'period_id' => $bid->period_id,
                            'status' => Order::STATUS_WAIT_PAY,
                            'buyer_id' => $bid->user_id,
                            'address_id' => $address->id, //收货人地址
                            'str_address' => str_replace('||', ' ', $address->str_address) . $address->detail_address,
                            'str_username' => $address->user_name, //收货人姓名
                            'str_phone_number' => $address->telephone, //手机号
                            'expired_at' => config('bid.order_expired_at'), //过期时间
                        ];
                        $order = $order->createOrder($orderInfo);
                        //转换状态
                        DB::table('period')->where(['id' => $period->id])->update([
                            'order_id' => $order->id,
                        ]);
                    }
                }
            }
        }
    }

    /** 判断是否可以中标 */
    public function isCanWinBid($period, $rate, $redis)
    {
        //当有真人参与时，机器人则一直跟拍
        if ($period->real_person) {
            return self::STATUS_FAIL;
        } else { //当没有真人参与时，判断是否到达开奖值
            if ($rate >= $period->robot_rate) {
                $redis->setex('period@robotSuccess' . $period->id, 10000, 'success');
                return self::STATUS_SUCCESS;
            }
        }
        return self::STATUS_FAIL;
    }

    /** 机器人竞价 */
    public function robotBid()
    {
        $periods = new Period();
        $products = new Product();
        $redis = app('redis')->connection('first');
        //获取所有正在进行中的期数,循环加入机器人竞拍，每8秒扫描一遍
        foreach ($periods->getAll() as $period) {
            //当有真人参与，且跟拍到平均价以上时，机器人将不跟拍
            if ($redis->ttl('realPersonBid@periodId' . $period->id) > 1) {
                echo $this->writeLog(['period_id' => $period->id, 'info' => '有真人参与，且跟拍到平均价以上，机器人将不跟拍']);
                continue;
            }
            $countdown = $redis->ttl('period@countdown' . $period->id);
            $flag = $redis->get('period@countdown' . $period->id);
            $success = $redis->get('period@robotSuccess' . $period->id);

            if ($countdown > 10) {
                echo $this->writeLog(['period_id' => $period->id, 'info' => '竞拍还未开始']);
                continue;
            }

            //当倒计时结束时,机器人将不会竞拍
            if ($success == 'success') {
                echo $this->writeLog(['period_id' => $period->id, 'info' => '竞拍倒计时结束']);
                continue;
            }

            $product = $products->getCacheProduct($period->product_id);

            if ($flag == $period->bid_price + $product->bid_step) {
                //减少竞拍次数
                echo $this->writeLog(['period_id' => $period->id, 'info' => '该时段已经竞拍过一次啦']);
                continue;
            }

            $robotPeriod = RobotPeriod::getInfo($period->id);
            DB::table('period')->where(['id' => $period->id])->increment('bid_price', 0.1);//自增0.1
            $rate = $period->bid_price / $product->sell_price;

            $time = date('Y-m-d H:i:s', time());
            $data = [
                'product_id' => $period->product_id,
                'period_id' => $period->id,
                'bid_price' => $period->bid_price + $product->bid_step,
                'user_id' => $robotPeriod->user_id,
                'status' => $this->isCanWinBid($period, $rate, $redis),
                'bid_step' => $product->bid_step,
                'pay_amount' => $product->pay_amount, //判断是否是10元专区
                'pay_type' => self::TYPE_BID_CURRENCY,
                'nickname' => $robotPeriod->nickname,
                'product_title' => $product->title,
                'created_at' => $time,
                'updated_at' => $time,
                'end_time' => $time,
                'is_real' => User::TYPE_ROBOT
            ];
            $this->setLastPersonId($redis, $data);
            if ($data['status'] == self::STATUS_SUCCESS) {
                //竞拍成功则立即保存
                $bid = Bid::create($data);
                //转换状态
                DB::table('period')->where(['id' => $period->id])->update([
                    'status' => Period::STATUS_OVER,
                    'user_id' => $robotPeriod->user_id,
                    'bid_end_time' => date('Y-m-d H:i:s', time()),
                    'bid_id' => $bid->id
                ]);
                //新增该产品新的期数
                $periods->saveData($period->product_id);
                //同时清除期数缓存
                $this->delCache('period@allInProgress' . Period::STATUS_IN_PROGRESS);
                //redis缓存也改变
                $data['id'] = $bid->id;
                $this->setLastPersonId($redis, $data);
            } else {
                $redis->setex('period@countdown' . $period->id, 10, $data['bid_price']);
                $data['id'] = DB::table('bid')->insertGetId($data);
                $this->setLastPersonId($redis, $data);
                //加入竞拍队列，3秒之后进入数据库Bid表
                //dispatch(new BidTask($data));
            }
        }
    }

    /** 获取竞拍记录 */
    public function bidRecord($periodId)
    {
        $data = [];
        $bids = Bid::has('user')->where(['period_id' => $periodId])->limit($this->limit)->orderBy('bid_price', 'desc')->get();
        foreach ($bids as $key => $bid) {
            $user = $bid->user;
            $data[] = [
                'bid_price' => $bid->bid_price,
                'bid_time' => $bid->end_time,
                'nickname' => $bid->nickname,
                'avatar' => '',
                'area' => $user->province . $user->city,
                'bid_type' => $key == 0 ? self::TYPE_LEAD : self::TYPE_OUT, //0 =出局 1=领先
            ];
        }
        return $data;
    }

    /** 竞拍最新的状态 */
    public function newestBid($periodIds)
    {
        //redis搜索
        $redis = app('redis')->connection('first');
        $res = [];
        foreach (explode(',', $periodIds) as $id) {
            if ($bid = $this->getLastBidInfo($redis, $id)) {
                $res[] = [
                    'a' => $bid->period_id,
                    'b' => $bid->pay_amount,
                    'c' => round($bid->bid_price, 2) . '0',
                    'd' => $bid->nickname,
                    'e' => $bid->pay_type,
                    'f' => $bid->status,
                    'g' => $bid->end_time,
                    'h' => ($x = $redis->ttl('period@countdown' . $bid->period_id)) > 0 ? $x : 0
                ];
            }
        }
        return $res;
        //mysql数据库搜索
        /*        $model = DB::table('bid')
                    ->select(DB::raw('max(bid_price) as bid_price,period_id'))
                    ->whereIn('period_id', $ids)
                    ->groupBy(['period_id'])
                    ->orderBy('bid_price', 'desc')
                    ->get();
                foreach ($model as $item) {
                    $bid = DB::table('bid')
                        ->select('status', 'period_id', 'nickname', 'bid_price', 'status', 'pay_type', 'end_time', 'pay_amount')
                        ->where([
                            'bid_price' => $item->bid_price,
                            'period_id' => $item->period_id
                        ])->first();
                    $res[] = [
                        'a' => $bid->period_id,
                        'b' => $bid->pay_amount,
                        'c' => $bid->bid_price,
                        'd' => $bid->nickname,
                        'e' => $bid->pay_type,
                        'f' => $bid->status,
                        'g' => $bid->end_time
                    ];
                }*/
    }

    /** 获取用户表信息 */
    public function User()
    {
        return $this->hasOne('App\User', 'id', 'user_id');
    }

    public function period()
    {
        return $this->belongsTo('App\Models\Period');
    }
}
