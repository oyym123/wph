<?php

namespace App\Api\Controllers;

use App\Api\components\WebController;
use App\Models\Auctioneer;
use Encore\Admin\Controllers\ModelForm;

class AuctioneerController extends WebController
{
    use ModelForm;

    /**
     * /**
     * @SWG\Get(path="/api/auctioneer",
     *   tags={"拍卖师"},
     *   summary="",
     *   description="Author: OYYM",
     *   @SWG\Parameter(name="name", in="query", default="", description="", required=true,
     *     type="string",
     *   ),
     *   @SWG\Response(
     *       response=200,description="successful operation"
     *   )
     * )
     */
    public function index()
    {
        $userInfo = Auctioneer::find(1)->toArray();
        self::showMsg($userInfo);
    }
}
