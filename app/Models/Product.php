<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Product extends Common
{
    use SoftDeletes;

    protected $table = 'product';

    const BUY_BY_DIFF_NO = 0;
    const BUY_BY_DIFF_YES = 1;

    const SHOPPING_YES = 1; //加入购物币专区
    const SHOPPING_NO = 0;  //不加入购物币专区

    public static $buyByDiff = [
        self::BUY_BY_DIFF_NO => '不可差价购',
        self::BUY_BY_DIFF_YES => '可差价购',
    ];

    /** 加入购物币专区 */
    public static function getIsShop($key = 999)
    {
        $data = [
            self::SHOPPING_YES => '加入购物币专区',
            self::SHOPPING_NO => '不加入购物币专区',
        ];
        return $key != 999 ? $data[$key] : $data;
    }

    public function setImgsAttribute($pictures)
    {
        if (is_array($pictures)) {
            $this->attributes['imgs'] = json_encode($pictures);
        }
    }

    public function getImgsAttribute($pictures)
    {
        return json_decode($pictures, true) ?: [];
    }

    /** 获取产品数量 */
    public static function counts($today = 0)
    {
        if ($today) {
            return DB::table('product')->where(['status' => self::STATUS_ENABLE])
                ->whereBetween('created_at', [date('Y-m-d', time()), date('Y-m-d', time()) . ' 23:59:59'])
                ->count();
        } else {
            return DB::table('product')->where(['status' => self::STATUS_ENABLE])->count();
        }
    }

    /** 获取缓存的产品信息 */
    public function getCacheProduct($productId)
    {
        $key = 'product@find' . $productId;
        if ($this->hasCache($key)) {
            return $this->getCache($key);
        } else {
            $data = $this->getProduct($productId);
            return $this->putCache($key, $data, 10);
        }
    }

    public function getImgCover()
    {
        return env('QINIU_URL_IMAGES') . $this->img_cover;
    }

    /** 判断是否是10元专区 */
    public function isTen()
    {
        return $this->type == 1 ? 1 : 0;
    }

    public function getProduct($id)
    {
        if ($model = Product::find($id)) {
            return $model;
        }
        self::showMsg('该产品不存在!', self::CODE_NO_DATA);
    }

    /** 购物币专区 */
    public function shopList()
    {
        $products = Product::where([
            'is_shop' => self::SHOPPING_YES,
            'status' => self::STATUS_ENABLE
        ])->offset($this->offset)->limit($this->limit)->get();
        $data = [];
        foreach ($products as $product) {
            $data[] = [
                'product_id' => $product->id,
                'title' => $product->title,
                'sell_price' => $product->sell_price,
                'img_cover' => $product->getImgCover(),
            ];
        }
        return $data;
    }

    /** 购物币专区详情 */
    public function shopDetail($productId)
    {
        $product = $this->getCacheProduct($productId);
        if ($product->is_shop == self::SHOPPING_YES) {
            $collection = new Collection();
            return [
                'product_id' => $product->id,
                'title' => $product->title,
                'sell_price' => $product->sell_price,
                'is_favorite' => $collection->isCollect($this->userId, $product->id),
                'img_cover' => $product->getImgCover(),
                'imgs' => array_merge(self::getImgs($product->imgs),[$product->getImgCover()]),
                'evaluate' => (new Evaluate())->getList(['product_id' => $productId])
            ];
        } else {
            return [];
        }
    }
}
