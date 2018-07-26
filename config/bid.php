<?php
/**
 * Created by PhpStorm.
 * User: Alienware
 * Date: 2018/7/25
 * Time: 16:24
 */
return [
    'robot_rate' => mt_rand(4, 20) / 100, //随机概率（即没有真人参与时，中标价不超过售价的4%~20%）
    'init_countdown' => 300, //初始化竞拍时间5分钟
];