<?php

# 商户信息
Route::resource('business', 'Business\BusinessController');


# 商品信息
Route::resource('production', 'Business\ProductionController');



# 活动信息
Route::resource('promotion', 'Business\PromotionController');