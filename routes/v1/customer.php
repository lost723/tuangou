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
Route::post('customer/promotions/recommend', 'Customer\CustomerController@recommend');


# 订单相关路由
Route::post('customer/order/add','Customer\OrderController@createOrder');
Route::post('customer/order/cancel', 'Customer\OrderController@cancelOrder');

# 订单列表 详情
Route::get('customer/order/list', 'Customer\OrderController@listOrder');# 待支付
Route::post('customer/order/sublist', 'Customer\OrderController@subOrder');
Route::post('customer/order/detail', 'Customer\OrderController@orderDetail');
Route::post('customer/order/sub/detail', 'Customer\OrderController@subOrderDetail');
Route::get('customer/order/finished', 'Customer\OrderController@finishedOrder');
Route::post('customer/order/refundlist', 'Customer\OrderController@refundOrder');

# 订单支付 退款
Route::post('customer/order/pay', 'Customer\PaymentController@Pay');
Route::post('customer/order/refund', 'Customer\RefundController@Refund');

# 统计
Route::post('customer/count/view', 'Customer\StatisticController@viewCount');
Route::post('customer/count/share', 'Customer\StatisticController@shareCount');
Route::post('customer/count/cargo', 'Customer\StatisticController@cargoCount');

#通知
Route::post('notify/pay', 'Customer\PaymentController@notify');
Route::post('notify/refund', 'Customer\RefundController@notify');





