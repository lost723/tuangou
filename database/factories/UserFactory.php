<?php

//use Faker\Generator as Faker;
use Faker\Factory;
/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

//$factory->define(App\User::class, function (Faker $faker) {
//    return [
//        'name' => $faker->name,
//        'email' => $faker->unique()->safeEmail,
//        'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
//        'remember_token' => str_random(10),
//    ];
//});

$faker = Faker\Factory::create('zh_CN');
//$factory->define(App\Models\Road::class, function () use ($faker) {
//    return [
//        'parentid'      =>  3,
//        'leveltype'     =>  4,
//        'name'          =>  $faker->region,
//        'path'          =>  '1,2,3,4',
//        'province'      =>  '山东省',
//        'city'          =>  '青岛市',
//        'district'      =>  '城阳区',
////        'created_at'    =>  $faker->created_at,
////        'updated_at'    =>  $faker->updated_at,
//    ];
//});


$factory->define(App\Models\Community::class, function() use ($faker) {
   return [
       'name'          =>  $faker->area,
       'road_id'       =>  ((rand()%4)+6),
       'address'       =>   $faker->address,
       'longitude'    =>   $faker->longitude,
       'latitude'      =>   $faker->latitude,

   ];
});
