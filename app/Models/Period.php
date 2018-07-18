<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Period extends Model
{
    use SoftDeletes;

    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [
        'product_id',
        'code',
    ];

    protected $table = 'period';


    const STATUS_NOT_START = 0;
    const STATUS_IN_PROGRESS = 1;
    const STATUS_OVER = 2;

    public static function getStatus($key = '')
    {
        $data = [
            self::STATUS_NOT_START => '未开始',
            self::STATUS_IN_PROGRESS => '正在进行',
            self::STATUS_OVER => '已结束',
        ];
        return $key ? $data[$key] : $data;
    }

    /**
     * 根据产品保存期数
     */
    public function saveData($product_id)
    {
        $dayStart = date('Y-m-d', time()) . ' 00:00:00';
        $dayEnd = date('Y-m-d', time()) . ' 23:59:59';

        $check = DB::table('period')
            ->whereBetween('created_at', [$dayStart, $dayEnd])
            ->where('product_id', '=', $product_id)
            ->orderBy('created_at', 'desc')
            ->first();

        $period = $check ? intval(substr($check->code, -4)) : 0;

        $code = date('Ymd', time()) . str_pad($period + 1, 4, '0', STR_PAD_LEFT);

        $data = [
            'product_id' => $product_id,
            'code' => $code,
        ];
        $model = self::create($data);
        $model->save();
    }
}
