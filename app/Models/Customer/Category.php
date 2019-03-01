<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Category extends Model
{
    protected $fillable = ['parentid', 'level', 'title', 'logo'];
    # 获取所有分类
    static function getAllCategories()
    {
        return DB::table('categories')->get();
    }

    # 获取顶级分类信息
    static function getTopLevelCategory()
    {
        return DB::table('categories')->where('parentid', 0)->get();
    }
}
