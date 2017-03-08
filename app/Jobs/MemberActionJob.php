<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\MemberActionLog;
use App\Services\PointService;

class MemberActionJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    private $memberId;

    private $data;

    private $group;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($memberId, $group, $data)
    {
        $this->group = $group;
        $this->memberId = $memberId;
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(MemberActionLog $history)
    {
        $history->member_id = $this->memberId;
        $history->group = $this->group;
        $history->content = $this->data;
        $history->save();
        
    }
    
}
