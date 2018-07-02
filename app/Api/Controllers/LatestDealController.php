<?php
/**
 * Created by PhpStorm.
 * User: Alienware
 * Date: 2018/7/2
 * Time: 23:03
 */

namespace App\Api\Controllers;


use App\Api\components\WebController;

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
            1 =>
                array(
                    'id' => 4487361,
                    'nickname' => '小了了',
                    'avatar' => 'https://qnimg.gogobids.com/avatar/b1b0aeb05bae50bb015bae5551804b1c',
                    'img_cover' => '1489658629414',
                    'bid_price' => '19.10',
                    'title' => '小米 Ninebot九号平衡车 颜色随机',
                    'short_title' => '小米平衡车',
                ),
            2 =>
                array(
                    'id' => 4487430,
                    'nickname' => '弑途',
                    'avatar' => 'https://qnimg.gogobids.com/avatar/b1b0aeb05bae50bb015bae519f8d0c94',
                    'img_cover' => '1509071245367',
                    'bid_price' => '4.50',
                    'title' => '品胜 三合一多功能手机充电线 1米 白色',
                    'short_title' => '品胜充电线',
                ),
            3 =>
                array(
                    'id' => 4487276,
                    'nickname' => '小汉纸',
                    'avatar' => 'https://qnimg.gogobids.com/avatar/b1b0aeb05d77c897015d77d07d536cd8',
                    'img_cover' => '1526287668849',
                    'bid_price' => '14.70',
                    'title' => '华为三键线控带麦半入耳式耳机AM115(标准版)',
                    'short_title' => '耳机',
                ),
            4 =>
                array(
                    'id' => 4487280,
                    'nickname' => '娜娜nn',
                    'avatar' => 'https://qnimg.gogobids.com/avatar/b1b0aeb05b55c4e3015b55c9e7b04c69',
                    'img_cover' => '1491990116254',
                    'bid_price' => '18.70',
                    'title' => '探路者 户外男女通款双肩背包 ZEBF80609 30L 颜色随机',
                    'short_title' => '探路者双肩包',
                ),
            5 =>
                array(
                    'id' => 4487201,
                    'nickname' => 'GUOCHUNSHENG',
                    'avatar' => 'https://qnimg.gogobids.com/avatar/b1b0aeb05bdd01d0015bdd157e820a12',
                    'img_cover' => '1530263508739',
                    'bid_price' => '27.50',
                    'title' => '汉美驰 （绿色单碗）家用软冰激凌机68554-CN',
                    'short_title' => '激凌机',
                ),
            6 =>
                array(
                    'id' => 4487203,
                    'nickname' => 'Angela夜与兮',
                    'avatar' => 'https://qnimg.gogobids.com/avatar/b1b0aeb05d77c897015d77d711214220',
                    'img_cover' => '1509071521493',
                    'bid_price' => '27.40',
                    'title' => '唱吧 C1麦克风 手机电脑通用 颜色随机',
                    'short_title' => '唱吧 麦克风',
                ),
            7 =>
                array(
                    'id' => 4487298,
                    'nickname' => '湘湘忒',
                    'avatar' => 'https://qnimg.gogobids.com/avatar/b1b0aeb05d77c897015d77d519c0286e',
                    'img_cover' => '1526288057021',
                    'bid_price' => '16.90',
                    'title' => '迪士尼 米奇公仔大号',
                    'short_title' => '米奇公仔',
                ),
            8 =>
                array(
                    'id' => 4487036,
                    'nickname' => '赐予我米吧',
                    'avatar' => 'https://qnimg.gogobids.com/avatar/b1b0aeb05b5ab106015b5ab74c506143',
                    'img_cover' => '1525425516044',
                    'bid_price' => '45.20',
                    'title' => '长城 九五特级赤霞珠干红葡萄酒 整箱装 750ml*6瓶',
                    'short_title' => '长城葡萄酒',
                ),
            9 =>
                array(
                    'id' => 4487232,
                    'nickname' => '不小了',
                    'avatar' => 'https://qnimg.gogobids.com/avatar/b1b0aeb05bae50bb015bae548f283dc5',
                    'img_cover' => '1529056468333',
                    'bid_price' => '23.40',
                    'title' => '大洋世家 阿根廷红虾L1 2kg盒 30-40只 海鲜大虾',
                    'short_title' => '海鲜大虾',
                ),
            10 =>
                array(
                    'id' => 4487445,
                    'nickname' => '沉默的我',
                    'avatar' => 'https://qnimg.gogobids.com/avatar/b1b0aeb05bdd01d0015bdd0d076e0f21',
                    'img_cover' => '1491998401710',
                    'bid_price' => '2.00',
                    'title' => 'Q 币20个（支持实时发货）',
                    'short_title' => 'Q 币20个',
                ),
            11 =>
                array(
                    'id' => 4487307,
                    'nickname' => 'wangruixiang',
                    'avatar' => 'https://qnimg.gogobids.com/avatar/b1b0aeb05be5e077015be5e0c57f04f9',
                    'img_cover' => '1529056260179',
                    'bid_price' => '15.30',
                    'title' => '佳佰 不锈钢奶锅(电磁炉明火通用) JBYS-16D',
                    'short_title' => '不锈钢奶锅',
                ),
        );
        self::showMsg($data);
    }
}