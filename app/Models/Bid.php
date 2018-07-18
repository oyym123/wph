<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bid extends Model
{
    use SoftDeletes;

    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [
        'product_id',
        'period_id',
        'bid_price',
        'user_id',
        'status',
        'bid_step',
        'nickname',
        'product_title',
        'end_time',
    ];

    protected $table = 'bid';

    public function saveData($data)
    {
        $model = self::create($data);
        $model->save();
    }
}
