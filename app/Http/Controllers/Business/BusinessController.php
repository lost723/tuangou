<?php

namespace App\Http\Controllers\Business;


use App\Models\Business\Business;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Exceptions\BusinessException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class BusinessController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{
            $user = Auth::user();
            if(!empty($user->orgid)){
                throw new BusinessException('不能重复创建商户！');
            }
            $count = Business::where('title', $request->get('title'))->count();
            if($count>=1){
                throw new BusinessException('商户名称已经被占有，请重新选择！');
            }
            # 开启你的表演
            DB::beginTransaction();
            $item = Business::create($request->all());
            $user->orgid = $item->id;
            $user->save();
            DB::commit();
            return $this->ok([$user, $item]);
        }catch (BusinessException $e){
            return $this->warning($e->getMessage());
        }catch (\Exception $e){
            DB::rollBack();
            return $this->warning($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Business\Business  $business
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try{
            #商家只能看自己的信息
            $this->checkBusinessOwnship($id);
            $item = Business::find($id);
            return $this->ok($item);
        }catch (\Exception $e){
            return $this->warning($e->getMessage());
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Business\Business  $business
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try{
            $all = $request->all();
            $title = $request->get('title');
            $this->checkBusinessOwnship($id);

            $item = Business::find($id);
            $count = Business::where('title', $title)->count();
            if($count>=1 and $item->title <> $title){
                throw new BusinessException('商户名称已经被占有，请重新选择！');
            }
            # 不可改名
//            if(key_exists('title', $all)){
//                unset($all['title']);
//            }
            $item->update($all);
            $item->save();
            return $this->ok();
        }catch (\Exception $e){
            return $this->warning($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Business\Business  $business
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            $this->checkTraderOwnship();
            $item = Business::find($id);
            $item->status = Business::Frozen;
            $item->save();
            return $this->ok();
        }catch (\Exception $e){
            return $this->warning($e->getMessage());
        }
    }




}
