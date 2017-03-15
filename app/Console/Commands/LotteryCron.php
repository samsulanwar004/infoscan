<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Services\LuckyDrawService;
use App\LuckyDrawWinner;
use App\Jobs\MemberActionJob;

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
        $date = Carbon::now('Asia/Jakarta')->toDateTimeString();

        $luckys = (new LuckyDrawService)->getEndDateLuckyDraw($date);

        foreach ($luckys as $lucky) {
            $memberLucky = $lucky->memberLuckyDraws;
            if (count($memberLucky) > 0) {
                $win = collect($memberLucky)->random();
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
                $memberWin = $win->member_id;
                //build notification for member win
                $content = [
                        'type' => 'luckydraw',
                        'title' => 'Menang',
                        'description' => 'Selamat! Kamu telah memenangkan undian '.$lucky->title.'. Staf kami akan menghubungimu',
                    ];
                    $config = config('common.queue_list.member_action_log');
                    $job = (new MemberActionJob($memberWin, 'notification', $content))->onQueue($config)->onConnection(env('INFOSCAN_QUEUE'));
                    dispatch($job);

                $memberLucky = $memberLucky->filter(function($value, $Key) use ($memberWin){
                    return $value->member_id != $memberWin;
                });

                foreach ($memberLucky as $member) {
                    //build notification for member lose
                    $content = [
                        'type' => 'luckydraw',
                        'title' => 'Kalah',
                        'description' => 'Maaf, kamu belum beruntung. Ayo coba keberuntunganmu di undian lainnya!',
                    ];
                    $config = config('common.queue_list.member_action_log');
                    $job = (new MemberActionJob($member->member_id, 'notification', $content))->onQueue($config)->onConnection(env('INFOSCAN_QUEUE'));
                    dispatch($job);
                }

            }
        }
        
    }
}
