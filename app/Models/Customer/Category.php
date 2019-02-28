<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Category extends Model
{
    # 获取所有分类
    static function getAllCategories()
    {
        return DB::table('categories')->get();
    }
}
