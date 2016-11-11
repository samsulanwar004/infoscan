<?php

use Illuminate\Database\Seeder;

class CreatePromotionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = auth()->loginUsingId(1);
        $faker = \Faker\Factory::create();
        $p = new \App\Promotion();
        $p->title = 'New Promo in 2016';
        $p->description = $faker->realText();
        $p->start_at = \Carbon\Carbon::now();
        $p->end_at = \Carbon\Carbon::today()->addDay(3);
        $p->created_by = $user->email;
        $p->save();
    }
}
