<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\CrowdsourceActivity;

class CrowdsourceLog implements ShouldQueue
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
    public function handle(CrowdsourceActivity $crowdsourceActivity)
    {
        $crowdsourceActivity->action_in = $this->data->action;
        $crowdsourceActivity->description = $this->data->description;
        $crowdsourceActivity->user()->associate($this->data->userId);
        $crowdsourceActivity->save();
    }
}
