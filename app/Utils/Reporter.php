<?php
namespace App\Utils;

trait Reporter
{

    /**
     * 正常返回
     * @param null $result
     * @return \Illuminate\Http\JsonResponse
     */
    public function ok($result=null)
    {
        return response()->json($result, 200);
    }


    /**
     *
     * @param null $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function note($message=null)
    {
        $msg = '请求成功';
        if($message) $msg = $message;
        return $this->jsonage($msg, 200);
    }

    /**
     * @param null $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function warning($message=null)
    {
        $msg = '操作禁止';
        if($message) $msg = $message;
        return $this->jsonage($msg, 400);
    }

    /**
     * 创建成功
     * @param null $message
     * @return \Illuminate\Http\JsonResponse
     * @internal param null $result
     */
    public function created($message=null)
    {
        $msg = '创建成功';
        if($message) $msg = $message;
        return $this->jsonage($msg, 201);
    }

    /**
     * 无内容
     * @param null $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function nocontent($message=null)
    {
        $msg = '无内容';
        if($message) $msg = $message;
        return $this->jsonage($msg, 204);
    }

    /**
     * 未授权
     * @param null $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function unauthed($message=null)
    {
        $msg = '未授权';
        if($message) $msg = $message;
        return $this->jsonage($msg, 401);
    }



    /**
     * 统一返回格式
     * @param $message
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function jsonage($message,$code)
    {
        return response()->json(['message' => $message], $code);
    }


    protected function okWithResource($data= [], $message = '请求成功', $code = 1)
    {
        return response()->json([
            'data'  =>  $data,
            'message'   =>  $message,
            'code'  =>  $code,
        ], 200);
    }

    protected function okWithResourcePaginate($data= [], $message = '请求成功', $code = 1)
    {
        return response()->json(['data' => [
            'data'  =>  $data,
            'message'   =>  $message,
            'code'  =>  $code,
        ],
            'link'=> [
                'total'     =>  $data->total(),
                'pageSize'  =>  $data->perPage(),
                'current'   =>  $data->currentPage(),
            ]], 200);
    }

}