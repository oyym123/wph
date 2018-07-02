<?php
/**
 * Created by PhpStorm.
 * User: Alienware
 * Date: 2018/7/2
 * Time: 23:19
 */

namespace App\Api\Controllers;


use App\Api\components\WebController;

class ProductController extends WebController
{
    /**
     *
     * @SWG\Get(path="/api/product",
     *   tags={"产品"},
     *   summary="产品列表",
     *   description="Author: OYYM",
     *   @SWG\Parameter(name="type", in="query", default="", description="类型",
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
        $data = array(
            0 =>
                array(
                    'a' => 4484958,
                    'c' => 7475,
                    'd' => '315.90',
                    'h' => '纯属废话',
                    'g' => 1672113436,
                    'b' => 1,
                    'i' => 10,
                    'e' => 0,
                    'f' => 0,
                ),
            1 =>
                array(
                    'a' => 4486256,
                    'c' => 1772,
                    'd' => '171.50',
                    'h' => '01240529',
                    'g' => 1417997220,
                    'b' => 1,
                    'i' => 10,
                    'e' => 0,
                    'f' => 0,
                ),
            2 =>
                array(
                    'a' => 4486696,
                    'c' => 8310,
                    'd' => '111.30',
                    'h' => 'justnie',
                    'g' => 1165890576,
                    'b' => 1,
                    'i' => 10,
                    'e' => 0,
                    'f' => 0,
                ),
            3 =>
                array(
                    'a' => 4487077,
                    'c' => 5638,
                    'd' => '75.70',
                    'h' => '陈图图',
                    'g' => 1825686966,
                    'b' => 1,
                    'i' => 10,
                    'e' => 0,
                    'f' => 0,
                ),
            4 =>
                array(
                    'a' => 4487147,
                    'c' => 0,
                    'd' => '65.10',
                    'h' => '海子',
                    'g' => 1281821791,
                    'b' => 2,
                    'i' => 10,
                    'e' => 0,
                    'f' => 0,
                ),
            5 =>
                array(
                    'a' => 4487256,
                    'c' => 9649,
                    'd' => '58.60',
                    'h' => '秘密',
                    'g' => 1614974222,
                    'b' => 1,
                    'i' => 10,
                    'e' => 0,
                    'f' => 0,
                ),
            6 =>
                array(
                    'a' => 4487288,
                    'c' => 0,
                    'd' => '49.00',
                    'h' => '梁学超',
                    'g' => 1361515487,
                    'b' => 2,
                    'i' => 10,
                    'e' => 0,
                    'f' => 0,
                ),
            7 =>
                array(
                    'a' => 4487364,
                    'c' => 0,
                    'd' => '34.40',
                    'h' => 'shrmao',
                    'g' => 1603839126,
                    'b' => 2,
                    'i' => 10,
                    'e' => 0,
                    'f' => 0,
                ),
            8 =>
                array(
                    'a' => 4487432,
                    'c' => 5880,
                    'd' => '35.40',
                    'h' => 'chhh梦里花开',
                    'g' => 1607666430,
                    'b' => 1,
                    'i' => 10,
                    'e' => 0,
                    'f' => 0,
                ),
            9 =>
                array(
                    'a' => 4487524,
                    'c' => 0,
                    'd' => '20.50',
                    'h' => '鸭鸡给给',
                    'g' => 1595293367,
                    'b' => 2,
                    'i' => 10,
                    'e' => 0,
                    'f' => 0,
                ),
            10 =>
                array(
                    'a' => 4487529,
                    'c' => 0,
                    'd' => '18.80',
                    'h' => '唐凡东',
                    'g' => 1072244419,
                    'b' => 2,
                    'i' => 10,
                    'e' => 0,
                    'f' => 0,
                ),
            11 =>
                array(
                    'a' => 4487544,
                    'c' => 2027,
                    'd' => '25.10',
                    'h' => '尼姑庵里的小和尚',
                    'g' => 1471240821,
                    'b' => 1,
                    'i' => 10,
                    'e' => 0,
                    'f' => 0,
                ),
            12 =>
                array(
                    'a' => 4487549,
                    'c' => 0,
                    'd' => '21.70',
                    'h' => '箫山',
                    'g' => 1220974021,
                    'b' => 2,
                    'i' => 10,
                    'e' => 0,
                    'f' => 0,
                ),

        );
        self::showMsg($data);
    }

    /**
     *
     * @SWG\Get(path="/api/product/type",
     *   tags={"产品"},
     *   summary="产品分类",
     *   description="Author: OYYM",
     *   @SWG\Parameter(name="name", in="query", default="", description="",
     *     type="string",
     *   ),
     *   @SWG\Response(
     *       response=200,description="successful operation"
     *   )
     * )
     */
    public function type()
    {

        $data = array(
            0 =>
                array(
                    'id' => 27,
                    'title' => '十元专区',
                    'img' => '1509257802730',
                ),
            1 =>
                array(
                    'id' => 4,
                    'title' => '手机专区',
                    'img' => '1509181778432',
                ),
            2 =>
                array(
                    'id' => 9,
                    'title' => '珠宝配饰',
                    'img' => '1509181806129',
                ),
            3 =>
                array(
                    'id' => 28,
                    'title' => '电脑平板',
                    'img' => '1509181783299',
                ),
            4 =>
                array(
                    'id' => 5,
                    'title' => '生活家电',
                    'img' => '1509181799941',
                ),
            5 =>
                array(
                    'id' => 3,
                    'title' => '数码影音',
                    'img' => '1509181788626',
                ),
            6 =>
                array(
                    'id' => 11,
                    'title' => '其他专区',
                    'img' => '1509181816726',
                ),
            7 =>
                array(
                    'id' => 10,
                    'title' => '美食天地',
                    'img' => '1509181823805',
                ),
            8 =>
                array(
                    'id' => 30,
                    'title' => '运动户外',
                    'img' => '1509181810711',
                ),
            9 =>
                array(
                    'id' => 32,
                    'title' => '美妆个护',
                    'img' => '1509181828587',
                ),
            10 =>
                array(
                    'id' => 33,
                    'title' => '家居生活',
                    'img' => '1509181834778',
                ),
        );
        self::showMsg($data);

    }
}