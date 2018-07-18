<?php
/**
 * Created by PhpStorm.
 * User: Alienware
 * Date: 2018/7/17
 * Time: 11:03
 */

namespace App\Api\Controllers;

use App\Models\Bid;
use Illuminate\Http\Request;
use App\Api\components\WebController;

class BidController extends WebController
{
    /**
     * @SWG\Post(path="/api/bid/bidding",
     *   tags={"竞拍"},
     *   summary="每个用户竞拍时的接口",
     *   description="Author: OYYM",
     *   @SWG\Parameter(name="name", in="formData", default="", description="", required=true,
     *     type="string",
     *   ),
     *   @SWG\Parameter(name="open_id", in="formData", default="", description="", required=true,
     *     type="string",
     *   ),
     *   @SWG\Parameter(name="product_id", in="formData", default="", description="", required=true,
     *     type="string",
     *   ),
     *   @SWG\Parameter(name="period_id", in="formData", default="", description="", required=true,
     *     type="string",
     *   ),
     *   @SWG\Response(
     *       response=200,description="successful operation"
     *   )
     * )
     */
    public function bidding(Request $request)
    {
        print_r($request->input('name'));
        $data = [
            'product_id',
            'period_id',
            'bid_price',
            'user_id',
            'status',
            'bid_step',
            'nickname',
            'product_title',
            'end_time',
        ];
        $model = new Bid();
        $model->saveData($data);
    }
}