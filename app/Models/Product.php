<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $table = 'product';

    const BUY_BY_DIFF_NO = 0;
    const BUY_BY_DIFF_YES = 1;
    public static $buyByDiff = [
        self::BUY_BY_DIFF_NO => '不可差价购',
        self::BUY_BY_DIFF_YES => '可差价购',
    ];
}
