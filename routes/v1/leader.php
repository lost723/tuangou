<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/22
 * Time: 17:04
 */

# 团长注册
Route::post('leader/register', 'Common\LeaderController@register');
Route::resource('leader', 'Common\LeaderController');




# 团长活动相关路由
# 团长活动商品列表
Route::get('customer/leader/promotions', 'Customer\LeaderPromotionController@getPromotions');
# 团长选货商品活动详情
Route::get('customer/leader/promotion/{id}', 'Customer\LeaderPromotionController@getPromotion');
# 团长挑选活动
Route::post('customer/leader/add/promotion', 'Customer\LeaderPromotionController@addPromotions');
# 团长签收记录(记录列表/单个记录详情)
Route::get('customer/leader/record/promotion', 'Customer\LeaderPromotionController@getReceivedPromotions');
Route::resource('leader/promotion', 'Customer\LeaderPromotionController');







