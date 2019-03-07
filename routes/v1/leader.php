<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/22
 * Time: 17:04
 */

Route::post('leader/register', 'Common\LeaderController@register');
Route::resource('leader', 'Common\LeaderController');




# 团长活动
Route::get('leader/promotion/ownlist', 'Customer\LeaderPromotionController@getOwnPromotions');
Route::get('leader/promotion/choicelist', 'Customer\LeaderPromotionController@getChoicePromotions');
Route::get('leader/promotion/detail', 'Customer\LeaderPromotionController@getPromotiondetail');
Route::post('leader/promotion/add', 'Customer\LeaderPromotionController@addPromotions');



# 团长签收记录(记录列表/单个记录详情)
#Route::get('leader/promotion/records', 'Customer\LeaderPromotionController@getReceivedPromotions');
#Route::get('leader/promotion/records/detail', 'Customer\LeaderPromotionController@getReceivedPromotions');
# 团长活动相关路由
//Route::resource('leader/promotion', 'Customer\LeaderPromotionController');










