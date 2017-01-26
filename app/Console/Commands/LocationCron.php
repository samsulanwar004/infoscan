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
            $location = $snapService->handleMapAddress($snap->latitude, $snap->longitude);
            if ($location->status == "OK") {
                $address = $location->results[0]->address_components;
                $length = count($address);
                $s = $snapService->getSnapByid($snap->id);
                $s->location = $location->results[0]->formatted_address;
                $s->outlet_city = in_array("administrative_area_level_2", $address[$length-4]->types) ? $address[$length-4]->long_name : null;
                $s->outlet_province = in_array("administrative_area_level_1", $address[$length-3]->types) ? $address[$length-3]->long_name : null;
                $s->outlet_zip_code = in_array("postal_code", $address[$length-1]->types) ? $address[$length-1]->long_name : null;
                $s->update();
            }
            
        }

    }
}
