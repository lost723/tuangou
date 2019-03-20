<?php

namespace App\Models\Customer;


use App\Models\BaseModel;

class RefundLog extends BaseModel
{
    const ERROR = 1;
    protected $fillable = ['customerid', 'trade_no', 'refund_id', 'fee', 'status', 'note'];
    protected $table='refundlogs';

    static function createLog($data)
    {
        return self::firstOrCreate(
        [
            'trade_no'      =>  $data['trade_no']
        ],
        [
            'refund_id'     =>  '',
            'customerid'    =>  $data['customerid'],
            'fee'           =>  $data['fee'],
            'status'        =>  $data['status'],
            'note'          =>  $data['note'],
        ]);
    }
}
