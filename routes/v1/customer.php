<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/22
 * Time: 18:25
 */
## 消费者相关接口

# 商品分类信息
Route::get('customer/category', 'Customer\CustomerController@getCategories');
# 用户首页 商品列表页 id 为小区id
Route::get('customer/promotions/{commid}', 'Customer\CustomerController@getPromotions');
# 商品详情页
Route::get('customer/promotions/detail/{id}', 'Customer\CustomerController@getPromotionDetail');
# 已购买该商品的用户信息
Route::get('customer/promotions/purchaserecord/{id}', 'Customer\CustomerController@purchaseRecord');


# 订单相关路由
# 生成订单 createOrder
# 支付订单 payOrder
# 取消订单 cancelOrder
# 订单列表 orderList （不同的支付状态）
# 订单详情 orderDetail
Route::post('customer/order/create','Customer\OrderController@createOrder');
Route::get('customer/order/pay/{id}', 'Customer\OrderController@payOrder');
Route::get('customer/order/cancel/{id}', 'Customer\OrderController@cancelOrder');
Route::post('customer/order/list', 'Customer\OrderController@listOrder');
Route::get('customer/order/detail/{id}', 'Customer\OrderController@detailOrder');
Route::get('customer/order/detailpromotion/{id}', 'Customer\OrderController@detailPromotionOrder');
# 订单退款
Route::get('customer/order/refund/{id}', 'Customer\OrderController@refundOrder');


#通知
Route::post('customer/order/notify/pay', 'Customer\NotifyController@payResult');
Route::post('customer/order/notify/refund', 'Customer\NotifyController@refundResult');



