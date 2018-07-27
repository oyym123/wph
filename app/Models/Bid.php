<?php

namespace App\Models;

use App\Jobs\BidTask;
use App\User;
use Carbon\Carbon;
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
    public function personBid($request)
    {
        $redis = app('redis')->connection('first');
        DB::table('period')->where(['id' => $request->period_id])->increment('bid_price', 0.1);//自增0.1
        DB::table('period')->where(['id' => $request->period_id])->update(['real_person' => Period::REAL_PERSON_YES]);//有真人参与
        $products = new Product();
        $periods = new Period();
        $period = $periods->getPeriod($request->period_id, ['status' => Period::STATUS_IN_PROGRESS]);
        $product = $products->getCacheProduct($period->product_id);
        $countdown = $redis->ttl('period@countdown' . $period->id);
        $rate = $period->bid_price / $product->sell_price;
        $time = date('Y-m-d H:i:s', time());
        $data = [
            'product_id' => $period->product_id,
            'period_id' => $request->period_id,
            'bid_price' => $period->bid_price,
            'user_id' => $this->userIdent->id,
            'status' => $this->isCanWinBid($period, $rate, $redis),
            'bid_step' => 1,
            'nickname' => $this->userIdent->nickname,
            'product_title' => $product->title,
            'created_at' => $time,
            'updated_at' => $time,
            'end_time' => $time,
            'is_real' => User::TYPE_REAL_PERSON
        ];

        if ($data['status'] == self::STATUS_SUCCESS) {
            //竞拍成功则立即保存
            $bid = Bid::create($data);
            //转换状态
            DB::table('period')->where(['id' => $period->id])->update([
                'status' => Period::STATUS_OVER,
                'user_id' => $this->userId,
                'bid_id' => $bid->id
            ]);
            //新增该产品新的期数
            $periods->saveData($period->product_id);
            //同时清除期数缓存
            $this->delCache('period@allInProgress' . Period::STATUS_IN_PROGRESS);
            $res = [
                'status' => 20,
            ];
        } else {
            //重置倒计时
            if ($countdown <= 10) {
                $redis->setex('period@countdown' . $period->id, 10, $data['bid_price']);
            }
            //加入竞拍队列，3秒之后进入数据库Bid表
            $model = (new BidTask($data));
            dispatch($model);
            $res = [
                'status' => 10,
            ];
        }
        return $res;
    }

    /** 判断是否可以中标 */
    public function isCanWinBid($period, $rate, $redis)
    {
        //当有真人参与时，机器人则一直跟拍
        if ($period->real_person) {
            if ($rate >= 1) { //到达平均售价时，机器人将不再参与竞拍,设置一个一年时间的key,当机器人参与的时候，判断是不是存在这个
                $redis->setex('realPersonBid@periodId' . $period->id, 60 * 24 * 365, $period->id);
                //当竞拍结束时
                if ($redis->ttl('period@countdown' . $period->id) < 0) {
                    $redis->setex('period@countdown' . $period->id, 10000, 'success');
                    return self::STATUS_SUCCESS;
                }
            }
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

            if ($flag == $period->id . ($period->bid_price + $product->bid_step)) {
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
                'bid_step' => 1,
                'nickname' => $robotPeriod->nickname,
                'product_title' => $product->title,
                'created_at' => $time,
                'updated_at' => $time,
                'end_time' => $time,
                'is_real' => User::TYPE_ROBOT
            ];

            if ($data['status'] == self::STATUS_SUCCESS) {
                //竞拍成功则立即保存
                $bid = Bid::create($data);
                //转换状态
                DB::table('period')->where(['id' => $period->id])->update([
                    'status' => Period::STATUS_OVER,
                    'user_id' => $robotPeriod->user_id,
                    'bid_id' => $bid->id
                ]);
                //新增该产品新的期数
                $periods->saveData($period->product_id);
                //同时清除期数缓存
                $this->delCache('period@allInProgress' . Period::STATUS_IN_PROGRESS);
            } else {
                $redis->setex('period@countdown' . $period->id, 10, $data['bid_price']);
                //加入竞拍队列，3秒之后进入数据库Bid表
                $model = (new BidTask($data));
                dispatch($model);
            }
        }
    }

    /** 获取竞拍记录 */
    public function bidRecord($periodId)
    {
        $data = [];
        $bids = Bid::has('user')->where(['period_id' => $periodId])->limit(100)->orderBy('end_time', 'desc')->get();
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

    /** 获取用户表信息 */
    public function User()
    {
        return $this->hasOne('App\User', 'id', 'user_id');
    }
}
