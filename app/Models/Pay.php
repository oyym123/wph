<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Pay extends Common
{
    use SoftDeletes;

    protected $table = 'pay';
    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [
        'pay_amount',
        'pay_type',
        'status',
        'out_trade_no', //返回的流水号
        'out_trade_status', //支付状态中文显示
        'log',
        'order_id',
        'sn',
        'paid_at', //支付时间
    ];
    const TYPE_WEI_XIN = 1;

    const STATUS_UNPAID = 10;//未支付
    const STATUS_ALREADY_PAY = 20;//已支付
}
