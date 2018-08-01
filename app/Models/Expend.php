<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expend extends Common
{
    protected $table = 'expend';
    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'user_id',
        'amount',
        'pay_amount',
        'name',
        'product_id',
        'period_id',
    ];

    /** 竞拍支出 */
    public function bidPay($data)
    {
        self::create($data);
    }

}
