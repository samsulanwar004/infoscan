<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Services\LuckyDrawService;
use App\LuckyDrawWinner;

class LotteryCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lottery:cron';

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
        $this->info('Lottery:Cron Command Run successfully');
        $date = Carbon::now();
        $luckys = (new LuckyDrawService)->getEndDateLuckyDraw($date);

        foreach ($luckys as $lucky) {
            $win = collect($lucky->memberLuckyDraws)->random();
            $lucky->is_active = 0;
            $lucky->update();

            $data = [
                'code' => $lucky->luckydraw_code,
                'title' => $lucky->title,
                'description' => $lucky->description,
                'point' => $lucky->point,
                'random_number' => $win->random_number,
            ];

            $ldw = new LuckyDrawWinner;
            $ldw->description = json_encode($data);
            $ldw->member()->associate($win->member_id);
            $ldw->luckydraw()->associate($lucky->id);           
            $ldw->save();
        }
        
    }
}
