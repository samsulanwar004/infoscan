<?php
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model; 
use Faker\Factory as Faker;
    
class CreateReportsSeeder extends Seeder {
 
    public function run() {
        $faker = Faker::create();
        $id = 1;
        foreach (range(1,1000) as $index) {
            $pro = array('coca cola','chitos','aqua','tempe','tahu','nestle','sprite');
            $c = array('Jakarta Selatan','Jakarta Utara','Jakarta Barat','Jakarta Pusat','Jakarta Timur');
            $ot = array('Cirkle K','Indomaret','Alfa Maret','Seven Eleven');
            $oc = array('Progammer','Desainer','Pengusaha','Bakulan');
            $occupation = array_rand($oc);
            $product = array_rand($pro);
            $outlet = array_rand($ot);
            $city = array_rand($c);
            $s = array('c','b','a');
            $p = array('1','2','3','4');            
            $sec = array_rand($s);
            $pih = array_rand($p);
            $gen = array('male','female');
            $gender = array_rand($gen);
            $le = array('sd','smp','sma','s1');
            $last = array_rand($le);
            DB::table('reports')->insert([
                'user_id' => $id,
                'occupation' => $oc[$occupation],
                'person_in_house' => $p[$pih],
                'last_education' => $le[$last],
                'receipt_number' => str_random(10),
                'outlet_province' => 'DKI Jakarta',
                'outlet_city' => $c[$city],
                'brand' => null,
                'quantity' => rand(1,10),
                'total_price_quantity' => rand(100,100000),
                'grand_total_price' => rand(1000,1000000),
                'outlet_name' => $ot[$outlet],
                'products' => $pro[$product],
                'users_city' => $c[$city],
                'age' => rand(20,40),
                'outlet_address' => $faker->address,
                'province' => 'DKI Jakarta',
                'gender' => $gen[$gender],
                'usership' => null,
                'sec' => $s[$sec],
                'outlet_type' => null,
                'longitude' => $faker->longitude($min = -180, $max = 180),
                'latitude' => $faker->latitude($min = -90, $max = 90),
                'snap_at' => $faker->dateTimeThisMonth($max = 'now', $timezone = date_default_timezone_get()),
                'created_at' => $faker->dateTimeThisMonth($max = 'now', $timezone = date_default_timezone_get()),
                'updated_at' => $faker->dateTimeThisMonth($max = 'now', $timezone = date_default_timezone_get()),
                'purchase_date' => $faker->dateTimeThisMonth($max = 'now', $timezone = date_default_timezone_get()),
                'sent_time' => $faker->dateTimeThisMonth($max = 'now', $timezone = date_default_timezone_get()),
            ]);

            $id++;
        }
    } 

}