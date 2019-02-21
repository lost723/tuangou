<?php

namespace App\Http\Controllers\Business;

use App\Exceptions\BusinessException;
use App\Http\Controllers\Controller;
use App\Models\Business\District;
use App\Models\Business\DistrictItem;
use App\Models\Business\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DistrictController extends Controller
{
    /**
     * 获取某商户的区域模版
     * 供商家后台使用
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function index(Request $request)
    {
        try{
            $orgid = Auth::user()->orgid;
            $result = District::where('orgid', $orgid)->get();
            return $this->ok($result);
        }catch (\Exception $e){
            return $this->warning($e->getMessage());
        }
    }


    /**
     * 存储一个区域模版，不包括小区列表
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{
            $all = $request->all();
            $all['orgid'] = Auth::user()->orgid;
            $item = District::create($all);
            return $this->created('创建成功');
        }catch (\Exception $e){
            return $this->warning($e->getMessage());
        }
    }

    /**
     * 获取模版，不带详情
     *
     * @param  \App\Models\Business\District  $district
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try{
            $item = District::findWithItmes($id);
            return $this->ok($item);
        }catch (\Exception $e){
            return $this->warning($e->getMessage());
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Business\District  $district
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try{
            $all = $request->all();
            $obj = District::find($id)->update($all);
            return $this->ok($obj);
        }catch (\Exception $e){
            return $this->warning($e->getMessage());
        }
    }

    /**
     * 更新区域模版下的小区列表
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateItems(Request $request, $id)
    {
        try{
            $items = $request->get('items');
            DistrictItem::where('orgid', $id)->delete();
            DistrictItem::addAll($items);
            return $this->created('更新成功');
        }catch (\Exception $e){
            return $this->warning($e->getMessage());
        }
    }

    /**
     * 物理删除
     *
     * @param $id
     * @return void
     * @throws \Exception
     */
    public function destroy($id)
    {
        try{
            # 先检查有没有使用过
            $products = Product::getByDistrict($id);
            if($products) {
                throw new BusinessException('此区域模版还在商品中使用，不能删除。');
            }
            # 没有使用过
            # 先删除 items
            DistrictItem::where('orgid', $id)->delete();
            # 后删除记录本身
            District::destory($id);
        }catch (BusinessException $e){
            $this->warning($e->getMessage());
        } catch (\Exception $e){
            throw $e;
        }
    }
}
