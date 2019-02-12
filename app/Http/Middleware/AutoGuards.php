<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AutoGuards
{
    /**
     * 在HTTP Header 内定义 Mua 字段，实现多用户表自动切换
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param  string|null $guard
     * @return mixed
     * @throws \Exception
     */
    public function handle($request, Closure $next, $guard = null)
    {

        if(empty($guard)){
            $guard =  $request->header('Mua');
            if(env('APP_DEBUG')){
                $guard = $guard ?: $request->get('Mua');
            }
            if(empty($guard)){
                throw new \Exception('Mua is missing ...');
            }
        }
        Auth::setDefaultDriver($guard);

        return $next($request);
    }
}
