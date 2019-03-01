<?php

# 商户信息
Route::resource('business', 'Business\BusinessController');

# 区域模版
Route::resource('district',  'Business\DistrictController');

# 活动信息
Route::resource('promotion', 'Business\PromotionController');

# 商品信息
Route::resource('product', 'Business\ProductController');


