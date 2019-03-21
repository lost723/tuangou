<?php

namespace App\Http\Controllers\Business;

use App\Exceptions\VersionExpiredException;
use App\Http\Controllers\Controller;
use App\Models\Business\District;
use App\Models\Business\Product;
use Illuminate\Http\Request;
use App\Models\Business\Promotion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class PromotionController extends Controller
{
    /**
     * 过滤商品活动列表
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try{
            $result = Promotion::getBusinessOwnList($request);
            return $this->ok($result);
        }catch (\Exception $e){
            return $this->warning($e->getMessage());
        }
    }

    /**
     * 创建保存一个活动
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{
            $user = Auth::user();
            $all = $request->all();
            $all['start'] = time();
            $all['optid'] = $user->id;
            $all['orgid'] = $user->orgid;
            if(!$all['published']){
                $all['status'] = Promotion::Unpublished;
            }else{
                $all['status'] = Promotion::Ordering;
            }
            DB::beginTransaction();
            $pm = Promotion::create($all);
            # 活动期数减一
            Product::find($pm->productid)->increment('issue');
            DB::commit();
            return $this->created();
        }catch (\Exception $e){
            DB::rollBack();
            return $this->warning($e->getMessage());
        }
    }

    /**
     * 展示一个活动的详情，不包括商品信息
     *
     * @param  \App\Models\Business\Promotion  $promotion
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try{
            $item = Promotion::find($id);
            $item->product = Product::find($item->productid);
            $item->district = District::find($item->product->distid);
            $this->checkBusinessOwnship($item->orgid);
            return $this->ok($item);
        }catch (\Exception $e){
            return $this->warning($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Business\Promotion  $promotion
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try{
            $all = $request->all();
            $item = Promotion::find($id);
            if($item->version > $all['version']){
                throw new VersionExpiredException();
            }
            $all['version'] = time();
            $this->checkBusinessOwnship($item->orgid);
            $item->update($all);
            return $this->ok($item);
        }catch (\Exception $e){
            return $this->warning($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Business\Promotion  $promotion
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            $item = Product::find($id);
            $this->checkProductOwnShip() ($item->id);
            # 如果还有没结束的活动，不能删除
            if ($item->status<>Promotion::Unpublished){
                throw new \Exception('删除失败：只能删除未发布的活动。');
            }
            $item->status = Promotion::Deleted;
            $item->save();
            return $this->note('删除成功');
        }catch (\Exception $e){
            return $this->warning($e->getMessage());
        }
    }

    /**
     * 设置活动状态
     * @param $id
     * @param Request $request
     */
    public function setStatus($id, Request $request)
    {
        try{
            $stat = $request->get('status');
            $item = Promotion::find($id);
            if(in_array($stat, Promotion::Status) and $stat>$item->status){
                $item->status = $stat;
                # 新发布，重置开始时间
                if($stat == Promotion::Ordering){
                    $item->start = time();
                }
                $item->save();
                return $this->note();
            }else{
                return $this->warning('无效的状态');
            }ok($result);
        }catch (\Exception $e){
            return $this->warning($e->getMessage());
        }
    }

    /**
     * 获取分销团长列表
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLeaderList($id)
    {
        try{
            $result = Promotion::getLeaderList($id);
            return $this->ok($result);
        }catch (\Exception $e){
            return $this->warning($e->getMessage());
        }
    }

    /**
     * 统计团长分销量
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function summaryByLeaders($id)
    {
        try{
            $result = Promotion::summaryByLeaders($id);
            return $this->ok($result);
        }catch (\Exception $e){
            return $this->warning($e->getMessage());
        }
    }

}
