<?php
/**
 * Created by PhpStorm.
 * User: likun
 * Date: 2019/2/27
 * Time: 11:32 AM
 */

namespace App\Http\Controllers\Common;


use App\Http\Controllers\Controller;

class ToolsController extends Controller
{

    /**
     * Create a new instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth', ['except' => []]);
    }

    /**
     * 检测服务器状态
     * 附带有更新Token之作用
     * @return \Illuminate\Http\JsonResponse
     */
    public function ping()
    {
        return $this->note('working...');
    }

}