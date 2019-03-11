<?php

namespace App\Http\Controllers\Customer;

use App\Http\Resources\Customer\PromotionDetail;
use App\Http\Resources\Customer\Promotions as PromotionResource;
use App\Http\Resources\Customer\PurchaseRecord;
use App\Models\Customer\Promotion;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;

class CustomerController extends Controller
{
    # 用户首页 + 详情页 所需接口数据

    /**
     * 获取该小区内的所有的商品活动
     * @param $id  小区id
     */
    public function getCommPromotions(Request $request)
    {
        try{
            $promotions = Promotion::getPromotions($request);
            return PromotionResource::collection(($promotions));
//            $list = PromotionResource::collection(($promotions));
        }
        catch (\Exception $exception) {
            return $this->warning($exception->getMessage());
        }
    }


    /**
     * 获取某个团长活动详情
     * @param $id 团长活动id
     * @return PromotionDetail|\Illuminate\Http\JsonResponse
     */
    public function getPromotionDetail(Request $request)
    {
        try{
            $id = $request->post('id');
            $promotion = Promotion::getPromotion($id);
            if($promotion) {
                return new PromotionDetail($promotion);
            }
            return $this->ok(['data'=>[]]);

        }
        catch (\Exception $exception) {
            return $this->warning($exception->getMessage());
        }
    }


    /**
     * 商品的购买记录
     * @param $id 团长活动id
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function record(Request $request)
    {
        try{
            return Promotion::getPurchaseRecord($request);
        }
        catch (\Exception $exception) {
            return $this->warning($exception->getMessage());
        }
    }


}
