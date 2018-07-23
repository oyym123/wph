<?php
/**
 * Created by PhpStorm.
 * User: Alienware
 * Date: 2018/7/2
 * Time: 23:19
 */

namespace App\Api\Controllers;


use App\Api\components\WebController;
use App\Models\Period;
use App\Models\Product;
use App\Models\ProductType;
use Composer\DependencyResolver\Request;
use Illuminate\Support\Facades\Input;


class ProductController extends WebController
{

    /**
     * @SWG\Get(path="/api/product",
     *   tags={"产品"},
     *   summary="产品列表",
     *   description="Author: OYYM",
     *   @SWG\Parameter(name="type", in="query", default="", description="类型",
     *     type="string",
     *   ),
     *   @SWG\Parameter(name="token", in="header", default="", description="用户token" ,required=true,
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
        $period = new Period();
        $period->userId = $this->userId;
        $period->limit = $this->limit;
        $period->offset = $this->offset;
        self::showMsg($period->getProductList());
    }

    /**
     * @SWG\Get(path="/api/product/detail",
     *   tags={"产品"},
     *   summary="商品详情",
     *   description="Author: OYYM",
     *   @SWG\Parameter(name="period_id", in="query", default="", description="",required=true,
     *     type="string",
     *   ),
     *   @SWG\Parameter(name="token", in="header", default="", description="用户token",
     *     type="string",
     *   ),
     *   @SWG\Response(
     *       response=200,description="
     *             [expended] => Array 用户消耗的
     *                       (
     *                          [used_real_bids] => 0
     *                          [used_voucher_bids] => 0
     *                          [used_money] => 0.00
     *                          [is_buy_differential_able] => 0
     *                          [buy_differential_money] => 0.00
     *                          [order_id] =>
     *                          [order_type] =>
     *                          [need_to_bided_pay] => 0
     *                          [need_to_bided_pay_price] => 0.00
     *                          [return_bids] => 0
     *                          [return_shop_bids] => 0
     *                          [pay_status] => 0
     *                          [pay_time] => 0
     *                      )
     *              [detail] => Array
     *                      (
     *                          [id] => 期数id
     *                          [period_status] => 2
     *                          [product_id] => 1042
     *                          [period_code] => 期数代码
     *                          [title] => 预售 三星 Galaxy A9 Star 4GB+64GB 颜色随机
     *                          [product_type] => 0
     *                          [img_cover] => 1529055804660
     *                          [imgs] => 1529055809318,1529055809433,1529055809639,1529055809710,1529055809774
     *                          [sell_price] =>3299.00
     *                          [bid_step] => 1
     *                          [price_add_length] => 0.1
     *                          [init_price] => 0.00
     *                          [countdown_length] => 10
     *                          [is_voucher_bids_enable] => 1
     *                          [buy_by_diff] => 1
     *                          [settlement_bid_id] => b1b7b2ae63f70ab2016465b1307b6061
     *                          [auctioneer_id] => 5
     *                          [is_favorite] => 0
     *                          [product_status] => 3
     *                          [default_offer] => 5
     *                          [offer_ladder] => 10,20,50,66
     *                          [have_show] => 0
     *                          [auction_avatar] => 1517297843391
     *                          [auction_id] => 1
     *                          [auction_name] => 诺诺拍卖行
     *                          [auctioneer_avatar] => 1520580890766
     *                          [auctioneer_license] => 2300410
     *                          [auctioneer_name] => 陈英嫦
     *                      )
     *              [proxy] => Array
     *                      (
     *                      )
     *
     *              [price] => Array
     *                      (
     *                          [c] => 0
     *                          [d] => 377.50
     *                          [h] =>
     *                          [g] =>
     *                          [b] =>
     *                          [e] =>
     *                          [f] =>
     *                          [a] =>
     *                      )
     *              [bid_records] => Array
     *                      (
     *                      [0] => Array
     *                      (
     *                          [area] => 江西南昌
     *                          [bid_nickname] => 不可忽视的激情
     *                          [bid_no] => 3775
     *                          [bid_price] => 377.50
     *                      )
     *              )
     *     "
     *   )
     * )
     */
    public function detail()
    {
        $period = new  Period();
        self::showMsg($period->getProductDetail($this->request->period_id));
    }


