<?php

namespace App\Jobs;

use App\Libraries\GoogleVision;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ExtractTextJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    private $data;

    private $type = 'images';

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $data, $type = 'images')
    {
        $this->data = $data;

        $this->type = $type;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
    }

    private function getImageProvider()
    {
        $key = config('google.vision.key');

        return new GoogleVision($key);
    }

    private function getAudioProvider()
    {
    }
}
