<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\HistoryMemberTransaction;
use App\Services\PointService;

class HistoryMemberTransactionJob implements ShouldQueue
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
    public function handle(HistoryMemberTransaction $history)
    {
        if ($this->data['action'] == 'snap') {
            if ($this->data['status'] == 'approve') {
                $point = (new PointService)->calculateApprovePoint($this->data['data']);
                $title = $this->getType($this->data['data']->snap_type);
                $description = $this->getWording($this->data['status'], $point);                
            } else {
                $title = $this->getType($this->data['data']->snap_type);
                $description = $this->getWording($this->data['status']); 
            }
            $history->member_id = $this->data['data']->member_id;
            $history->title = $title;
            $history->description = $description;
            $history->save();
        }
        
    }

    private function getWording($status, $point = null)
    {
        if ($status == 'approve') {
            return 'Selamat, klaim sebesar '.$point.' poin telah berhasil! Kluk!';
        } else {
            return 'Sayang sekali, transaksi kamu gagal. Ayo coba lagi!';
        }
    }

    private function getType($type)
    {
        if ($type == 'handWritten') {
            return 'Nota Tulis';
        } else if ($type == 'generalTrade') {
            return 'Warung';
        } else if ($type == 'receipt') {
            return 'Struk';
        }
    }
}
