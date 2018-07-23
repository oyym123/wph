<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

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
    ];

    protected $table = 'period';


    const STATUS_NOT_START = 0;
    const STATUS_IN_PROGRESS = 1;
    const STATUS_OVER = 2;

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
                'user_id' => $period->user_id,
                'nickname' => $period->user ? $period->user->nickname : '',
                'title' => $product->title,
                'bid_step' => $product->bid_step,
                'end_time' => $period->bid_end_time,
                'img_cover' => $product->img_cover,
                'product_id' => $product->id,
                'sell_price' => $product->sell_price,
            ];
        }
        return $data;
    }

    /** 获取产品列表 */
    public function getProductList()
    {
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
        return $data;
    }

    /**
     * 根据产品保存期数
     */
    public function saveData($product_id)
    {
        $dayStart = date('Y-m-d', time()) . ' 00:00:00';
        $dayEnd = date('Y-m-d', time()) . ' 23:59:59';

        $check = DB::table('period')
            ->whereBetween('created_at', [$dayStart, $dayEnd])
            ->where('product_id', '=', $product_id)
            ->orderBy('created_at', 'desc')
            ->first();

        $period = $check ? intval(substr($check->code, -4)) : 0;

        $code = date('Ymd', time()) . str_pad($period + 1, 4, '0', STR_PAD_LEFT);

        $data = [
            'product_id' => $product_id,
            'code' => $code,
        ];
        $model = self::create($data);
        $model->save();
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
}
