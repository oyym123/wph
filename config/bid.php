<?php
/**
 * Created by PhpStorm.
 * User: Alienware
 * Date: 2018/7/25
 * Time: 16:24
 */
date_default_timezone_set('PRC');
return [
    'wx_app_id' => 'wx4ac6bfc69de292fc',
    'wx_mch_id' => '1510294251',
    'robot_rate' => mt_rand(4, 20) / 100, //随机概率（即没有真人参与时，中标价不超过售价的4%~20%）
    'init_countdown' => 300, //初始化竞拍时间5分钟
    'return_proportion' => 1, //购物币返还比例
    'order_expired_at' => date('Y-m-d H:i:s', time() + 86400 * 10), //订单过期时间
    'bid_currency_expired_at' => date('Y-m-d H:i:s', time() + 86400 * 30), //返还的购物币过期时间
];