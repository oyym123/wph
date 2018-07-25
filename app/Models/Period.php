<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class Period extends Common
{
    use SoftDeletes;

    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [
        'product_id',
        'code',
        'status',
        'robot_rate',
        'person_rate',
    ];

    protected $table = 'period';

    const STATUS_NOT_START = 10;
    const STATUS_IN_PROGRESS = 20;
    const STATUS_OVER = 30;

    const REAL_PERSON_YES = 1;
    const REAL_PERSON_NO = 0;

    public static function getStatus($key = 999)
    {
        $data = [
            self::STATUS_NOT_START => '未开始',
            self::STATUS_IN_PROGRESS => '正在进行',
            self::STATUS_OVER => '已结束',
        ];
        return $key != 999 ? $data[$key] : $data;
    }

    /** 获取闪拍头条数据 */
    public function dealEnd()
    {
        $data = [];
        $periods = Period::has('product')->where(['status' => self::STATUS_OVER])->limit(3)->orderBy('updated_at', 'desc')->get();
        foreach ($periods as $period) {
            $product = $period->product;
            $data[] = [
                'id' => $period->id,
                'period_code' => $period->code,
                'bid_price' => $period->bid_price,
                'nickname' => $period->user ? $period->user->nickname : '',
                'avatar' => $period->user ? $period->user->getAvatar() : '',
                'title' => $product->title,
                'short_title' => $product->title,
                'bid_step' => $product->bid_step,
                'end_time' => $period->bid_end_time,
                'img_cover' => $product->img_cover,
                'product_id' => $product->id,
                'sell_price' => $product->sell_price,
            ];
        }
        return $this->getCache('period@dealEnd', $data, 1);
    }

    /** 获取产品列表 */
    public function getProductList()
    {
        $cacheKey = 'period@getProductList';
        if ($this->hasCache($cacheKey)) {
            return $this->getCache($cacheKey);
        }

        $data = [];
        $periods = DB::table('period')->where([
            'deleted_at' => null
        ])->offset($this->offset)->limit($this->limit)->get();
        $collection = new Collection();
        foreach ($periods as $period) {
            $product = Product::find($period->product_id);
            $data[] = [
                'id' => $period->id,
                'product_id' => $product->id,
                'period_code' => $period->code,
                'title' => $product->title,
                'img_cover' => $product->img_cover,
                'sell_price' => $product->sell_price,
                'bid_step' => $product->bid_step,
                'is_favorite' => $collection->isCollect($this->userId, $product->id),
            ];
        }
        return $this->putCache($cacheKey, $data, 0.1);
    }

    /** 获取产品详情 */
    public function getProductDetail($id)
    {
        $period = $this->getPeriod($id);
        $product = $period->product;
        $auctioneer = $period->Auctioneer;
        $collection = new Collection();
        $data = [
            'detail' => [
                'id' => $period->id,
                'period_status' => $period->status,
                'product_id' => $period->product_id,
                'period_code' => $period->code,
                'title' => $product->title,
                'product_type' => $product->type,
                'img_cover' => $product->img_cover,
                'imgs' => $product->imgs,
                'sell_price' => $product->sell_price,
                'bid_step' => $product->step,
                'price_add_length' => $product->price_add_length,
                'init_price' => $product->init_price,
                'countdown_length' => $product->countdown_length,
                'is_voucher_bids_enable' => 1,
                'buy_by_diff' => $product->buy_by_diff,
                'settlement_bid_id' => $period->bid_id,
                'auctioneer_id' => $period->auctioneer_id,
                'is_favorite' => $collection->isCollect($this->userId, $product->id),
                'product_status' => $product->status,
                'default_offer' => 5,
                'offer_ladder' => '10,20,50,66',
                'have_show' => 0,
                'auction_avatar' => Auctioneer::AUCTION_AVATAR,
                'auction_id' => Auctioneer::AUCTION_ID,
                'auction_name' => Auctioneer::AUCTION_NAME,
                'auctioneer_avatar' => $auctioneer->image,
                'auctioneer_license' => $auctioneer->number,
                'auctioneer_name' => $auctioneer->name,
            ],
            'expended' => [
                'used_real_bids' => 0,
                'used_voucher_bids' => 0,
                'used_money' => '0.00',
                'is_buy_differential_able' => 0,
                'buy_differential_money' => '0.00',
                'order_id' => NULL,
                'order_type' => NULL,
                'need_to_bided_pay' => 0,
                'need_to_bided_pay_price' => '0.00',
                'return_bids' => 0,
                'return_shop_bids' => 0,
                'pay_status' => 0,
                'pay_time' => 0,
            ],
            'bid_records' => [
                0 =>
                    array(
                        'area' => '江西南昌',
                        'bid_nickname' => '不可忽视的激情',
                        'bid_no' => 3775,
                        'bid_price' => '377.50',
                    ),
                1 =>
                    array(
                        'area' => '陕西西安',
                        'bid_nickname' => 'appouu',
                        'bid_no' => 3774,
                        'bid_price' => '377.40',
                    ),
                2 =>
                    array(
                        'area' => '江西南昌',
                        'bid_nickname' => '不可忽视的激情',
                        'bid_no' => 3773,
                        'bid_price' => '377.30',
                    ),
            ]
        ];
        return $data;
        $data = array(
            'proxy' =>
                array(),
            'price' =>
                array(
                    'c' => 0,
                    'd' => '377.50',
                    'h' => NULL,
                    'g' => NULL,
                    'b' => NULL,
                    'e' => NULL,
                    'f' => NULL,
                    'a' => NULL,
                ),
            'bid_records' =>
                array(
                    0 =>
                        array(
                            'area' => '江西南昌',
                            'bid_nickname' => '不可忽视的激情',
                            'bid_no' => 3775,
                            'bid_price' => '377.50',
                        ),
                    1 =>
                        array(
                            'area' => '陕西西安',
                            'bid_nickname' => 'appouu',
                            'bid_no' => 3774,
                            'bid_price' => '377.40',
                        ),
                    2 =>
                        array(
                            'area' => '江西南昌',
                            'bid_nickname' => '不可忽视的激情',
                            'bid_no' => 3773,
                            'bid_price' => '377.30',
                        ),
                ),
        );
    }

//    /** 是否有真人参与 */
//    public function isRealPerson($bidPrice)
//    {
//        $period = DB::table('period')->where([
//            'id' => $bidPrice,
//            'real_person' => self::REAL_PERSON_YES
//        ])->first();
//        return $this->getCache('period@isRealPerson' . $bidPrice, $period, 1);
//    }

    /** 获取所有期数，默认进行中 */
    public function getAll($status = self::STATUS_IN_PROGRESS)
    {
        $cacheKey = 'period@allInProgress' . $status;
        if ($this->hasCache($cacheKey)) {
            return $this->getCache($cacheKey);
        } else {
            $periods = DB::table('period')->where([
                'status' => $status,
                'deleted_at' => null
            ])->get();
            return $this->putCache($cacheKey, $periods, 0.1);
        }
    }

    /**
     * 根据产品保存期数
     */
    public function saveData($productId)
    {
        $dayStart = date('Y-m-d', time()) . ' 00:00:00';
        $dayEnd = date('Y-m-d', time()) . ' 23:59:59';

        $check = DB::table('period')
            ->whereBetween('created_at', [$dayStart, $dayEnd])
            ->where('product_id', '=', $productId)
            ->orderBy('created_at', 'desc')
            ->first();

        $period = $check ? intval(substr($check->code, -4)) : 0;
        $code = date('Ymd', time()) . str_pad($period + 1, 4, '0', STR_PAD_LEFT);
        $data = [
            'product_id' => $productId,
            'auctioneer_id' => Auctioneer::randAuctioneer(),
            'status' => self::STATUS_IN_PROGRESS,
            'robot_rate' => config('bid.robot_rate'),
            'person_rate' => mt_rand(100, 150) / 100,
            'code' => $code,
        ];
        $model = self::create($data);
        $redis = app('redis')->connection('first');
        //设置倒计时初始时间和初始价格
        $redis->setex('period@countdown' . $model->id, config('bid.init_countdown'), 0);
        RobotPeriod::batchSave($model->id, $productId);
    }


    /** 获取产品表信息 */
    public function Product()
    {
        return $this->hasOne('App\Models\Product', 'id', 'product_id');
    }

    /** 获取用户表信息 */
    public function User()
    {
        return $this->hasOne('App\User', 'id', 'user_id');
    }

    /** 获取拍卖师表信息 */
    public function Auctioneer()
    {
        return $this->hasOne('App\Models\Auctioneer', 'id', 'auctioneer_id');
    }

    public function getPeriod($id)
    {
        if ($model = Period::find($id)) {
            return $model;
        }
        list($info, $status) = $this->returnRes('', self::CODE_NO_DATA);
        self::showMsg($info, $status);
    }
}
