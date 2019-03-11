<?php

namespace App\Http\Controllers\Business;

use App\Exceptions\BusinessException;
use App\Http\Controllers\Controller;
use App\Models\Business\Promotion;
use Illuminate\Http\Request;
use App\Models\Business\Product;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /**
     * 按条件，过滤某商户的商品列表
     * 供商家后台使用
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function index(Request $request)
    {
        try{
            $result = Product::getBusinessOwnList($request);
            return $this->ok($result);
        }catch (\Exception $e){
            return $this->warning($e->getMessage());
        }
    }


    /**
     * 创建一个新商品
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function store(Request $request)
    {
        try{
            $user = Auth::user();
            $all = $request->all();
            $all['optid'] = $user->id;
            $all['orgid'] = $user->orgid;
            $all['thumb'] = $all['picture'][0];
            $all['picture'] = json_encode($all['picture']);
            $all['content'] = json_encode($all['content']);
            Product::create($all);
            return $this->created();
        }catch (\Exception $e){
            return $this->warning($e->getMessage());
        }
    }

    /**
     * 获取某个产品的信息
     *
     * @param  \App\Models\Production  $production
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try{
            $item = Product::find($id);
            $item->distid = [$item->distid];

            #  处理图片
            $pictures = [];
            $contents = [];
            $pics = json_decode($item->picture,true);
            if(is_array($pics)){
                foreach ($pics as $i=>$obj){
                    $pictures[] = ['uid'=>++$i, 'name'=>$obj, 'status'=>'done', 'url'=>$obj, 'thumbUrl'=>$obj];
                }
            }
            $conts = json_decode($item->content,true);
            if(is_array($conts)){
                foreach ($conts as $i=>$obj){
                    $contents[] = ['uid'=>-++$i, 'name'=>$obj, 'status'=>'done', 'url'=>$obj, 'thumbUrl'=>$obj];
                }
            }
            $item->picture = $pictures;
            $item->content = $contents;
            return $this->ok($item);
        }catch (\Exception $e){
            return $this->warning($e->getMessage());
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Production  $production
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try{
            $all = $request->all();
            $item = Product::find($id);
            $this->checkProductOwnShip($item->orgid);
            $all['thumb'] = $all['picture'][0];
            $all['picture'] = json_encode($all['picture']);
            $all['content'] = json_encode($all['content']);
            $item->update($all);
            return $this->ok($item);
        }catch (\Exception $e){
            return $this->warning($e->getMessage());
        }
    }


    public function disable(Request $request, $id)
    {
        try{
            $item = Product::find($id);
            $this->checkProductOwnShip($item->orgid);
            $item->status = Product::Disable;
            $item->save();
            return $this->ok($item);
        }catch (\Exception $e){
            return $this->warning($e->getMessage());
        }
    }

    public function active(Request $request, $id)
    {
        try{
            $item = Product::find($id);
            $this->checkProductOwnShip($item->orgid);
            $item->status = Product::Active;
            $item->save();
            return $this->ok($item);
        }catch (\Exception $e){
            return $this->warning($e->getMessage());
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Production  $production
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            $item = Product::find($id);
            $this->checkProductOwnShip($item->id);
            # 如果还有没结束的活动，不能删除
            if (Promotion::countUnfinished($item->id) >0){
                throw new \Exception('删除失败：此商品下面有尚未完成的活动。');
            }
            $item->status = Product::Del;
            $item->save();
            return $this->note('删除成功');
        }catch (\Exception $e){
            return $this->warning($e->getMessage());
        }
    }


    /**
     * 检查所有权
     * @param $item
     * @throws BusinessException
     */
    protected  function checkProductOwnShip($orgid)
    {
        if($orgid <> Auth::user()->orgid){
            throw new BusinessException('无权操作：非产品所有人！');
        }
    }
}
