<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Vouchers extends Common
{
    use SoftDeletes;
    protected $table = 'vouchers';
    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [
        'product_id',
        'user_id',
        'status',
        'expired_at',
        'content',
        'amount',
        'count',
    ];

    const STATUS_ENABLE = 10; //可用
    const STATUS_ALREADY_USED = 20;//已使用

    public function saveData($data)
    {
        $model = self::where([
            'product_id' => $data['product_id'],
            'status' => self::STATUS_ENABLE,
            'user_id' => $data['user_id']
        ])->first();

        if ($model) {
            DB::table('vouchers')->where(['id' => $model->id])->update($data);
            return $model->id;
        } else {
            $model = self::create($data);
            return $model->id;
        }
    }
}
