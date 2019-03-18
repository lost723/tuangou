<?php

# 商户信息
Route::resource('business', 'Business\BusinessController');

# 区域模版
Route::resource('district',  'Business\DistrictController');
Route::post('district/communitys/{id}',  'Business\DistrictController@updateItems');
Route::get('district/communitys/{id}',  'Business\DistrictController@getCommunitys');



# 活动信息
Route::resource('promotion', 'Business\PromotionController');

# 商品信息
Route::resource('product', 'Business\ProductController');
Route::put('product/active/{id}',  'Business\ProductController@active');
Route::put('product/disable/{id}',  'Business\ProductController@disable');



