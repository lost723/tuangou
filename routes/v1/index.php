<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/19
 * Time: 17:22
 */

# 街道相关路由
# 坐标定位城市
Route::post('road/city/my', 'Common\RoadController@myCity');
Route::get('road/city', 'Common\RoadController@listCity');
# 获取下级城市信息
Route::get('road/sub', 'Common\RoadController@getSubRoads');
Route::resource('road','Common\RoadController');




# 小区
Route::get('community/my', 'Common\CommunityController@myCommunity');
Route::post('community/relate', 'Common\CommunityController@relateCommunity');
Route::post('community/list', 'Common\CommunityController@CommunityList');
//Route::post('community/search', 'Common\CommunityController@searchCommunity'); # 作废
Route::resource('community', 'Common\CommunityController');



# 文件上传
Route::post('upload/public/image','Common\QiNiuUploadController@uploadPublicImg');
Route::post('upload/private/image','Common\QiNiuUploadController@uploadPrivateImg');



# 小程序接口相关
# 手机号解析
Route::post('system/parse/mobile', 'Auth\CustomerController@parseMobile');
Route::get('system/qr', 'Customer\SystemController@QRcode');

# 微信事件监听

Route::post('test', 'Log\PayLog@test');
Route::get('testtransfer', 'Common\TestTraderPayTransafer@doTransfer');