    /**
     * @SWG\Get(path="/api/product/past-deals",
     *   tags={"产品"},
     *   summary="往期成交",
     *   description="Author: OYYM",
     *   @SWG\Parameter(name="name", in="query", default="", description="",
     *     type="string",
     *   ),
     *   @SWG\Response(
     *       response=200,description="successful operation"
     *   )
     * )
     */
    public function pastDeals()
    {
        $data = array(
            0 =>
                array(
                    'id' => 4505048,
                    'product_id' => 1042,
                    'period_id' => 4505048,
                    'period_code' => '201807040003',
                    'title' => '预售 三星 Galaxy A9 Star 4GB+64GB 颜色随机',
                    'img_cover' => '1529055804660',
                    'end_time' => 1530714478410.0,
                    'sell_price' => '3299.00',
                    'bid_price' => '377.50',
                    'user_id' => 1485069167,
                    'nickname' => '不可忽视的激情',
                    'avatar' => 'https://qnimg.gogobids.com/avatar/b1b0aeb05bae50bb015bae5bd35c3bf6',
                ),
            1 =>
                array(
                    'id' => 4502791,
                    'product_id' => 1042,
                    'period_id' => 4502791,
                    'period_code' => '201807040002',
                    'title' => '预售 三星 Galaxy A9 Star 4GB+64GB 颜色随机',
                    'img_cover' => '1529055804660',
                    'end_time' => 1530692501807.0,
                    'sell_price' => '3299.00',
                    'bid_price' => '250.70',
                    'user_id' => 1561020566,
                    'nickname' => 'Neednah',
                    'avatar' => 'https://qnimg.gogobids.com/avatar/b1b0aeb05be5e077015be5e4d1974a12',
                ),
            2 =>
                array(
                    'id' => 4500065,
                    'product_id' => 1042,
                    'period_id' => 4500065,
                    'period_code' => '201807040001',
                    'title' => '预售 三星 Galaxy A9 Star 4GB+64GB 颜色随机',
                    'img_cover' => '1529055804660',
                    'end_time' => 1530677752097.0,
                    'sell_price' => '3299.00',
                    'bid_price' => '317.00',
                    'user_id' => 1977635926,
                    'nickname' => '杨大善人',
                    'avatar' => 'https://qnimg.gogobids.com/avatar/b1b0aeb05be5e077015be5e90d69125a',
                ),
            3 =>
                array(
                    'id' => 4497693,
                    'product_id' => 1042,
                    'period_id' => 4497693,
                    'period_code' => '201807030004',
                    'title' => '预售 三星 Galaxy A9 Star 4GB+64GB 颜色随机',
                    'img_cover' => '1529055804660',
                    'end_time' => 1530647458375.0,
                    'sell_price' => '3299.00',
                    'bid_price' => '346.20',
                    'user_id' => 1433519537,
                    'nickname' => '搭进去不少钱了',
                    'avatar' => 'https://qnimg.gogobids.com/avatar/b1b0aeb05bae50bb015bae61a99521f5',
                ),
            4 =>
                array(
                    'id' => 4494932,
                    'product_id' => 1042,
                    'period_id' => 4494932,
                    'period_code' => '201807030003',
                    'title' => '预售 三星 Galaxy A9 Star 4GB+64GB 颜色随机',
                    'img_cover' => '1529055804660',
                    'end_time' => 1530627113233.0,
                    'sell_price' => '3299.00',
                    'bid_price' => '304.30',
                    'user_id' => 1476284623,
                    'nickname' => '我是幸运星',
                    'avatar' => 'https://qnimg.gogobids.com/avatar/b1b0aeb05bdd01d0015bdd14e7d77f7d',
                ),
            5 =>
                array(
                    'id' => 4492342,
                    'product_id' => 1042,
                    'period_id' => 4492342,
                    'period_code' => '201807030002',
                    'title' => '预售 三星 Galaxy A9 Star 4GB+64GB 颜色随机',
                    'img_cover' => '1529055804660',
                    'end_time' => 1530609074216.0,
                    'sell_price' => '3299.00',
                    'bid_price' => '290.70',
                    'user_id' => 1773726143,
                    'nickname' => '独狼疑难杂症调理',
                    'avatar' => 'https://qnimg.gogobids.com/avatar/1139f185-ec81-4927-8ff0-2325996f7c14.jpeg',
                ),
            6 =>
                array(
                    'id' => 4489485,
                    'product_id' => 1042,
                    'period_id' => 4489485,
                    'period_code' => '201807030001',
                    'title' => '预售 三星 Galaxy A9 Star 4GB+64GB 颜色随机',
                    'img_cover' => '1529055804660',
                    'end_time' => 1530591986060.0,
                    'sell_price' => '3299.00',
                    'bid_price' => '325.70',
                    'user_id' => 1050134534,
                    'nickname' => 'GoodLuck',
                    'avatar' => 'https://qnimg.gogobids.com/avatar/b1b0aeb05bdd01d0015bdd09991f62b1',
                ),
            7 =>
                array(
                    'id' => 4487567,
                    'product_id' => 1042,
                    'period_id' => 4487567,
                    'period_code' => '201807020004',
                    'title' => '预售 三星 Galaxy A9 Star 4GB+64GB 颜色随机',
                    'img_cover' => '1529055804660',
                    'end_time' => 1530564776792.0,
                    'sell_price' => '3299.00',
                    'bid_price' => '365.00',
                    'user_id' => 1445922399,
                    'nickname' => 'swmsfm',
                    'avatar' => 'https://qnimg.gogobids.com/avatar/b1b0aeb05bae50bb015bae5fc3e90079',
                ),
            8 =>
                array(
                    'id' => 4484776,
                    'product_id' => 1042,
                    'period_id' => 4484776,
                    'period_code' => '201807020003',
                    'title' => '预售 三星 Galaxy A9 Star 4GB+64GB 颜色随机',
                    'img_cover' => '1529055804660',
                    'end_time' => 1530543694972.0,
                    'sell_price' => '3299.00',
                    'bid_price' => '315.40',
                    'user_id' => 1207584489,
                    'nickname' => '有志小青年',
                    'avatar' => 'https://qnimg.gogobids.com/avatar/b1b0aeb05b5ab106015b5ab37ece2386',
                ),
            9 =>
                array(
                    'id' => 4481933,
                    'product_id' => 1042,
                    'period_id' => 4481933,
                    'period_code' => '201807020002',
                    'title' => '预售 三星 Galaxy A9 Star 4GB+64GB 颜色随机',
                    'img_cover' => '1529055804660',
                    'end_time' => 1530525428050.0,
                    'sell_price' => '3299.00',
                    'bid_price' => '320.10',
                    'user_id' => 1688934979,
                    'nickname' => 'hy',
                    'avatar' => 'https://qnimg.gogobids.com/avatar/b1b0aeb05bdd01d0015bdd10fe0840e5',
                ),
            10 =>
                array(
                    'id' => 4478987,
                    'product_id' => 1042,
                    'period_id' => 4478987,
                    'period_code' => '201807020001',
                    'title' => '预售 三星 Galaxy A9 Star 4GB+64GB 颜色随机',
                    'img_cover' => '1529055804660',
                    'end_time' => 1530506751056.0,
                    'sell_price' => '3299.00',
                    'bid_price' => '345.10',
                    'user_id' => 1196400916,
                    'nickname' => '古古古',
                    'avatar' => 'https://qnimg.gogobids.com/avatar/b1b0aeb05b55c4e3015b55cb16985de5',

                )
        );
        self::showMsg($data);
    }

