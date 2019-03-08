<?php

namespace App\Models\Common;

use App\Models\BaseModel;

class Leader extends BaseModel
{
    //
    const FROZEN = 0;  # 身份冻结
    const DENY   = 1;  # 审核拒绝
    const CREATE = 2;  # 审核中
    const NORMAL = 3;  # 正常

    protected $fillable = ['customerid', 'commid', 'name', 'mobile', 'idcard', 'idcard_front_url',
        'idcard_back_url', 'address', 'commission', 'status'];



}
