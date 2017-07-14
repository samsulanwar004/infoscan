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
                $snapService->saveLocationSnap($snap->id, $snap->latitude, $snap->longitude);
            }
        }

    }
}