    /**
     * @SWG\Get(path="/api/product/bid-record",
     *   tags={"产品"},
     *   summary="出价记录",
     *   description="Author: OYYM",
     *   @SWG\Parameter(name="name", in="query", default="", description="",
     *     type="string",
     *   ),
     *   @SWG\Response(
     *       response=200,description="successful operation"
     *   )
     * )
     */
    public function bidRecord()
    {
        $data = array(
            0 =>
                array(
                    'bid_price' => '377.50',
                    'bid_time' => 1530714468410.0,
                    'bid_no' => 3775,
                    'user_id' => 1485069167,
                    'nickname' => '不可忽视的激情',
                    'avatar' => 'https://qnimg.gogobids.com/avatar/b1b0aeb05bae50bb015bae5bd35c3bf6',
                    'area' => '江西南昌',
                    'bid_type' => 1,
                ),
            1 =>
                array(
                    'bid_price' => '377.40',
                    'bid_time' => 1530714463510.0,
                    'bid_no' => 3774,
                    'user_id' => 1727940296,
                    'nickname' => 'appouu',
                    'avatar' => 'https://qnimg.gogobids.com/avatar/b1b0aeb05b5c15fa015b5c213a663834',
                    'area' => '陕西西安',
                    'bid_type' => 1,
                ),
            2 =>
                array(
                    'bid_price' => '377.30',
                    'bid_time' => 1530714463501.0,
                    'bid_no' => 3773,
                    'user_id' => 1485069167,
                    'nickname' => '不可忽视的激情',
                    'avatar' => 'https://qnimg.gogobids.com/avatar/b1b0aeb05bae50bb015bae5bd35c3bf6',
                    'area' => '江西南昌',
                    'bid_type' => 1,
                ),
            3 =>
                array(
                    'bid_price' => '377.20',
                    'bid_time' => 1530714463433.0,
                    'bid_no' => 3772,
                    'user_id' => 1079177023,
                    'nickname' => '邪的恶',
                    'avatar' => 'https://qnimg.gogobids.com/avatar/b1b0aeb05be5e077015be5eeb5587427',
                    'area' => '山东青岛',
                    'bid_type' => 1,
                ),
            4 =>
                array(
                    'bid_price' => '377.10',
                    'bid_time' => 1530714454400.0,
                    'bid_no' => 3771,
                    'user_id' => 1485069167,
                    'nickname' => '不可忽视的激情',
                    'avatar' => 'https://qnimg.gogobids.com/avatar/b1b0aeb05bae50bb015bae5bd35c3bf6',
                    'area' => '江西南昌',
                    'bid_type' => 1,
                ),
            5 =>
                array(
                    'bid_price' => '377.00',
                    'bid_time' => 1530714445400.0,
                    'bid_no' => 3770,
                    'user_id' => 1941017421,
                    'nickname' => '朱小妞',
                    'avatar' => 'https://qnimg.gogobids.com/avatar/b1b0aeb05b5c15fa015b5c21d819425d',
                    'area' => '河南郑州',
                    'bid_type' => 1,
                ),
            6 =>
                array(
                    'bid_price' => '376.90',
                    'bid_time' => 1530714438500.0,
                    'bid_no' => 3769,
                    'user_id' => 1077937483,
                    'nickname' => '成爹哋',
                    'avatar' => 'https://qnimg.gogobids.com/avatar/b1b0aeb05b5ab106015b5ab4c16536db',
                    'area' => '河南驻马',
                    'bid_type' => 1,
                ),
            7 =>
                array(
                    'bid_price' => '376.80',
                    'bid_time' => 1530714432700.0,
                    'bid_no' => 3768,
                    'user_id' => 1485069167,
                    'nickname' => '不可忽视的激情',
                    'avatar' => 'https://qnimg.gogobids.com/avatar/b1b0aeb05bae50bb015bae5bd35c3bf6',
                    'area' => '江西南昌',
                    'bid_type' => 1,
                ),
            8 =>
                array(
                    'bid_price' => '376.70',
                    'bid_time' => 1530714432330.0,
                    'bid_no' => 3767,
                    'user_id' => 1152967132,
                    'nickname' => '很爱你的人',
                    'avatar' => 'https://qnimg.gogobids.com/avatar/b1b0aeb05bae50bb015bae59a434175c',
                    'area' => '安徽阜阳',
                    'bid_type' => 1,
                ),
            9 =>
                array(
                    'bid_price' => '376.60',
                    'bid_time' => 1530714432231.0,
                    'bid_no' => 3766,
                    'user_id' => 1485069167,
                    'nickname' => '不可忽视的激情',
                    'avatar' => 'https://qnimg.gogobids.com/avatar/b1b0aeb05bae50bb015bae5bd35c3bf6',
                    'area' => '江西南昌',
                    'bid_type' => 1,
                )
        );
        self::showMsg($data);
    }

