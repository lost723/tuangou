<?php

namespace App\Http\Controllers\Customer;

use App\Http\Resources\Customer\PromotionDetail;
use App\Http\Resources\Customer\Promotions as PromotionResource;
use App\Http\Resources\Customer\PurchaseRecord;
use App\Http\Resources\Customer\Category as CategroyResource;
use App\Models\Common\Category;
use App\Models\Customer\Promotion;
 use App\Http\Controllers\Controller;

class CustomerController extends Controller
{
    # 用户首页 + 详情页 所需接口数据
    public function __construct()
    {
        # todo 正式生产环境 需进行token校验
        $this->middleware('auth', ['except' =>  ['getCategories', 'getPromotions', 'getPromotionDetail'
        , 'purchaseRecord']]);
    }

    /**
     * 轮播图
     * @param $id 小区id
     * @return array
     */
    public function getSlides($id)
    {
        # todo 轮播图
        $list = [];
        return $list;
    }

    /**
     * 获取分类信息
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getCategories()
    {
        try{
            $cates = Category::getTopLevelCategory();
            return CategroyResource::collection($cates);
        }
        catch (\Exception $exception) {
            return $this->warning($exception->getMessage());
        }
    }

    /**
     * 获取该小区内的所有的商品活动
     * @param $id  小区id
     */
    public function getPromotions($commid)
    {
        try{
            $promotions = Promotion::getPromotions($commid);
            return PromotionResource::collection(($promotions));
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
    public function getPromotionDetail($id)
    {
        try{
            return new PromotionDetail(Promotion::getPromotion($id));
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
    public function purchaseRecord($id)
    {
        try{
            return Promotion::getPurchaseRecord($id);
        }
        catch (\Exception $exception) {
            return $this->warning($exception->getMessage());
        }
    }


}
