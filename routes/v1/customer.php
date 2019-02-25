<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/22
 * Time: 18:25
 */
# 用户首页 商品列表页
Route::resource('customer/promotion', 'Customer\CustomerController');
# 商品详情页

# 生成订单 createOrder
# 支付订单 payOrder
# 取消订单 cancelOrder
# 订单列表 orderList （不同的支付状态）
# 订单详情 orderDetail
Route::post('customer/order/create','Customer\OrderController@createOrder');
Route::get('customer/order/pay', 'Customer\OrderController@payOrder');
Route::get('customer/order/cancel', 'Customer\OrderController@cancelOrder');
Route::post('customer/order/list', 'Customer\OrderController@listOrder');
Route::get('customer/order/detail', 'Customer\OrderController@detailOrder');
# 订单退款
Route::get('customer/order/refund', 'Customer\OrderController@refundOrder');


