<?php

namespace App\Http\Resources\Customer;

use Illuminate\Http\Resources\Json\Resource;

class PromotionDetail extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "id"    =>  $this->id,
            "sales" =>  $this->sales,
            "promotion" =>[
                'id'        =>  $this->promotionid,
                'price'     =>  $this->price,
                'expire'    =>  $this->expire,
                'stock'     =>  $this->stock,
            ],
            "bussiness" =>  $this->btitle,
            "leader"    =>  [
                'id'        =>  $this->leaderid,
                'commid'    =>  $this->community_id,
                'name'      =>  $this->name,
                'mobile'    =>  $this->mobile,
                'status'    =>  $this->lstatus,
            ],
            "product"   =>  [
                'title'     =>  $this->title,
                'norm'      =>  $this->norm,
                'rate'      =>  $this->rate,
                'quotation' =>  $this->quotation,
                'intro'     =>  $this->intro,
                'picture'   =>  $this->picture,
                'content'   =>  $this->content,

            ],
            "cate"      =>  [
                'id'        =>  $this->cid,
                'title'     =>  $this->ctitle,
                'parentid'  =>  $this->parentid,
                'level'     =>  $this->level,
                'logo'      =>  $this->logo,
            ]

        ];
    }
}