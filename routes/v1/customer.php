<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/22
 * Time: 18:25
 */
# 用户首页 商品列表页
Route::get('customer/list/promotion', 'Customer\CustomerController@index');
# 生成订单
# 订单列表
# 订单详情
# 订单退款
Route::resource('customer/order','Customer\OrderController');



