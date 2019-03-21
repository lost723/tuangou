<?php

namespace App\Models\Common;

use App\Models\BaseModel;

class Leader extends BaseModel
{
    const BaseNO = 100000;
    const FROZEN = 0;  # 身份冻结
    const DENY   = 1;  # 审核拒绝
    const CREATE = 2;  # 审核中
    const NORMAL = 3;  # 正常

    protected $fillable = ['customerid', 'commid', 'leaderno', 'name', 'mobile', 'idcard', 'idcard_front_url',
        'idcard_back_url', 'address', 'commission', 'status'];


    public function customer()
    {
        return $this->belongsTo('App\Models\Auth\Customer', 'customerid', 'id');
    }

    # 获取团长编号
    static function getLeaderNo($id)
    {
        return sprintf("%06X", (self::BaseNO+$id));
    }

}
