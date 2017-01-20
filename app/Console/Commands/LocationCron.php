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
            $s = $snapService->getSnapByid($snap->id);
            $s->location = $location->results[0]->formatted_address;
            $s->outlet_city = $location->results[0]->address_components[6]->long_name;
            $s->outlet_province = $location->results[0]->address_components[7]->long_name;
            $s->outlet_zip_code = $location->results[0]->address_components[9]->long_name;
            $s->update();
        }

    }
}
