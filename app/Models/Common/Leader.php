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

    static function createLeader($data)
    {
        $leader =  self::updateOrCreate(
            [
                'customerid'    =>  $data['customerid'],
            ],
            [
                'commid'            =>  $data['commid'],
                'name'              =>  $data['name'],
                'mobile'            =>  $data['mobile'],
                'idcard'            =>  $data['idcard'],
                'idcard_front_url'  =>  $data['idcard_front_url'],
                'idcard_back_url'   =>  $data['idcard_back_url'],
                'address'           =>  $data['address'],
                'commission'        =>  $data['commission'],
                'status'            =>  empty($data['status'])? Leader::CREATE:$data['status'],
            ]);

        $leader->leaderno = self::getLeaderNo($leader->id);
        return $leader;
    }

}
