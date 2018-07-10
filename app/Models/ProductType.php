<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class ProductType extends Model
{
    use SoftDeletes;
    protected $table = 'product_type';

    public static function getOne($id, $field = [])
    {
        return DB::table('product_type')->select($field)->where('id', $id)->first();
    }

    public static function getList($flag = null)
    {
        $data = [];
        foreach (ProductType::all() as $item) {
            if ($flag) {
                $data[$item->id] = $item->name;
            } else {
                $data[] = [
                    'id' => $item->id,
                    'title' => $item->name
                ];
            }
        }
        return $data;
    }
}
