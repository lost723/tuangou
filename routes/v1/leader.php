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
Route::get('leader/promotion/detail', 'Customer\LeaderPromotionController@getPromotionDetail');
Route::get('leader/promotion/owndetail', 'Customer\LeaderPromotionController@getLeaderPromDetail');
Route::post('leader/promotion/add', 'Customer\LeaderPromotionController@addPromotions');
Route::post('leader/promotion/cancel', 'Customer\LeaderPromotionController@cancelPromotions');



# 团长验收
Route::get('leader/promotion/check/list', 'Customer\LeaderPromotionController@getCheckList');
Route::get('leader/promotion/check/detail', 'Customer\LeaderPromotionController@getcheckDetail');
Route::post('leader/promotion/check', 'Customer\LeaderPromotionController@doCheck');


# 团长核销
Route::get('leader/promotion/verify/list', 'Customer\LeaderPromotionController@getVerifyList');
Route::get('leader/promotion/verify/detail', 'Customer\LeaderPromotionController@getVerifyDetail');
Route::post('leader/promotion/verify', 'Customer\LeaderPromotionController@doVerify');









