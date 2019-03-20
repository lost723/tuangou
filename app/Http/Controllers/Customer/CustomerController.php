<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Log\PayLog;
use App\Http\Resources\Customer\PromotionDetail;
use App\Http\Resources\Customer\Promotions as PromotionResource;
use App\Http\Resources\Customer\PurchaseRecord;
use App\Models\Customer\Promotion;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    # 用户首页 + 详情页 所需接口数据

    /**
     * 获取该小区内的所有的商品活动 =》 修改为 以团长为中心的活动
     * @param $id  小区id
     */
    public function getCommPromotions(Request $request)
    {
        try{
            $promotions = Promotion::getPromotions($request);
            $list = PromotionResource::collection(($promotions));
            return $this->okWithResourcePaginate($list);
        }
        catch (\Exception $exception) {
            return $this->warning($exception->getMessage());
        }
    }


    /**
     * 获取某个团长活动详情
     * #todo 记录访问数据
     * @param $id 团长活动id
     * @return PromotionDetail|\Illuminate\Http\JsonResponse
     */
    public function getPromotionDetail(Request $request)
    {
        try{
            $id = $request->post('id');
            $promotion = Promotion::getPromotion($id);
            if(!$promotion) {
                throw new \Exception('商品走丢了');
            }
            $resouce = new PromotionDetail($promotion);
            return $this->okWithResource($resouce);
        }
        catch (\Exception $exception) {
            return $this->warning($exception->getMessage());
        }
    }


    /**
     * #todo 应该是以商品为单位 且在该团长下的购买记录 而不是以活动
     * 商品的购买记录
     * @param $id 团长活动id
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function record(Request $request)
    {
        try{
            $list =  Promotion::getPurchaseRecord($request);
            $result = PurchaseRecord::collection($list);
            return $this->okWithResourcePaginate($result);
        }
        catch (\Exception $exception) {
            return $this->warning($exception->getMessage());
        }
    }

    /**
     * 邻居购买推荐
     * todo 数据查询连接过多 需后期优化
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
   public function recommend(Request $request)
   {
       try{
           # 获取商品分类id
           $id = $request->post('id');
           $cats = Promotion::getCategroy($id);
           $promotions = Promotion::getRecommendPromotions($request, $cats->id);
           $list = PromotionResource::collection(($promotions));
           return $this->okWithResourcePaginate($list);
       }
       catch (\Exception $exception) {
           return $this->warning($exception->getMessage());
       }
   }

}
