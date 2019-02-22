<?php

# Customer
Route::get('customer/me', 'Auth\CustomerController@me');
Route::post('customer/login', 'Auth\CustomerController@login');
Route::get('customer/refresh', 'Auth\CustomerController@refresh');
Route::post('customer/register', 'Auth\CustomerController@register');

# Distributor
Route::get('distributor/me', 'Auth\DistributorController@me');
Route::post('distributor/login', 'Auth\DistributorController@login');
Route::post('distributor/logout', 'Auth\DistributorController@logout');
Route::get('distributor/refresh', 'Auth\DistributorController@refresh');
Route::put('distributor/password', 'Auth\DistributorController@password');
Route::post('distributor/register', 'Auth\DistributorController@register');
Route::post('distributor/find/password', 'Auth\DistributorController@findPassword');
# 获取手机验证码
Route::post('distributor/getverifycode', 'Auth\DistributorController@getVerifycode');

# Trader
Route::get('trader/me', 'Auth\TraderController@me');
Route::post('trader/login', 'Auth\TraderController@login');
Route::post('trader/logout', 'Auth\TraderController@logout');
Route::get('trader/refresh', 'Auth\TraderController@refresh');
Route::put('trader/password', 'Auth\TraderController@password');
Route::post('trader/register', 'Auth\TraderController@register');
Route::post('trader/find/password', 'Auth\TraderController@findPassword');







# 文件上传
Route::post('upload/public/image','Common\QiNiuUploadController@uploadPublicImg');
Route::post('upload/private/image','Common\QiNiuUploadController@uploadPrivateImg');
