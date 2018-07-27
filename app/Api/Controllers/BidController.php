<?php
/**
 * Created by PhpStorm.
 * User: Alienware
 * Date: 2018/7/17
 * Time: 11:03
 */

namespace App\Api\Controllers;

use App\Models\Bid;
use App\Api\components\WebController;

class BidController extends WebController
{
    /**
     * @SWG\Post(path="/api/bid/bidding",
     *   tags={"竞拍"},
     *   summary="每个用户竞拍时的接口",
     *   description="Author: OYYM",
     *   @SWG\Parameter(name="token", in="header", default="1", description="用户token" ,required=true,
     *     type="string",
     *   ),
     *   @SWG\Parameter(name="period_id", in="formData", default="7", description="", required=true,
     *     type="string",
     *   ),
     *   @SWG\Response(
     *       response=200,description="
     *              [status] => 10 = 请求成功,但是竞拍失败，当获取到这个状态时，刷新倒计时为10秒 |  20 = 请求成功，且竞拍也成功 ，获取到这个状态时，表示竞拍结束
     *              "
     *   )
     * )
     */
    public function bidding()
    {
        $this->auth();
        $bid = new Bid();
        $bid->userIdent = $this->userIdent;
        self::showMsg($bid->personBid($this->request));
    }


    /**
     * @SWG\Get(path="/api/bid/bid-record",
     *   tags={"竞拍"},
     *   summary="出价记录",
     *   description="Author: OYYM",
     *   @SWG\Parameter(name="period_id", in="query", default="16", description="",
     *     type="string",
     *   ),
     *   @SWG\Response(
     *       response=200,description="
     *              [bid_price] => 价格
     *              [bid_time] => 2018-07-26 17:56:40
     *              [nickname] => 昵称
     *              [avatar] => 头像
     *              [area] => 地址
     *              [bid_type] => 0 = 出局 , 1 = 领先
     *     "
     *   )
     * )
     */
    public function bidRecord()
    {
        $bid = new Bid();
        self::showMsg($bid->bidRecord($this->request->period_id));
    }


}