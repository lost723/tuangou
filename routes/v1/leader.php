<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/22
 * Time: 17:04
 */

## 小程序端
# 团长注册
Route::post('customer/leader/register', 'Customer\LeaderController@register');
# 团长活动商品列表
Route::get('customer/leader/promotion', 'Customer\LeaderPromotionController@getPromotions');
# 团长挑选活动
Route::post('customer/leader/add/promotion', 'Customer\LeaderPromotionController@addPromotions');
# 团长签收记录(记录列表/单个记录详情)
Route::get('customer/leader/record/promotion', 'Customer\LeaderPromotionController@getReceivedPromotions');
# 团长核销





## 后台



