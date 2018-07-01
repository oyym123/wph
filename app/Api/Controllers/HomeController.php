<?php

namespace App\Api\Controllers;

use App\Api\components\WebController;
use Illuminate\Http\Request;

class HomeController extends WebController
{
    public function successView(Request $request)
    {
        return view('api.home.success', ['data' => $request->input()]);
    }

    /**
     * /**
     * @SWG\Get(path="/api/home",
     *   tags={"首页"},
     *   summary="",
     *   description="Author: OYYM",
     *   @SWG\Parameter(name="name", in="query", default="", description="",
     *     type="string",
     *   ),
     *   @SWG\Response(
     *       response=200,description="successful operation"
     *   )
     * )
     *
     */
    public function index()
    {
        $data = [
            'banner' => [
                'http://od83l5fvw.bkt.clouddn.com/1485314751522.jpg',
                'http://od83l5fvw.bkt.clouddn.com/1485314751522.jpg'
            ],
            'display_module' => [
                [
                    'img' => 'http://od83l5fvw.bkt.clouddn.com/1499684610679.jpg',
                    'title' => '签到'
                ],
                [
                    'img' => 'http://od83l5fvw.bkt.clouddn.com/1499684610679.jpg',
                    'title' => '充值'
                ],
                [
                    'img' => 'http://od83l5fvw.bkt.clouddn.com/1499684610679.jpg',
                    'title' => '10元专区'
                ],
                [
                    'img' => 'http://od83l5fvw.bkt.clouddn.com/1499684610679.jpg',
                    'title' => '晒单'
                ],
                [
                    'img' => 'http://od83l5fvw.bkt.clouddn.com/1499684610679.jpg',
                    'title' => '常见问题'
                ],
            ]
        ];

        self::showMsg($data);
    }

    /**
     * /**
     * @SWG\Get(path="/api/home/dealEnd",
     *   tags={"首页"},
     *   summary="闪拍头条&已完成商品的接口数据，一次取多条，前端定时展示，展示完再调用该接口",
     *   description="Author: OYYM",
     *   @SWG\Response(
     *       response=200,description="successful operation"
     *   )
     * )
     */
    public function dealEnd()
    {
        $data = [
            [
                'title' => '泰迪熊公仔',
                'user_name' => '韩芸汐',
                'price' => '10.80',
                'img' => 'http://od83l5fvw.bkt.clouddn.com/1499684610679.jpg',
                'product_id' => 1,
                'is_10_area' => '0 = 不是 ,1 = 是(需要加上10元专区图标)'
            ],
            [
                'title' => '泰迪熊公仔',
                'user_name' => '韩芸汐',
                'price' => '10.80',
                'img' => 'http://od83l5fvw.bkt.clouddn.com/1499684610679.jpg',
                'product_id' => 2,
                'is_10_area' => '0 = 不是 ,1 = 是(需要加上10元专区图标)'
            ],
            [
                'title' => '泰迪熊公仔',
                'user_name' => '韩芸汐',
                'price' => '10.80',
                'img' => 'http://od83l5fvw.bkt.clouddn.com/1499684610679.jpg',
                'product_id' => 2,
                'is_10_area' => '0 = 不是 ,1 = 是(需要加上10元专区图标)'
            ],
            [
                'title' => '泰迪熊公仔',
                'user_name' => '韩芸汐',
                'price' => '10.80',
                'img' => 'http://od83l5fvw.bkt.clouddn.com/1499684610679.jpg',
                'product_id' => 2,
                'is_10_area' => '0 = 不是 ,1 = 是(需要加上10元专区图标)'
            ],
        ];
        self::showMsg($data);
    }


    /**
     * /**
     * @SWG\Get(path="/api/home/hotAuction",
     *   tags={"首页"},
     *   summary="正在热拍",
     *   description="Author: OYYM",
     *   @SWG\Parameter(name="name", in="query", default="", description="", required=true,
     *     type="string",
     *   ),
     *   @SWG\Response(
     *       response=200,description="successful operation"
     *   )
     * )
     */
    public function hotAuction()
    {
        $data = [
            [
                'title' => '泰迪熊公仔',
                'user_name' => '韩芸汐',
                'price' => '10.80',
                'img' => 'http://od83l5fvw.bkt.clouddn.com/1499684610679.jpg',
                'product_id' => 1,
                'is_10_area' => '0 = 不是 ,1 = 是(需要加上10元专区图标)'
            ],
            [
                'title' => '泰迪熊公仔',
                'user_name' => '韩芸汐',
                'price' => '10.80',
                'img' => 'http://od83l5fvw.bkt.clouddn.com/1499684610679.jpg',
                'product_id' => 2,
                'is_10_area' => '0 = 不是 ,1 = 是(需要加上10元专区图标)'
            ],
            [
                'title' => '泰迪熊公仔',
                'user_name' => '韩芸汐',
                'price' => '10.80',
                'img' => 'http://od83l5fvw.bkt.clouddn.com/1499684610679.jpg',
                'product_id' => 2,
                'is_10_area' => '0 = 不是 ,1 = 是(需要加上10元专区图标)'
            ],
            [
                'title' => '泰迪熊公仔',
                'user_name' => '韩芸汐',
                'price' => '10.80',
                'img' => 'http://od83l5fvw.bkt.clouddn.com/1499684610679.jpg',
                'product_id' => 2,
                'is_10_area' => '0 = 不是 ,1 = 是(需要加上10元专区图标)'
            ],
        ];
        self::showMsg($data);
    }
}
