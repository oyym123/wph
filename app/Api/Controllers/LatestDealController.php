<?php
/**
 * Created by PhpStorm.
 * User: Alienware
 * Date: 2018/7/2
 * Time: 23:03
 */

namespace App\Api\Controllers;


use App\Api\components\WebController;
use App\Models\Period;

class LatestDealController extends WebController
{
    /**
     * /**
     * @SWG\Get(path="/api/latest-deal",
     *   tags={"最新成交"},
     *   summary="最新成交列表",
     *   description="Author: OYYM",
     *   @SWG\Parameter(name="name", in="query", default="", description="",
     *     type="string",
     *   ),
     *   @SWG\Response(
     *       response=200,description="successful operation"
     *   )
     * )
     */
    public function index()
    {
        $data = array(
            0 =>
                array(
                    'id' => 4487428,
                    'nickname' => '最后的块钱了',
                    'avatar' => 'https://qnimg.gogobids.com/avatar/b1b0aeb05bdd01d0015bdd0697cc3bf8',
                    'img_cover' => '1509076497121',
                    'bid_price' => '5.10',
                    'title' => '卫龙 辣条 亲嘴烧1250g/袋',
                    'short_title' => '卫龙 亲嘴烧',
                ),
        );
        self::showMsg($data);
    }
}