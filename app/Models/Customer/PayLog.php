<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Model;

class PayLog extends Model
{
    protected $fillable = ['customerid', 'transaction_id', 'trade_no', 'fee', 'status', 'note'];
    protected $table= 'paylogs';
    static function crateLog($data)
    {
        return self::firstOrCreate(
            [
                'trade_no'      =>      $data['trade_no']
            ],
            [
                'transaction_id'=>  '',
                'customerid'    =>  $data['customerid'],
                'fee'           =>  $data['fee'],
                'status'        =>  $data['status'],
                'note'          =>  $data['note'],
            ]);
    }


    static function updateLog($data)
    {
        return self::updateOrCreate(
            [
                'trade_no'      =>  $data['trade_no'],
            ],
            [
                'status'        =>  $data['status'],
                'transaction_id'=>  $data['transaction_id'],
            ]);
    }
}
