<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Product extends Common
{
    use SoftDeletes;

    protected $table = 'product';

    const BUY_BY_DIFF_NO = 0;
    const BUY_BY_DIFF_YES = 1;
    public static $buyByDiff = [
        self::BUY_BY_DIFF_NO => '不可差价购',
        self::BUY_BY_DIFF_YES => '可差价购',
    ];

    /** 获取产品数量 */
    public static function counts()
    {
        return DB::table('product')->where(['status' => 1])->count();
    }

    /** 获取缓存的产品信息 */
    public function getCacheProduct($productId)
    {
        $key = 'product@find' . $productId;
        if ($this->hasCache($key)) {
            return $this->getCache($key);
        } else {
            return $this->putCache($key, Product::find($productId), 10);
        }
    }

    public function getProduct($id)
    {
        if ($model = Product::find($id)) {
            return $model;
        }
        list($info, $status) = $this->returnRes('', self::CODE_NO_DATA);
        self::showMsg($info, $status);
    }
}
