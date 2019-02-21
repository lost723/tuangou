<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/19
 * Time: 17:22
 */
# 关联小区
Route::post('customer/relate/community', 'Auth\CustomerController@relateCommunity');
# 我的小区
Route::get('customer/my/community', 'Auth\CustomerController@mycommunity');
# 坐标定位城市
Route::post('customer/my/city', 'Customer\RoadController@myCity');
Route::post('customer/my/location', 'common\WXLocationController@getLocation');

# 获取城市列表
Route::post('customer/list/city', 'Customer\RoadController@listCity');
# 获取周边小区列表
Route::post('customer/list/community', 'Customer\CommunityController@CommunityList');
Route::post('customer/list/community1', 'Customer\CommunityController@testResource');


# 团长注册
Route::post('customer/leader/register', 'Customer\LeaderController@register');


# 文件上传
Route::post('upload/public/image','Common\QiNiuUploadController@uploadPublicImg');
Route::post('upload/private/image','Common\QiNiuUploadController@uploadPrivateImg');


# 街道管理
Route::resource('trader/road','Trader\RoadController');
# 小区管理
Route::resource('trader/community','Trader\CommunityController');