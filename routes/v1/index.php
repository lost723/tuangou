<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/19
 * Time: 17:22
 */


# 坐标定位城市
Route::post('road/city/my', 'Common\RoadController@myCity');
# 获取城市列表
Route::post('road/city', 'Common\RoadController@listCity');
# 获取下级城市信息
Route::get('road/sub', 'Common\RoadController@getSubRoads');
Route::resource('road','Common\RoadController');




# 我的小区
Route::get('customer/my/community', 'Customer\CommunityController@myCommunity');
# 关联小区
Route::post('customer/relate/community', 'Customer\CommunityController@relateCommunity');
# 获取周边小区列表
Route::post('customer/list/community', 'Customer\CommunityController@CommunityList');
# 获取周边小区 通过腾讯api获取
Route::post('customer/search/community', 'Customer\CommunityController@searchCommunity');




# 文件上传
Route::post('upload/public/image','Common\QiNiuUploadController@uploadPublicImg');
Route::post('upload/private/image','Common\QiNiuUploadController@uploadPrivateImg');


