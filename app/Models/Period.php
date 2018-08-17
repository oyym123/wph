<?php

namespace App\Models;

use DeepCopy\f001\B;
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
     *
     * @var array
     */
    protected $fillable = [
        'product_id',
        'code',
        'status',
        'auctioneer_id',
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

    /** 获取成交数据 */
    public function dealEnd($where = [])
    {
        $data = [];
        $periods = Period::has('product')->where($where + ['status' => self::STATUS_OVER])
            ->offset($this->offset)->limit($this->limit)->orderBy('updated_at', 'desc')->get();
        foreach ($periods as $period) {
            $product = $period->product;
            $savePrice = ($x = round(((1 - ($period->bid_price / $product->sell_price)) * 100), 1)) > 0 ? $x : 0.0;
            $data[] = [
                'id' => $period->id,
                'period_code' => $period->code,
                'bid_price' => $period->bid_price,
                'nickname' => $period->user ? $period->user->nickname : '',
                'avatar' => $period->user ? $period->user->getAvatar() : '',
                'title' => $product->title,
                'short_title' => $product->title,
                'bid_step' => $product->bid_step,
                'save_price' => $savePrice,
                'end_time' => $period->bid_end_time,
                'img_cover' => env('QINIU_URL_IMAGES') . $product->img_cover,
                'product_id' => $product->id,
                'sell_price' => $product->sell_price,
                'product_type' => $product->type,
            ];
        }
        return $data;
    }

    /** 获取下一期的period_id */
    public function nextPeriod($productId)
    {
        $period = Period::where([
            'product_id' => $productId,
            'status' => self::STATUS_IN_PROGRESS
        ])->select(['id'])->orderBy('created_at', 'desc')->first();
        if ($period) {
            return $period->id;
        } else {
            self::showMsg('该产品暂时没有竞拍活动!', 4);
        }
    }

    /** 获取产品列表 */
    public function getProductList($type = 1, $data = [])
    {
        $cacheKey = 'period@getProductList' . $this->offset;
        if ($this->hasCache($cacheKey)) {
            return $this->getCache($cacheKey);
        }

        $where = [
            'deleted_at' => null,
            'status' => self::STATUS_IN_PROGRESS
        ];

        if ($type == 2) {   //我在拍
            $expend = DB::table('expend')
                ->select('period_id')
                ->where(['user_id' => $this->userId])
                ->groupBy('period_id')
                ->get()->toArray();
            $where = $where + [
                    'id' => array_column($expend, 'period_id'),
                ];
            if (empty($expend)) {
                self::showMsg('没有我在拍数据', self::CODE_NO_DATA);
            }
        } elseif ($type == 3) { //我收藏
            $collectIds = DB::table('collection')->select('product_id')->where([
                'user_id' => $this->userId,
                'status' => Collection::STATUS_COLLECTION_YES
            ])->get()->toArray();

            $where = $where + [
                    'product_id' => array_column($collectIds, 'product_id'),
                ];
            if (empty($collectIds)) {
                self::showMsg('没有数据', self::CODE_NO_DATA);
            }
        } elseif ($type == 5) { //拍卖师分类
            $where = $where + [
                    'auctioneer_id' => $data['auctioneer_id'],
                ];
        }

        if ($type == 4) { //产品类型分类
            $where = [];
            if (!empty($this->request->type)) {
                $where = ['product.type' => $this->request->type];
            }
            $periods = DB::table('period')
                ->join('product', 'product.id', '=', 'period.product_id')
                ->where([
                        'period.deleted_at' => null,
                        'period.status' => self::STATUS_IN_PROGRESS
                    ] + $where)->offset($this->offset)->limit($this->limit)->get();
        } else {
            $periods = DB::table('period')->where($where)->offset($this->offset)->limit($this->limit)->get();
        }

        if (count($periods) == 0) {
            self::showMsg('没有数据', self::CODE_NO_DATA);
        }

        $data = [];
        $collection = new Collection();
        foreach ($periods as $period) {
            $product = Product::find($period->product_id);
            $data[] = [
                'id' => $period->id,
                'product_id' => $product->id,
                'period_code' => $period->code,
                'title' => $product->title,
                'img_cover' => self::getImg($product->img_cover),
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
        $period = $this->getPeriod(['id' => $id]);
        $product = $period->product;
        $auctioneer = $period->Auctioneer;
        $collection = new Collection();
//       $bid = new Bid();
//        $bid->limit = 3;
        $userCount = DB::table('bid')->select('user_id')->where(['period_id' => $period->id])->groupBy(['user_id'])->get();
        $this->limit = 6;
        $proxy = [];
        if ($this->userId > 0) {
            $proxy = AutoBid::isAutoBid($this->userId, $period->id);
        }
        $redis = app('redis')->connection('first');
        $data = [
            'detail' => [
                'id' => $period->id,
                'period_status' => $period->status == self::STATUS_IN_PROGRESS ? 1 : 0,
                'product_id' => $period->product_id,
                'period_code' => $period->code,
                'title' => $product->title,
                'product_type' => $product->type,
                'img_cover' => $product->getImgCover(),
                'imgs' => self::getImgs($product->imgs),
                'sell_price' => $product->sell_price,
                'bid_step' => $product->pay_amount,
                'price_add_length' => $product->price_add_length,
                'init_price' => $product->init_price,
                'countdown' => $product->countdown_length,
                'countdown_length' => ($x = $redis->ttl('period@countdown' . $period->id)) > 0 ? $x : 0,
                'is_gift_bids_enable' => 1,
                'collection_users_count' => $product->collection_count,
                'bid_users_count' => count($userCount),
                'bid_count' => ($period->bid_price * 10) / $product->pay_amount,
                'buy_by_diff' => $product->buy_by_diff,
                'settlement_bid_id' => $period->bid_id,
                'auctioneer_id' => $period->auctioneer_id,
                'is_favorite' => $collection->isCollect($this->userId, $product->id),
                'product_status' => $product->status,
                'return_proportion' => config('bid.return_proportion') * 100,
                'tags_img' => self::getImg('weipaihangbanner.png'),
                'auction_avatar' => Auctioneer::AUCTION_AVATAR,
                'auction_id' => Auctioneer::AUCTION_ID,
                'auction_name' => Auctioneer::AUCTION_NAME,
                'auctioneer_avatar' => self::getImg($auctioneer->image),
                'auctioneer_tags' => $auctioneer->tags,
                'auctioneer_license' => $auctioneer->number,
                'auctioneer_name' => $auctioneer->name,
            ],
            'expended' => [
                'used_real_bids' => 0,
                'used_gift_bids' => 0,
                'used_money' => '0.00',
                'is_buy_differential_able' => $product->buy_by_diff,
                'buy_differential_money' => '0.00',
                'order_sn' => '',
                'order_type' => '',
                'need_to_bided_pay' => 0,
                'need_to_bided_pay_price' => '0.00',
                'return_shop_bids' => 0,
                'pay_status' => 0,
                'pay_time' => 0,
            ],
            'past_deal' => array_chunk($this->dealEnd(['product_id' => $product->id]), 3),
            'proxy' => $proxy,
            'price' =>
                array(
                    'd' => 0,
                    'c' => $period->bid_price,
                    'h' => '',
                    'g' => '',
                    'b' => '',
                    'e' => '',
                    'f' => '',
                    'a' => '',
                ),
            //'bid_records' => $bid->bidRecord($period->id)
        ];
        return $data;
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
    public function getAll($status = [self::STATUS_IN_PROGRESS])
    {
        $cacheKey = 'period@allInProgress' . json_encode($status);
        if ($this->hasCache($cacheKey)) {
            return $this->getCache($cacheKey);
        } else {
            $periods = DB::table('period')->where([
                'deleted_at' => null
            ])->whereIn('status', $status)->get();
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
        $redis->setex('period@countdown' . $model->id, config('bid.init_countdown'), 1);
        RobotPeriod::batchSave($model->id, $productId);
    }


    /** 历史成交走势图 */
    public function historyTrend($productId)
    {
        $periods = Period::where([
            'product_id' => $productId,
            'status' => self::STATUS_OVER
        ])->offset($this->offset)->limit($this->limit)->orderBy('created_at', 'desc')->get();
        $list = $bidPrices = $data = [];
        $products = new Product();
        $product = $products->getCacheProduct($productId);
        foreach ($periods as $period) {
            $bidPrices[] = $period->bid_price;
        }
        $averagePrice = round(array_sum($bidPrices) / count($bidPrices), 2);
        foreach ($periods as $period) {

            $list[] = [
                'code' => $period->code,
                'price' => $period->bid_price,
            ];
            if ($period->bid_price - $averagePrice > 0) {
                $flag = 1;
            } else {
                $flag = 0;
            }
            $data[] = [
                'end_time' => $period->bid_end_time,
                'bid_price' => $period->bid_price,
                'flag' => $flag,
                'diff_price' => abs($period->bid_price - $averagePrice),
                'nickname' => $period->user ? $period->user->nickname : '',
            ];
        }
        $res = [
            'img' => $product->getImgCover(),
            'title' => $product->title,
            'present_price' => $data[0]['bid_price'],
            'max_price' => min($bidPrices),
            'min_price' => max($bidPrices),
            'average_price' => $averagePrice,
            'detail' => $data,
            'list' => $list
        ];
        return $res;
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


    /** 获取拍卖师表信息 */
    public function Order()
    {
        return $this->hasOne('App\Models\Order', 'id', 'order_id');
    }


//    public function bid()
//    {
//        return $this->hasMany('App\Models\Bid', 'period_id', 'id');
//    }

    public function getPeriod($where = [])
    {
        if ($model = Period::where($where)->first()) {
            return $model;
        }
        list($info, $status) = $this->returnRes('', self::CODE_NO_DATA);
        self::showMsg($info, $status);
    }
}
