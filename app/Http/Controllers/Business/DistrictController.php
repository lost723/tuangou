<?php

namespace App\Http\Controllers\Business;

use App\Exceptions\BusinessException;
use App\Exceptions\VersionExpiredException;
use App\Http\Controllers\Controller;
use App\Models\Business\District;
use App\Models\Business\DistrictItem;
use App\Models\Business\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DistrictController extends Controller
{
    /**
     * todo
     * 获取某商户的区域模版
     * 供商家后台使用
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function index(Request $request)
    {
        try{
            $result =  District::getList($request);
            return $this->ok($result);
        }catch (\Exception $e){
            return $this->ok($e->getMessage());
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
            District::create($all);
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
     * @param  \App\Models\Business\District  $district
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try{
            $all = $request->all();
            $obj = District::find($id);
            # 同时修改控制
            if($obj->version > $all['version']){
                throw new VersionExpiredException();
            }
            $all['version'] = time();
            $this->checkBusinessOwnship($obj->orgid);
            $obj->update($all);
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
            $obj = District::find($id);
            # 同时修改控制
            if($obj->version > $request->get('version')){
                throw new VersionExpiredException();
            }
            # 执行业务
            $items = $request->get('items');
            DB::beginTransaction();
            DistrictItem::where('distid', $id)->delete();
            DistrictItem::addAll($id, $items);
            $obj->totals = count($items);
            $obj->version = time();
            $obj->save();
            DB::commit();
            return $this->created('更新成功');
        }catch (\Exception $e){
            DB::rollBack();
            return $this->warning($e->getMessage());
        }
    }

    /**
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCommunitys($id)
    {
        try{
            $obj = District::find($id);
            $this->checkBusinessOwnship($obj->orgid);
            $result = District::getCommunitys($id);
            return $this->ok($result);
        }catch (\Exception $e){
            return $this->ok($e->getMessage());
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
            # 有使在产品中使用过，不能删除
            if(Product::getByDistrict($id)) {
                throw new BusinessException('此区域模版还在商品中使用，不能删除。');
            }
            # 先删除 items
            DistrictItem::where('distid', $id)->delete();
            # 后删除记录本身
            District::destory($id);
        }catch (BusinessException $e){
            $this->warning($e->getMessage());
        } catch (\Exception $e){
            throw $e;
        }
    }
}
