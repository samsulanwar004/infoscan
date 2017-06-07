<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;

class ExportCurrentPoint extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gojago:member:point';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export member point to xls';

    /**
     * Create a new command instance.
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
        $data = $this->getCurrentMemberPoint()->map(function ($entry) {
            $point = $entry->temporary_point * 2.5;

            return [
                'Member Code' => $entry->member_code,
                'Name' => $entry->name,
                'Email' => $entry->email,
                'Current Point' => $entry->temporary_point,
                'Current Money' => (0 < $point) ? $point : 0,
            ];
        });

        $filename = 'ActiveUser-' . date('YmdHis');
        Excel::create($filename, function ($excel) use ($data) {
            $excel->sheet('Current Point & Money', function ($sheet) use ($data) {
                $sheet->fromArray($data);
            });
        })->store('xlsx');
    }

    private function getCurrentMemberPoint()
    {
        return \App\Member::orderBy('email', 'ASC')
                          ->get(['member_code', 'email', 'name', 'temporary_point']);
    }
}
