<?php

namespace App\Models\Customer;

use App\Models\BaseModel;

class Leader extends BaseModel
{
    //
    const NORMAL = 1;
    const CREATE = 2;
    const FROZEN = -1;
    const DENY   = 0;

    protected $fillable = ['customer_id', 'community_id', 'name', 'mobile', 'idcard', 'idcard_front_url',
        'idcard_back_url', 'address', 'commission', 'status'];

    public function customer()
    {
        return $this->belongsTo('App\Models\Auth\Customer', 'customer_id');
    }

    public function community()
    {
        return $this->belongsTo('App\Models\Customer\Community', 'community_id');
    }

}
