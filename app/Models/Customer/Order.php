<?php

namespace App\Customer\Models;

use App\Models\BaseModel;

class Order extends BaseModel
{
    const Cancel = 0; # 订单超时异常
    const Unpaid = 1; # 未支付
    const Finished = 2; # 已支付

}
