<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Services\CrowdsourceService;
use App\Snap;

class ForcedAssignProcessJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    private $snapId;
    private $csId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($snapId, $csId)
    {
        $this->snapId = $snapId;
        $this->csId = $csId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $snap = $this->getSnapById($this->snapId);
        $snap->user_id = $this->csId;
        $snap->update();
    }

    private function getSnapById($id)
    {
        return Snap::where('id', $id)
            ->first();
    }
}
