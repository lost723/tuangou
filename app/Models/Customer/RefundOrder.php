<?php

namespace App\Models\Customer;

use App\Models\BaseModel;

class RefundOrder extends BaseModel
{
    const RefundPrefix = '200'; # 退款订单号前缀
    const Expire = 0; # 退款超时异常
    const Refunding = 1; # 退款中
    const Finished = 2; # 退款完成

}
