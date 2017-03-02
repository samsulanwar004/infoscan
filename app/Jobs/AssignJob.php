<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Services\CrowdsourceService;
use App\Services\SnapService;
use App\Snap;

class AssignJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $crowdsources = (new CrowdsourceService)->getCrowdsourceToAssign();
        $snaps = (new SnapService)->getSnapToAssign();
        $countOfCs = count($crowdsources);
        $countOfSnap = count($snaps);

        if ($countOfSnap >= $countOfCs) {
            for ($i=0; $i < $countOfCs; $i++) { 
                $snap = Snap::where('id', $snaps[$i]['id'])->first();
                $snap->user_id = $crowdsources[$i]->id;
                $snap->save();
            }
        }
    }
}
