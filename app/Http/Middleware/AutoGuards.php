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
    public function handle($request, Closure $next, $guard = 'customer')
    {

        $Mua = $request->header('Mua') ?: (config('app.debug')? $request->get('Mua'):$guard);
        $guard = $Mua ?: $guard;
        Auth::setDefaultDriver($guard);
        return $next($request);
    }
}
