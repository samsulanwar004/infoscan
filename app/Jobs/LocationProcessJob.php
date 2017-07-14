<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Services\SnapService;

class LocationProcessJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    private $snapId;

    private $longitude;

    private $latitude;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($snapId, $latitude, $longitude)
    {
        $this->snapId = $snapId;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->latitude != 0 && $this->longitude != 0 ) {
            (new SnapService)->saveLocationSnap($this->snapId, $this->latitude, $this->longitude);
        }
    }
}
