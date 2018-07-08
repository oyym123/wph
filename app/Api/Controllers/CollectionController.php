<?php
/**
 * Created by PhpStorm.
 * User: Alienware
 * Date: 2018/7/3
 * Time: 0:11
 */

namespace App\Api\Controllers;


use App\Api\components\WebController;

class CollectionController extends WebController
{

    /**
     * @SWG\Get(path="/api/collection",
     *   tags={"收藏"},
     *   summary="收藏列表",
     *   description="Author: OYYM",
     *   @SWG\Parameter(name="user_id", in="query", default="", description="", required=true,
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
                    'id' => 447958812,
                    'product_id' => 800,
                    'title' => '佳能 EOS 6D Mark II 单反套机（EF 24-105mm f/3.5-5.6 IS STM 镜头）',
                    'period_code' => '201807020001',
                    'img_cover' => '1513934849851',
                    'sell_price' => '15729.00',
                    'bid_step' => 1,
                    'is_favorite' => 1,
                ),
        );
        self::showMsg($data);
    }

    /**
     * @SWG\Get(path="/api/collection/collect",
     *   tags={"收藏"},
     *   summary="收藏或者取消收藏",
     *   description="Author: OYYM",
     *   @SWG\Parameter(name="product_id", in="query", default="", description="",
     *     type="string",
     *   ),
     *   @SWG\Response(
     *       response=200,description="successful operation"
     *   )
     * )
     */
    public function collect()
    {
        $data = array(
            0 =>
                array(
                    'product_id' => '800',
                    'is_favorite' => 1,
                ),
        );
        self::showMsg($data);
    }

}