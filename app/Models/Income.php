<?php

namespace App\Models;

use App\User;
use Illuminate\Support\Facades\DB;

class Income extends Common
{
    protected $table = 'income';
    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'user_id',
        'amount',
        'return_proportion', //返还比例
        'used_amount', //使用的金额
        'name',
        'product_id',
        'expired_at',
        'period_id',
    ];

    /** 结算所有未竞拍用户的返回拍币金额 */
    public static function settlement($periodId, $userId)
    {
        $bids = DB::table('bid')
            ->select(DB::raw('product_id,pay_amount,count(id) as counts, user_id'))
            ->where([
                'period_id' => $periodId,
                'status' => Bid::STATUS_FAIL,
                'pay_type' => self::TYPE_BID_CURRENCY, //只转换拍币
                'is_real' => User::TYPE_REAL_PERSON
            ])->where('user_id', '<>', $userId)//竞拍成功者没有返回购物币
            ->groupBy(['user_id', 'pay_amount', 'product_id'])
            ->get();
        foreach ($bids as $bid) {
            $data = [
                'user_id' => $bid->user_id,
                'type' => self::TYPE_SHOPPING_CURRENCY,
                'amount' => $bid->pay_amount * $bid->counts * config('bid.return_proportion'),
                'return_proportion' => config('bid.return_proportion'),
                'used_amount' => $bid->pay_amount * $bid->counts,
                'name' => '订单拍币返还',
                'expired_at' => config('bid.bid_currency_expired_at'),
                'product_id' => $bid->product_id,
                'period_id' => $periodId
            ];
            self::create($data);//保存记录
            DB::table('users')->where(['id' => $bid->user_id])->increment('shopping_currency', $data['amount']);
        }
    }

    /** 自动竞拍成功，返回剩余拍币 */
    public function autoSettlement($data, $userId)
    {
        self::create($data);//保存记录
        if ($data['type'] == self::TYPE_GIFT_CURRENCY) {
            DB::table('users')->where(['id' => $userId])->increment('gift_currency', $data['amount']);
        } elseif ($data['type'] == self::TYPE_BID_CURRENCY) {
            DB::table('users')->where(['id' => $userId])->increment('bid_currency', $data['amount']);
        }
    }
}
