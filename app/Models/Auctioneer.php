<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class Auctioneer extends Common
{
    protected $table = 'auctioneer';

    const AUCTION_ID = 1;
    const AUCTION_NAME = '诺诺拍卖行';
    const AUCTION_AVATAR = '';

    /** 获取拍卖师id和名称 */
    public static function getName()
    {
        $model = DB::table('auctioneer')->select('id', 'name')
            ->where(['status' => self::STATUS_ENABLE])
            ->get();
        return self::getNameId($model);
    }

    /**
     * 获取随机拍卖师
     */
    public static function randAuctioneer()
    {
        $model = DB::table('auctioneer')->select('id')
            ->where('status', self::STATUS_ENABLE)
            ->inRandomOrder()
            ->first();
        return $model->id;
    }

}
