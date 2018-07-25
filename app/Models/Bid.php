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

    protected $table = 'bid';

    /** 保存竞拍数据 */
    public function saveData($request)
    {
        DB::table('period')->where(['id' => $request->period_id])->increment('bid_price', 0.1);//自增0.1
        DB::table('period')->where(['id' => $request->period_id])->update(['real_person' => 1]);//有真人参与
        $time = date('Y-m-d H:i:s', time());
        $data = [
            'product_id' => $request->product_id,
            'period_id' => $request->period_id,
            'bid_price' => $request->bid_price,
            'user_id' => $this->userId,
            'status' => $this->isCanWinBid(),
            'bid_step' => 1,
            'nickname' => $this->userIdent->nickname,
            'product_title' => $request->product_title,
            'created_at' => $time,
            'updated_at' => $time,
            'end_time' => $time
        ];
        //加入竞拍队列，10秒之后进入数据库Bid表
        $model = (new BidTask($data))->delay(Carbon::now()->addSeconds(10));
        dispatch($model);
    }

    /** 判断是否可以中标 */
    public function isCanWinBid($period, $rate)
    {
        //当有真人参与时，机器人则一直跟拍
        if ($period->real_person) {
            if ($rate >= 1) {
                $robot = false;
            }
        } else { //当没有真人参与时，判断是否到达开奖值
            if ($rate >= $period->robot_rate) {
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
        foreach ($periods->getAll() as $period) {
            $robotPeriod = RobotPeriod::getInfo($period->id);
            DB::table('period')->where(['id' => $period->id])->increment('bid_price', 0.1);//自增0.1
            $product = $products->getCacheProduct($period->product_id);
            $rate = $period->bid_price / $product->sell_price;
            $time = date('Y-m-d H:i:s', time());
            $data = [
                'product_id' => $period->product_id,
                'period_id' => $period->id,
                'bid_price' => $period->bid_price + 0.1,
                'user_id' => $robotPeriod->user_id,
                'status' => $this->isCanWinBid($period, $rate),
                'bid_step' => 1,
                'nickname' => $robotPeriod->nickname,
                'product_title' => $product->title,
                'created_at' => $time,
                'updated_at' => $time,
                'end_time' => $time
            ];
            //竞拍成功则立即保存，同时清除期数缓存
            if ($data['status'] == self::STATUS_SUCCESS) {
                Bid::create($data);
                DB::table('period')->where(['id' => $period->id])->update(['status' => Period::STATUS_OVER]);//自增0.1
                $this->delCache('period@allInProgress' . Period::STATUS_IN_PROGRESS);
            } else {
                //加入竞拍队列，3秒之后进入数据库Bid表
                $model = (new BidTask($data))->delay(Carbon::now()->addSeconds(3));
                dispatch($model);
            }
        }
    }
}
