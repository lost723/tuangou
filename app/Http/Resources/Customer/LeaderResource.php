<?php

namespace App\Http\Resources\Customer;

use App\Models\Common\Community;
use Illuminate\Http\Resources\Json\Resource;

class LeaderResource extends Resource
{
    /**
     *  id          =>      团长id
     *  commid      =>      团长小区id
     *  leaderno    =>      团长编号
     *  logo        =>      团长头像
     *  commtitle   =>      团长小区名称
     *  alias       =>      团长别称
     *  name        =>      团长姓名
     *  mobile      =>      团长手机号
     *  address     =>      团长详细地址
     *  distance    =>      距离排序
     *  commission  =>      团长佣金
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'            =>  $this->id,
            'commid'        =>  $this->commid,
            'leaderno'      =>  $this->leaderno,
            'logo'          =>  $this->logo,
            'commtitle'     =>  $this->commtitle,
            'alias'         =>  $this->alias,
            'name'          =>  $this->name,
            'mobile'        =>  $this->when(isset($this->mobile), function () {
                return $this->mobile;
            }),
            'address'       =>  $this->address,
            'distance'      =>  $this->when(isset($this->distance), function () {
                return number_format($this->distance, 1);
            }),
            'commission'    =>  $this->when(isset($this->commission), function (){
                return $this->commission;
            }),
        ];
    }
}
