<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Services\SnapService;

class LocationCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'location:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $snapService = (new SnapService);
        $snaps = $snapService->getSnapByLocation();
        $this->info('Location:Cron Command Run successfully');

        foreach ($snaps as $snap) {    

            if ($snap->latitude != 0 && $snap->longitude != 0 ) {
                $location = $snapService->handleMapAddress($snap->latitude, $snap->longitude);
                if (strtolower($location->status) == "ok") {
                    $address = $location->results[0]->address_components;
                    $s = $snapService->getSnapByid($snap->id);
                    $s->location = $location->results[0]->formatted_address;
                    
                    $city = null;
                    $province = null;
                    $zipcode = null;
                    foreach ($address as $add) {

                        if (in_array("administrative_area_level_2", $add->types) == true) {
                            $city = $add->long_name;
                        }

                        if (in_array("administrative_area_level_1", $add->types) == true) {
                            $province = $add->long_name;
                        }

                        if (in_array("postal_code", $add->types) == true) {
                            $zipcode = $add->long_name;
                        }
                    }

                    $s->outlet_city = $city;
                    $s->outlet_province = $province;
                    $s->outlet_zip_code = $zipcode;
                    $s->update();
                }
            }
            
        }


    }
}
