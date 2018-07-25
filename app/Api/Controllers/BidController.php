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
     *   @SWG\Parameter(name="name", in="formData", default="1", description="", required=true,
     *     type="string",
     *   ),
     *   @SWG\Parameter(name="token", in="header", default="1", description="用户token" ,required=true,
     *     type="string",
     *   ),
     *   @SWG\Parameter(name="product_id", in="formData", default="1", description="", required=true,
     *     type="string",
     *   ),
     *   @SWG\Parameter(name="period_id", in="formData", default="7", description="", required=true,
     *     type="string",
     *   ),
     *   @SWG\Response(
     *       response=200,description="successful operation"
     *   )
     * )
     */
    public function bidding()
    {
        $bid = new Bid();
        $bid->saveData($this->request);
    }
}