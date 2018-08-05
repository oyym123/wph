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

    /** 支出明细 */
    public function detail($userId)
    {
        $expends = Expend::where([
            'user_id' => $userId
        ])->offset($this->offset)->limit($this->limit)->orderBy('created_at', 'desc')->get();
        $data = [];
        foreach ($expends as $expend) {
            $data[] = [
                'title' => $expend->name,
                'created_at' => $expend->created_at,
                'amount' => '-' . round($expend->amount) . $this->getCurrencyStr($expend->type),
            ];
        }
        return $data;
    }
}