    /**
     * @SWG\Get(path="/api/product/share-order",
     *   tags={"产品"},
     *   summary="晒单",
     *   description="Author: OYYM",
     *   @SWG\Parameter(name="name", in="query", default="", description="",
     *     type="string",
     *   ),
     *   @SWG\Response(
     *       response=200,description="successful operation"
     *   )
     * )
     */
    public function shareOrder()
    {
        $data = array(
            0 =>
                array(
                    'id' => 37993,
                    'status' => 2,
                    'user_id' => 1818755979,
                    'nickname' => '刘吵吵',
                    'avatar' => 'https://qnimg.gogobids.com/avatar/b1b0aeb05b55c4e3015b55c6828f18b2',
                    'title' => '燕之屋 即食冰糖燕窝 滋补品礼盒 70g*3瓶',
                    'content' => '吃了几次了，感觉味道甜了些，味道清淡点更好',
                    'imgs' => 'FkNWMA2OD5ZpOyg_6qF4BIyOvMwl,FvCsBA82t6hZ69kK5AUoH_7pYLNe,FrbKni301qaoSLWNfi7v-TNGL3D_',
                    'remark' => 'NULL',
                    'product_title' => '燕之屋 即食冰糖燕窝 滋补品礼盒 70g*3瓶',
                    'product_type' => 0,
                    'img_cover' => '1509075921774',
                    'product_id' => 724,
                    'is_long_history' => 1,
                    'review_time' => 1528869623245.0,
                    'show_time' => 1528826479000.0,
                    'update_time' => 1528869623245.0,
                    'create_time' => 1528869623245.0,
                ),
            1 =>
                array(
                    'id' => 37987,
                    'status' => 2,
                    'user_id' => 1901982292,
                    'nickname' => '厃殽',
                    'avatar' => 'https://qnimg.gogobids.com/avatar/b1b0aeb05d77c897015d77d141d37799',
                    'title' => '燕之屋 即食冰糖燕窝 滋补品礼盒 70g*3瓶',
                    'content' => '感觉还行吧，挺好喝的。喝完再拍啊',
                    'imgs' => 'FpMJ5KAFcfPnZ8My2bWlrYUMjHtl,FtOJIBxCg3sdtnY8iDNX9mHU_974,Fl7tJh-EkrsxxB-V8i5u0udcgeNT',
                    'remark' => 'NULL',
                    'product_title' => '燕之屋 即食冰糖燕窝 滋补品礼盒 70g*3瓶',
                    'product_type' => 0,
                    'img_cover' => '1509075921774',
                    'product_id' => 724,
                    'is_long_history' => 1,
                    'review_time' => 1528869609144.0,
                    'show_time' => 1528769633000.0,
                    'update_time' => 1528869609144.0,
                    'create_time' => 1528869609144.0,
                ),
            2 =>
                array(
                    'id' => 37995,
                    'status' => 2,
                    'user_id' => 1979803935,
                    'nickname' => 'anson',
                    'avatar' => 'https://qnimg.gogobids.com/avatar/b1b0aeb05b5ab106015b5ab35ac9214d',
                    'title' => '燕之屋 即食冰糖燕窝 滋补品礼盒 70g*3瓶',
                    'content' => '好喝，真的好喝。胶原蛋白补起来啊',
                    'imgs' => 'FkX0-ODwMG9LKtQkhMNkdvERAYhV,FlzoIWV5xSSFRkwXnT4JTLpMpiZa,FqPKGXUVjNuwesYMli4bHZD2oIhN,FoyTvqErAdK8sIrwo0k1D613HOqe,FtZB7jBM1M1y8lQ3lzSKiyCiD3NJ',
                    'remark' => 'NULL',
                    'product_title' => '燕之屋 即食冰糖燕窝 滋补品礼盒 70g*3瓶',
                    'product_type' => 0,
                    'img_cover' => '1509075921774',
                    'product_id' => 724,
                    'is_long_history' => 1,
                    'review_time' => 1528869630547.0,
                    'show_time' => 1528714317000.0,
                    'update_time' => 1528869630547.0,
                    'create_time' => 1528869630547.0,
                ),
            3 =>
                array(
                    'id' => 37994,
                    'status' => 2,
                    'user_id' => 1731860911,
                    'nickname' => 'aking',
                    'avatar' => 'https://qnimg.gogobids.com/avatar/b1b0aeb05b55c4e3015b55cddb4105a6',
                    'title' => '燕之屋 即食冰糖燕窝 滋补品礼盒 70g*3瓶',
                    'content' => '第一次拍的，包装精美，口感滑滑的，口味偏甜，好吃',
                    'imgs' => 'FgyQpwO1fZKqO2lbbByT2fjxrCII,Fmnb0o-4-nRppBhFxvbThb5syJa_,FiTmBCNrsjhmNBkDDzZ_gBXXhQmu',
                    'remark' => 'NULL',
                    'product_title' => '燕之屋 即食冰糖燕窝 滋补品礼盒 70g*3瓶',
                    'product_type' => 0,
                    'img_cover' => '1509075921774',
                    'product_id' => 724,
                    'is_long_history' => 1,
                    'review_time' => 1528869626065.0,
                    'show_time' => 1528581843000.0,
                    'update_time' => 1528869626065.0,
                    'create_time' => 1528869626065.0,
                ),
            4 =>
                array(
                    'id' => 37990,
                    'status' => 2,
                    'user_id' => 1991151083,
                    'nickname' => '萌男哟',
                    'avatar' => 'https://qnimg.gogobids.com/avatar/b1b0aeb05bdd01d0015bdd0cae830a97',
                    'title' => '燕之屋 即食冰糖燕窝 滋补品礼盒 70g*3瓶',
                    'content' => '多次来拍了，效果及口感好得不用说了。',
                    'imgs' => 'FjXgzaUBlF-ftHQq2oiNOcUwSvT7,Fr5wjf0WXa-ZK2NBFvgfFfv24u9w,FhvqEgbVUAmiIiu-NBTWw6aIMCK7',
                    'remark' => 'NULL',
                    'product_title' => '燕之屋 即食冰糖燕窝 滋补品礼盒 70g*3瓶',
                    'product_type' => 0,
                    'img_cover' => '1509075921774',
                    'product_id' => 724,
                    'is_long_history' => 1,
                    'review_time' => 1528869615914.0,
                    'show_time' => 1528549903000.0,
                    'update_time' => 1528869615914.0,
                    'create_time' => 1528869615914.0,
                ),
            5 =>
                array(
                    'id' => 37996,
                    'status' => 2,
                    'user_id' => 1462825830,
                    'nickname' => 'wasel',
                    'avatar' => 'https://qnimg.gogobids.com/avatar/b1b0aeb05bae50bb015bae5291911d41',
                    'title' => '燕之屋 即食冰糖燕窝 滋补品礼盒 70g*3瓶',
                    'content' => '非常超值，大爱，还会再拍的',
                    'imgs' => 'Fqmrw5ZPNbgGGLVxiI2M48ovaveI,FuPl_sXCdvs0xcWX7LWkhGTiafyC,FpXN_psN0o383zZWDriGOgxwDfXj',
                    'remark' => 'NULL',
                    'product_title' => '燕之屋 即食冰糖燕窝 滋补品礼盒 70g*3瓶',
                    'product_type' => 0,
                    'img_cover' => '1509075921774',
                    'product_id' => 724,
                    'is_long_history' => 1,
                    'review_time' => 1528869633398.0,
                    'show_time' => 1528540463000.0,
                    'update_time' => 1528869633398.0,
                    'create_time' => 1528869633398.0,
                ),
            6 =>
                array(
                    'id' => 37989,
                    'status' => 2,
                    'user_id' => 1365543189,
                    'nickname' => 'yxping',
                    'avatar' => 'https://qnimg.gogobids.com/avatar/b1b0aeb05be5e077015be5ea0c512497',
                    'title' => '燕之屋 即食冰糖燕窝 滋补品礼盒 70g*3瓶',
                    'content' => '收到直接打开了，这个直接一罐解决了。。',
                    'imgs' => 'FsxDR8Ix5hBRyu8ld6edW4Cy2TsF,FkLCJd85jYowJ2vWE9hsj8HCsq85,FqwrcCUB8Dj6rNhIXCXtBByQ0tWv',
                    'remark' => 'NULL',
                    'product_title' => '燕之屋 即食冰糖燕窝 滋补品礼盒 70g*3瓶',
                    'product_type' => 0,
                    'img_cover' => '1509075921774',
                    'product_id' => 724,
                    'is_long_history' => 1,
                    'review_time' => 1528869612914.0,
                    'show_time' => 1528523935000.0,
                    'update_time' => 1528869612914.0,
                    'create_time' => 1528869612914.0,
                ),
            7 =>
                array(
                    'id' => 37988,
                    'status' => 2,
                    'user_id' => 1114228984,
                    'nickname' => '善心痣',
                    'avatar' => 'https://qnimg.gogobids.com/avatar/b1b0aeb05b55c4e3015b55c766df2615',
                    'title' => '燕之屋 即食冰糖燕窝 滋补品礼盒 70g*3瓶',
                    'content' => '包装很仔细，早上空腹喝了一瓶，味道不错，还会继续拍拍拍的，比较方便，免得自己炖。',
                    'imgs' => 'FpH61IcdbCCIHj0rBjHd2I8d1ij7,FldHR9oaghjH0BcUQUcunNhkFq7S,Fh0i2TvRPDUwNpLZ-Wl8Olu-toT0',
                    'remark' => 'NULL',
                    'product_title' => '燕之屋 即食冰糖燕窝 滋补品礼盒 70g*3瓶',
                    'product_type' => 0,
                    'img_cover' => '1509075921774',
                    'product_id' => 724,
                    'is_long_history' => 1,
                    'review_time' => 1528869611486.0,
                    'show_time' => 1528498658000.0,
                    'update_time' => 1528869611486.0,
                    'create_time' => 1528869611486.0,
                ),
            8 =>
                array(
                    'id' => 37992,
                    'status' => 2,
                    'user_id' => 1753547578,
                    'nickname' => '一顺',
                    'avatar' => 'https://qnimg.gogobids.com/avatar/b1b0aeb05d77c897015d77cb0d7c1e65',
                    'title' => '燕之屋 即食冰糖燕窝 滋补品礼盒 70g*3瓶',
                    'content' => '吃了一瓶很满意，口感很好。棒棒哒',
                    'imgs' => 'Fo_C1XKzr9nSOwgbQcc9bR3h-MW5,FnnuFlx2vW5pxZqanigXIZ2qOALm,FnKMgoDhPymw4CFzgt5O42liR1hI,Fis99gBrgp2jXdNrTL-Nc9jumHHV',
                    'remark' => 'NULL',
                    'product_title' => '燕之屋 即食冰糖燕窝 滋补品礼盒 70g*3瓶',
                    'product_type' => 0,
                    'img_cover' => '1509075921774',
                    'product_id' => 724,
                    'is_long_history' => 1,
                    'review_time' => 1528869622050.0,
                    'show_time' => 1528463840000.0,
                    'update_time' => 1528869622050.0,
                    'create_time' => 1528869622050.0,
                ),
            9 =>
                array(
                    'id' => 37991,
                    'status' => 2,
                    'user_id' => 1162523932,
                    'nickname' => '楠木青尘',
                    'avatar' => 'https://qnimg.gogobids.com/avatar/b1b0aeb05be5e077015be5e4f9784d17',
                    'title' => '燕之屋 即食冰糖燕窝 滋补品礼盒 70g*3瓶',
                    'content' => '包装品质都非常棒，希望口感不错',
                    'imgs' => 'FntXl33t3INSC7AZDq2sulRBAtar,FocbGkDzoQgWj07zDXOqwOA1LAUS,FurVWoLpXj0VxQCBPej1AFEfebjV',
                    'remark' => 'NULL',
                    'product_title' => '燕之屋 即食冰糖燕窝 滋补品礼盒 70g*3瓶',
                    'product_type' => 0,
                    'img_cover' => '1509075921774',
                    'product_id' => 724,
                    'is_long_history' => 1,
                    'review_time' => 1528869617122.0,
                    'show_time' => 1528315412000.0,
                    'update_time' => 1528869617122.0,
                    'create_time' => 1528869617122.0,
                ),
        );
        self::showMsg($data);

    }


