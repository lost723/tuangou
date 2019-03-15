<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    $str = "2019-03-15T03:48:07.764Z";
    $time = strtotime($str);
    $date = date('Y-m-d H:i:s', $time);
    var_dump($str);
    var_dump($time);
    var_dump($date);
});
