<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/22
 * Time: 18:25
 */
## 消费者相关接口

# 商品分类信息
Route::get('category/top', 'Common\CategoryController@getCategories');
Route::resource('category', 'Common\CategoryController');


# 用户首页
Route::post('customer/promotions', 'Customer\CustomerController@getCommPromotions');
Route::post('customer/promotions/detail', 'Customer\CustomerController@getPromotionDetail');
# 已购买该商品的用户信息
Route::post('customer/promotions/record', 'Customer\CustomerController@record');


# 订单相关路由
Route::post('customer/order/add','Customer\OrderController@createOrder');
Route::post('customer/order/cancel', 'Customer\OrderController@cancelOrder');

# 订单列表 详情
Route::get('customer/order/list', 'Customer\OrderController@listOrder');
Route::get('customer/order/sublist', 'Customer\OrderController@subOrder');
Route::post('customer/order/detail', 'Customer\OrderController@orderDetail');
Route::post('customer/order/sub/detail', 'Customer\OrderController@subOrderDetail');

# 订单支付 退款
Route::post('customer/order/pay', 'Customer\PaymentController@Pay');
Route::post('customer/order/refund', 'Customer\RefundController@Refund');


#

#通知
Route::post('notify/pay', 'Customer\PaymentController@notify');
Route::post('notify/refund', 'Customer\RefundController@notify');





