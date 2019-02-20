<?php

use Illuminate\Database\Seeder;

class RoadTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        factory(App\Models\Road::class, 2)->create();

    }
}
