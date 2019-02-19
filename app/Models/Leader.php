<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Leader extends Model
{
    //
    protected $fillable = ['customer_id', 'community_id', 'name', 'mobile', 'idcard', 'idcard_front_url',
        'idcard_back_url', 'address', 'commission', 'status'];

    public function customer()
    {
        return $this->belongsTo('App\Models\Auth\Customer', 'customer_id');
    }

    public function community()
    {
        return $this->belongsTo('App\Models\Community', 'community_id');
    }

}
