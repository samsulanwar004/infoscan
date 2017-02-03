<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Services\PointService;
use App\Services\SnapService;

class PointCalculation implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        logger(serialize($this->data));
        $requestCode = $this->data['request_code'];
        $memberId = $this->data['member_id'];
        $type = $this->data['type'];
        $mode = $this->data['mode'];
        $files = $this->data['files'];

        $calculate = (new PointService)->calculatePointSnap($memberId, $type, $mode);
        $point = ($calculate != null) ? $calculate->point : '0';
        $total = $point * $files;
        $snap = (new SnapService)->getSnapByCode($requestCode);
        $snap->estimated_point = $total;
        $snap->update();

    }
}
