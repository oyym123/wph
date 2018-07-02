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
     * @SWG\Get(path="/api/home/deal-end",
     *   tags={"首页"},
     *   summary="闪拍头条&已完成商品的接口数据，一次取多条，前端定时展示，展示完再调用该接口",
     *   description="Author: OYYM",
     *   @SWG\Parameter(name="name", in="query", default="", description="",
     *     type="string",
     *   ),
     *   @SWG\Response(
     *       response=200,description="successful operation"
     *   )
     * )
     */
    public function dealEnd()
    {
        $data = array(
                0 =>
                    array(
                        'id' => 4487037,
                        'title' => '大洋世家 阿根廷红虾L1 2kg盒 30-40只 海鲜大虾',
                        'bid_step' => 1,
                        'end_time' => 1530541509006.0,
                        'img_cover' => '1529056468333',
                        'period_code' => '201807020047',
                        'product_id' => 1050,
                        'sell_price' => '197.00',
                        'bid_price' => '18.50',
                        'user_id' => 1194971827,
                        'nickname' => 'Andying',
                    ),
                1 =>
                    array(
                        'id' => 4487157,
                        'title' => '金沙河 面粉5kg*2袋',
                        'bid_step' => 1,
                        'end_time' => 1530541505973.0,
                        'img_cover' => '1499421562472',
                        'period_code' => '201807020136',
                        'product_id' => 528,
                        'sell_price' => '70.00',
                        'bid_price' => '8.80',
                        'user_id' => 1361837785,
                        'nickname' => '命里有时终有之',
                    ),
                2 =>
                    array(
                        'id' => 4486992,
                        'title' => '可莱丝 水润保湿面膜20片',
                        'bid_step' => 1,
                        'end_time' => 1530541476438.0,
                        'img_cover' => '1501837156838',
                        'period_code' => '201807020041',
                        'product_id' => 569,
                        'sell_price' => '282.00',
                        'bid_price' => '25.00',
                        'user_id' => 1988396280,
                        'nickname' => '我无为',
                    ),

            );
        self::showMsg($data);
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
        $str = '{"code":"0000","message":"成功","data":[{"a":4477599,"c":7660,"d":"1353.20","h":"卡西欧","g":1252796071,"b":1,"i":10,"e":0,"f":0},{"a":4478576,"c":7203,"d":"1211.60","h":"mc乐少","g":1960617520,"b":1,"i":10,"e":0,"f":0},{"a":4486960,"c":2649,"d":"13.90","h":"聪明阿呆","g":1940412557,"b":1,"i":10,"e":0,"f":0},{"a":4486818,"c":6533,"d":"31.20","h":"不会唱情歌","g":1643758403,"b":1,"i":10,"e":0,"f":0},{"a":4483224,"c":3809,"d":"341.10","h":"辉A哥","g":1041285855,"b":1,"i":10,"e":0,"f":0},{"a":4478786,"c":4058,"d":"840.80","h":"踏取","g":1235053492,"b":1,"i":10,"e":0,"f":0},{"a":4483621,"c":3308,"d":"389.70","h":"鹏鹏","g":1319687339,"b":1,"i":10,"e":0,"f":0},{"a":4486002,"c":4447,"d":"122.90","h":"浩辰潮鞋","g":1283653549,"b":1,"i":10,"e":0,"f":0},{"a":4486054,"c":7136,"d":"89.90","h":"靠山","g":1118111351,"b":1,"i":10,"e":0,"f":0},{"a":4487097,"c":7588,"d":"1.30","h":"丹妞高傲与生俱来","g":1656370338,"b":1,"i":10,"e":0,"f":0},{"a":4486980,"c":7257,"d":"12.30","h":"悲伤寒冷凄凉一片","g":1708964192,"b":1,"i":10,"e":0,"f":0},{"a":4485905,"c":9424,"d":"120.30","h":"兰HUI","g":1355747144,"b":1,"i":10,"e":0,"f":0},{"a":4486902,"c":8282,"d":"19.80","h":"kimboda","g":1109714831,"b":1,"i":10,"e":0,"f":0},{"a":4486847,"c":2113,"d":"24.50","h":"洗墨丶","g":1867892915,"b":1,"i":10,"e":0,"f":0},{"a":4486111,"c":8624,"d":"39.10","h":"KENXI","g":1655023111,"b":1,"i":10,"e":0,"f":0},{"a":4486472,"c":7088,"d":"67.30","h":"REVOLT","g":1462332465,"b":1,"i":10,"e":0,"f":0},{"a":4487080,"c":8925,"d":"3.30","h":"淋铃","g":1446060002,"b":1,"i":10,"e":0,"f":0},{"a":4483431,"c":6466,"d":"406.00","h":"微更好","g":1877527206,"b":1,"i":10,"e":0,"f":0},{"a":4487055,"c":4421,"d":"5.60","h":"huang蕾蕾","g":1987065500,"b":1,"i":10,"e":0,"f":0},{"a":4487024,"c":2879,"d":"9.50","h":"邂逅n","g":1998569116,"b":1,"i":10,"e":0,"f":0}]}';

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