    /**
     * @SWG\Get(path="/api/product/bid-rules",
     *   tags={"产品"},
     *   summary="竞价规则",
     *   description="Author: OYYM",
     *   @SWG\Parameter(name="name", in="query", default="", description="",
     *     type="string",
     *   ),
     *   @SWG\Response(
     *       response=200,description="successful operation"
     *   )
     * )
     */
    public function bidRules()
    {
        $data = array(
            'value' => '<p><span style="font-family: 微软雅黑, &quot;Microsoft YaHei&quot;; font-size: 14px; color: rgb(89, 89, 89);"></span></p><p style="margin-top:0;margin-bottom:0;padding:0 0 0 0 ;line-height:28px"><span style="font-family: 微软雅黑; font-size: 14px; color: rgb(89, 89, 89);"></span></p><p style="margin-top:0;margin-bottom:0;padding:0 0 0 0 ;line-height:28px"><span style="font-family: 微软雅黑; font-size: 14px; color: rgb(89, 89, 89);">(1) 所有商品竞拍初始价均为0元起，每出一次出价会消耗一定数量的拍币，同时商品价格以0.1元递增。</span></p><p style="margin-top:0;margin-bottom:0;padding:0 0 0 0 ;line-height:28px"><span style="font-family: 微软雅黑; font-size: 14px; color: rgb(89, 89, 89);"><br/></span></p><p style="margin-top:0;margin-bottom:0;padding:0 0 0 0 ;line-height:28px"><span style="color: rgb(89, 89, 89); font-family: 微软雅黑; font-size: 14px;">(2) 在初始倒计时内即可出价，初始倒计时后进入竞拍倒计时，当您出价后，该件商品的计时器将被自动重置，以便其他用户进行出价竞争。如果没有其他用户对该件商品出价，计时器归零时，您便成功拍得了该商品。</span></p><p style="margin-top:0;margin-bottom:0;padding:0 0 0 0 ;line-height:28px"><span style="font-family: 微软雅黑; font-size: 14px; color: rgb(89, 89, 89);"><br/></span></p><p style="margin-top:0;margin-bottom:0;padding:0 0 0 0 ;line-height:28px"><span style="font-family: 微软雅黑; font-size: 14px; color: rgb(89, 89, 89);">(3) 若拍卖成功，请在30天内以成交价购买竞拍商品，超过30天未下单，视为放弃，不返拍币。</span></p><p style="margin-top:0;margin-bottom:0;padding:0 0 0 0 ;line-height:28px"><span style="font-family: 微软雅黑; font-size: 14px; color: rgb(89, 89, 89);"><br/></span></p><p style="margin-top:0;margin-bottom:0;padding:0 0 0 0 ;line-height:28px"><span style="font-family: 微软雅黑; font-size: 14px; color: rgb(89, 89, 89);">(4) <span style="font-size:14px;font-family:&#39;微软雅黑&#39;,sans-serif">若拍卖失败，将返还所消耗拍币的30%作为购物币，可用于差价购买当期商品，赠币除外。</span></span></p><p style="margin-top:0;margin-bottom:0;padding:0 0 0 0 ;line-height:28px"><span style="font-family: 微软雅黑; font-size: 14px; color: rgb(89, 89, 89);"><br/></span></p><p style="margin-top:0;margin-bottom:0;padding:0 0 0 0 ;line-height:28px"><span style="font-family: 微软雅黑; font-size: 14px; color: rgb(89, 89, 89);">(5) 平台严禁违规操作，最终解释权归闪电拍卖所有。</span></p><p><span style="font-family: 微软雅黑, &quot;Microsoft YaHei&quot;; font-size: 14px; color: rgb(89, 89, 89);"><br/></span></p>',
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
        self::showMsg(ProductType::getList());
    }

    /**
     * @SWG\Post(path="/api/product/type-product-list",
     *   tags={"产品"},
     *   summary="分类产品列表",
     *   description="Author: OYYM",
     *   @SWG\Parameter(name="type_id", in="formData", default="1", description="类型id",
     *     type="string",
     *   ),
     *   @SWG\Response(
     *       response=200,description="successful operation"
     *   )
     * )
     */
    public function typeProductList(Request $request)
    {
        if (Input::get('type_id')) {

        }
        $data = array(
            0 =>
                array(
                    'id' => 4570371,
                    'product_id' => 419,
                    'period_code' => '201807100006',
                    'title' => '2017款 Apple iMac Pro 27英寸 3.2GHz八核处理器 32GB内存 1TB固态硬盘（发货周期一个月）',
                    'img_cover' => '1496734422804',
                    'sell_price' => '43437.00',
                    'bid_step' => 10,
                    'is_favorite' => 0,
                ),
            1 =>
                array(
                    'id' => 4570741,
                    'product_id' => 851,
                    'period_code' => '201807100009',
                    'title' => '欧米茄 手表 海马系列机械男表 212.30.41.20.01.003',
                    'img_cover' => '1509681943111',
                    'sell_price' => '28156.00',
                    'bid_step' => 10,
                    'is_favorite' => 0,
                ),
            2 =>
                array(
                    'id' => 4569990,
                    'product_id' => 423,
                    'period_code' => '201807100007',
                    'title' => '2017款 Apple MacBook Pro 13英寸 3.1GHz 256GB 颜色随机',
                    'img_cover' => '1496749031039',
                    'sell_price' => '15717.00',
                    'bid_step' => 10,
                    'is_favorite' => 0,
                ),
            3 =>
                array(
                    'id' => 4571297,
                    'product_id' => 635,
                    'period_code' => '201807100016',
                    'title' => '中国黄金 Au9999黄金金元宝50g',
                    'img_cover' => '1517389553224',
                    'sell_price' => '16088.00',
                    'bid_step' => 10,
                    'is_favorite' => 0,
                ),
            4 =>
                array(
                    'id' => 4571373,
                    'product_id' => 813,
                    'period_code' => '201807100016',
                    'title' => '中国黄金 Au9999黄金薄片投资金条50g',
                    'img_cover' => '1517389701883',
                    'sell_price' => '16088.00',
                    'bid_step' => 10,
                    'is_favorite' => 0,
                ),
            5 =>
                array(
                    'id' => 4570539,
                    'product_id' => 809,
                    'period_code' => '201807100008',
                    'title' => 'Apple iPhone X 64GB 颜色随机',
                    'img_cover' => '1505283417013',
                    'sell_price' => '9227.00',
                    'bid_step' => 10,
                    'is_favorite' => 0,
                ),
            6 =>
                array(
                    'id' => 4571397,
                    'product_id' => 643,
                    'period_code' => '201807100035',
                    'title' => '中国黄金 Au9999黄金薄片投资金条20g',
                    'img_cover' => '1517389610477',
                    'sell_price' => '6431.00',
                    'bid_step' => 10,
                    'is_favorite' => 0,
                ),
            7 =>
                array(
                    'id' => 4571406,
                    'product_id' => 850,
                    'period_code' => '201807100030',
                    'title' => '华为 Mate 10 Pro 全网通 6GB+64GB 颜色随机',
                    'img_cover' => '1508487287928',
                    'sell_price' => '5389.00',
                    'bid_step' => 10,
                    'is_favorite' => 0,
                ),
            8 =>
                array(
                    'id' => 4571410,
                    'product_id' => 814,
                    'period_code' => '201807100009',
                    'title' => '中国黄金 Au9999黄金薄片投资金条100g',
                    'img_cover' => '1517389752869',
                    'sell_price' => '32175.00',
                    'bid_step' => 10,
                    'is_favorite' => 0,
                ),
            9 =>
                array(
                    'id' => 4571477,
                    'product_id' => 280,
                    'period_code' => '201807100051',
                    'title' => '海信 BCD-535WTVBP/Q 535L 对开门冰箱 变频节能静音 （流光金）',
                    'img_cover' => '1493889435765',
                    'sell_price' => '3959.00',
                    'bid_step' => 10,
                    'is_favorite' => 0,
                ),
            10 =>
                array(
                    'id' => 4571494,
                    'product_id' => 951,
                    'period_code' => '201807100051',
                    'title' => 'OPPO R15 全面屏手机 6G+128G 颜色随机',
                    'img_cover' => '1521443309021',
                    'sell_price' => '3299.00',
                    'bid_step' => 10,
                    'is_favorite' => 0,
                ),
            11 =>
                array(
                    'id' => 4571499,
                    'product_id' => 284,
                    'period_code' => '201807100043',
                    'title' => 'vivo Xplay6 全网通 6GB+128GB 4G手机 颜色随机',
                    'img_cover' => '1493889744047',
                    'sell_price' => '4948.00',
                    'bid_step' => 10,
                    'is_favorite' => 0,
                ),
            12 =>
                array(
                    'id' => 4571507,
                    'product_id' => 407,
                    'period_code' => '201807100026',
                    'title' => '大疆“御”Mavic Pro 可折叠4K超清航拍无人机 全能套装',
                    'img_cover' => '1496405945137',
                    'sell_price' => '8799.00',
                    'bid_step' => 10,
                    'is_favorite' => 0,
                ),
            13 =>
                array(
                    'id' => 4571520,
                    'product_id' => 146,
                    'period_code' => '201807100031',
                    'title' => 'Apple iPhone 7 128GB 颜色随机',
                    'img_cover' => '1492654169352',
                    'sell_price' => '6807.00',
                    'bid_step' => 10,
                    'is_favorite' => 0,
                ),
            14 =>
                array(
                    'id' => 4571545,
                    'product_id' => 812,
                    'period_code' => '201807100054',
                    'title' => '中国黄金 Au9999黄金薄片投资金条10g',
                    'img_cover' => '1517389656916',
                    'sell_price' => '3248.00',
                    'bid_step' => 10,
                    'is_favorite' => 0,
                ),
            15 =>
                array(
                    'id' => 4571558,
                    'product_id' => 158,
                    'period_code' => '201807100069',
                    'title' => '小米 Ninebot九号平衡车 颜色随机',
                    'img_cover' => '1489658629414',
                    'sell_price' => '2199.00',
                    'bid_step' => 10,
                    'is_favorite' => 0,
                ),
            16 =>
                array(
                    'id' => 4571585,
                    'product_id' => 853,
                    'period_code' => '201807100004',
                    'title' => '中国黄金 Au9999黄金薄片投资金条200g',
                    'img_cover' => '1517389895501',
                    'sell_price' => '63910.00',
                    'bid_step' => 10,
                    'is_favorite' => 0,
                ),
            17 =>
                array(
                    'id' => 4571529,
                    'product_id' => 949,
                    'period_code' => '201807100046',
                    'title' => 'vivo X21 全面屏 双摄美颜拍照手机 6GB+128GB 颜色随机',
                    'img_cover' => '1521531050452',
                    'sell_price' => '3518.00',
                    'bid_step' => 10,
                    'is_favorite' => 0,
                ),
            18 =>
                array(
                    'id' => 4571592,
                    'product_id' => 141,
                    'period_code' => '201807100028',
                    'title' => 'Apple iPhone 7 Plus 128GB 颜色随机',
                    'img_cover' => '1489655988379',
                    'sell_price' => '7907.00',
                    'bid_step' => 10,
                    'is_favorite' => 0,
                ),
            19 =>
                array(
                    'id' => 4570749,
                    'product_id' => 636,
                    'period_code' => '201807100008',
                    'title' => 'Apple iPhone X 256GB 颜色随机',
                    'img_cover' => '1505282863087',
                    'sell_price' => '10657.00',
                    'bid_step' => 10,
                    'is_favorite' => 0,
                ),

        );
        self::showMsg($data);
    }


    public function period()
    {
        $model = new Period();
        $model->saveData(2);
    }
}