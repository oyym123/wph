<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class Collection extends Common
{
    protected $table = 'collection';
    const STATUS_COLLECTION_YES = 1;
    const STATUS_COLLECTION_NO = 0;

    /** 判断是否收藏 */
    public function isCollect($userId, $productId)
    {
        $res = DB::table('collection')->where([
            'status' => self::STATUS_COLLECTION_YES,
            'user_id' => $userId,
            'product_id' => $productId
        ])->first();
        return !empty($res) ? self::STATUS_COLLECTION_YES : self::STATUS_COLLECTION_NO;
    }
}
