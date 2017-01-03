<?php

    use Illuminate\Database\Seeder;
    
    class CreateReportsSeeder extends Seeder {
	   
        //protected $tables = ['reports' => 'reports'];

        protected $reports = [
            ['outlet_name' => 'Circle K', 'products' => 'Sari Roti', 'users_city' => 'Bekasi', 'age' => '30', 'outlet_area' => 'Tebet', 'province' => 'DKI Jakarta', 'gender' => 'Male', 'usership' => NULL, 'sec' => NULL, 'outlet_type' => 'Convenience Store', 'created_at' => '2017-01-03 01:01:01', 'updated_at' => NULL],
            ['outlet_name' => 'Circle K', 'products' => 'Equil', 'users_city' => 'Bekasi', 'age' => '30', 'outlet_area' => 'Tebet', 'province' => 'DKI Jakarta', 'gender' => 'Male', 'usership' => NULL, 'sec' => NULL, 'outlet_type' => 'Convenience Store', 'created_at' => '2017-01-03 01:01:01', 'updated_at' => NULL],
            ['outlet_name' => 'Circle K', 'products' => 'Sari Roti', 'users_city' => 'Bekasi', 'age' => '30', 'outlet_area' => 'Tebet', 'province' => 'DKI Jakarta', 'gender' => 'Male', 'usership' => NULL, 'sec' => NULL, 'outlet_type' => 'Convenience Store', 'created_at' => '2017-01-03 01:01:01', 'updated_at' => NULL],
            ['outlet_name' => 'Circle K', 'products' => 'Equil', 'users_city' => 'Bekasi', 'age' => '30', 'outlet_area' => 'Tebet', 'province' => 'DKI Jakarta', 'gender' => 'Male', 'usership' => NULL, 'sec' => NULL, 'outlet_type' => 'Convenience Store', 'created_at' => '2017-01-03 01:01:01', 'updated_at' => NULL],
            ['outlet_name' => 'Circle K', 'products' => 'Sari Roti', 'users_city' => 'Bekasi', 'age' => '30', 'outlet_area' => 'Tebet', 'province' => 'DKI Jakarta', 'gender' => 'Male', 'usership' => NULL, 'sec' => NULL, 'outlet_type' => 'Convenience Store', 'created_at' => '2017-01-03 01:01:01', 'updated_at' => NULL],
            ['outlet_name' => 'Circle K', 'products' => 'Equil', 'users_city' => 'Bekasi', 'age' => '30', 'outlet_area' => 'Tebet', 'province' => 'DKI Jakarta', 'gender' => 'Male', 'usership' => NULL, 'sec' => NULL, 'outlet_type' => 'Convenience Store', 'created_at' => '2017-01-03 01:01:01', 'updated_at' => NULL],
            ['outlet_name' => 'Circle K', 'products' => 'Sari Roti', 'users_city' => 'Bekasi', 'age' => '30', 'outlet_area' => 'Tebet', 'province' => 'DKI Jakarta', 'gender' => 'Male', 'usership' => NULL, 'sec' => NULL, 'outlet_type' => 'Convenience Store', 'created_at' => '2017-01-03 01:01:01', 'updated_at' => NULL],
            ['outlet_name' => 'Circle K', 'products' => 'Equil', 'users_city' => 'Bekasi', 'age' => '30', 'outlet_area' => 'Tebet', 'province' => 'DKI Jakarta', 'gender' => 'Male', 'usership' => NULL, 'sec' => NULL, 'outlet_type' => 'Convenience Store', 'created_at' => '2017-01-03 01:01:01', 'updated_at' => NULL],
        ];

        /**
         * Run the database seeds.
         *
         * @return void
        */
        public function run() {
            $this->seed('reports', $this->reports, 'Reports');
        }

    }