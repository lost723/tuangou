<?php

namespace App\Common;

use Illuminate\Database\Eloquent\Model;

class ProfitShare extends Model
{
    const SharePrefix = '200'; # 分账订单号前缀
    const Sharing = 1; # 分账中 已创建分账订单

    protected $fillable = [];

}
