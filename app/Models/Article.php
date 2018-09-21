<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Common
{
    protected $table = 'article';
    const TYPE_COMMON_QUESTION = 1; //常见问题


    public static function getStatus($key = 999)
    {
        $data = [
            self::TYPE_COMMON_QUESTION => '常见问题',
        ];
        return $key != 999 ? $data[$key] : $data;
    }

}
