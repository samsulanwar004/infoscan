<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\MemberActivity;

class MemberLog implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;
    
    private $data;
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
    public function handle(MemberActivity $memberActivity)
    {
        $memberActivity->member_code = $this->data->member;
        $memberActivity->action_in = $this->data->action;
        $memberActivity->description = $this->data->description;
        $memberActivity->save();

    }
}
