<?php

namespace App\Api\Controllers;

use App\Api\components\WebController;
use App\Models\Period;
use Illuminate\Http\Request;

class HomeController extends WebController
{
    public function successView(Request $request)
    {
        // echo encrypt(12321);
        return view('api.home.success', ['data' => $request->input()]);
    }

    /**
     * @SWG\Get(path="/api/home/banner",
     *   tags={"首页"},
     *   summary="首页banner",
     *   description="Author: OYYM",
     *   @SWG\Parameter(name="name", in="query", default="", description="",
     *     type="string",
     *   ),
     *   @SWG\Response(
     *       response=200,description="successful operation"
     *   )
     * )
     */
    public function banner()
    {
        $data = array(
            0 =>
                array(
                    'id' => 5,
                    'title' => '新手指引',
                    'img' => env('QINIU_URL_IMAGES') . '1485314751522.jpg',
                    'function' => 'html',
                    'params' =>
                        array(
                            0 =>
                                array(
                                    'key' => 'url',
                                    'type' => 'String',
                                    'value' => $_SERVER["HTTP_HOST"] . '/api/newbie-guide',
                                ),
                            1 =>
                                array(
                                    'key' => 'to_promotion_status',
                                    'type' => 'string',
                                    'value' => 0,
                                ),
                        ),
                    'to_promotion_status' => 0,
                ),
        );
        self::showMsg($data);
    }

    /**
     * @SWG\Get(path="/api/demo/demo",
     *   tags={"demo"},
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
    public function module()
    {
        $data = array(
            0 =>
                array(
                    'id' => 11,
                    'title' => '充值',
                    'img' => '1490015569219',
                    'function' => 'recharge.html',
                    'params' =>
                        array(),
                ),
            1 =>
                array(
                    'id' => 13,
                    'title' => '10元专区',
                    'img' => '1490015605523',
                    'function' => 'goods_list.html',
                    'params' =>
                        array(
                            0 =>
                                array(
                                    'key' => 'pr',
                                    'type' => 'undefined',
                                    'value' => '10',
                                ),
                        ),
                ),
            2 =>
                array(
                    'id' => 14,
                    'title' => '晒单',
                    'img' => '1490015587751',
                    'function' => 'share.html',
                    'params' =>
                        array(),
                ),
            3 =>
                array(
                    'id' => 12,
                    'title' => '常见问题',
                    'img' => '1490015634162',
                    'function' => 'html',
                    'params' =>
                        array(
                            0 =>
                                array(
                                    'key' => 'url',
                                    'type' => 'String',
                                    'value' => 'https://m.gogobids.com/h_service.html',
                                ),
                        ),
                ),
        );
        self::showMsg($data);
    }

    /**
     *
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
     * @SWG\Get(path="/api/home/deal-end",
     *   tags={"首页"},
     *   summary="闪拍头条&已完成商品的接口数据",
     *   description="Author: OYYM",
     *   @SWG\Parameter(name="name", in="query", default="", description="",
     *     type="string",
     *   ),
     *   @SWG\Response(
     *       response=200,description="
     *           [id] => 期数id
     *           [period_code] => 期数代码
     *           [bid_price] => 竞拍价格
     *           [user_id] => 用户id
     *           [nickname] => 用户昵称
     *           [title] => 标题
     *           [bid_step] => 竞拍单价
     *           [end_time] => 成交时间
     *           [img_cover] => 产品封面
     *           [product_id] => 产品id
     *           [sell_price] => 产品售价 "
     *   )
     * )
     */
    public function dealEnd()
    {
        $model = new Period();
        self::showMsg($model->dealEnd());
    }

    /**
     * /**
     * @SWG\Get(path="/api/home/hot-auction",
     *   tags={"首页"},
     *   summary="正在热拍",
     *   description="Author: OYYM",
     *   @SWG\Parameter(name="name", in="query", default="", description="",
     *     type="string",
     *   ),
     *   @SWG\Response(
     *       response=200,description="successful operation"
     *   )
     * )
     */
    public function hotAuction()
    {

        $data = array(
            'code' => '0000',
            'message' => '成功',
            'data' =>
                array(
                    0 =>
                        array(
                            'a' => 4477599,
                            'c' => 7660,
                            'd' => '1353.20',
                            'h' => '卡西欧',
                            'g' => 1252796071,
                            'b' => 1,
                            'i' => 10,
                            'e' => 0,
                            'f' => 0,
                        ),
                    1 =>
                        array(
                            'a' => 4478576,
                            'c' => 7203,
                            'd' => '1211.60',
                            'h' => 'mc乐少',
                            'g' => 1960617520,
                            'b' => 1,
                            'i' => 10,
                            'e' => 0,
                            'f' => 0,
                        ),
                    2 =>
                        array(
                            'a' => 4486960,
                            'c' => 2649,
                            'd' => '13.90',
                            'h' => '聪明阿呆',
                            'g' => 1940412557,
                            'b' => 1,
                            'i' => 10,
                            'e' => 0,
                            'f' => 0,
                        ),
                    3 =>
                        array(
                            'a' => 4486818,
                            'c' => 6533,
                            'd' => '31.20',
                            'h' => '不会唱情歌',
                            'g' => 1643758403,
                            'b' => 1,
                            'i' => 10,
                            'e' => 0,
                            'f' => 0,
                        ),
                    4 =>
                        array(
                            'a' => 4483224,
                            'c' => 3809,
                            'd' => '341.10',
                            'h' => '辉A哥',
                            'g' => 1041285855,
                            'b' => 1,
                            'i' => 10,
                            'e' => 0,
                            'f' => 0,
                        ),
                    5 =>
                        array(
                            'a' => 4478786,
                            'c' => 4058,
                            'd' => '840.80',
                            'h' => '踏取',
                            'g' => 1235053492,
                            'b' => 1,
                            'i' => 10,
                            'e' => 0,
                            'f' => 0,
                        ),
                    6 =>
                        array(
                            'a' => 4483621,
                            'c' => 3308,
                            'd' => '389.70',
                            'h' => '鹏鹏',
                            'g' => 1319687339,
                            'b' => 1,
                            'i' => 10,
                            'e' => 0,
                            'f' => 0,
                        ),
                    7 =>
                        array(
                            'a' => 4486002,
                            'c' => 4447,
                            'd' => '122.90',
                            'h' => '浩辰潮鞋',
                            'g' => 1283653549,
                            'b' => 1,
                            'i' => 10,
                            'e' => 0,
                            'f' => 0,
                        ),
                    8 =>
                        array(
                            'a' => 4486054,
                            'c' => 7136,
                            'd' => '89.90',
                            'h' => '靠山',
                            'g' => 1118111351,
                            'b' => 1,
                            'i' => 10,
                            'e' => 0,
                            'f' => 0,
                        ),
                    9 =>
                        array(
                            'a' => 4487097,
                            'c' => 7588,
                            'd' => '1.30',
                            'h' => '丹妞高傲与生俱来',
                            'g' => 1656370338,
                            'b' => 1,
                            'i' => 10,
                            'e' => 0,
                            'f' => 0,
                        )
                ),
        );
        self::showMsg($data);
    }
}
