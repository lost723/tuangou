<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Community extends Model
{
    protected $fillable = ['name', 'road_id', 'address', 'longitude', 'latitude'];

    # 获取小区所属的街道
    public function road()
    {
        return $this->belongsTo('App\Models\Road', 'road_id', 'id');
    }
    
}
