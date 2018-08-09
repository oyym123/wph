<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Withdraw extends Model
{
    protected $table = 'withdraw';

    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'amount',
        'status',
        'withdraw_at', //处理的时间
        'account', //账号状态
    ];

    public function saveData($data)
    {
        self::create($data);
    }
}
