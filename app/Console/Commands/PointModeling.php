<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class PointModeling extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gojago:point:model {--export} {--exportdb}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export current snap transaction point into excel';

    private $categories = [
        1 => 500,
        2 => 750,
        3 => 1000,
        4 => 1500,
        5 => 300,
        6 => 350,
        7 => 500,
        8 => 315,
        9 => 350,
        10 => 200,
        11 => 250,
        12 => 400,
        13 => 250,
        14 => 275,
    ];

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
        // export point_modeling table to excel
        if($this->option('exportdb')) {
            $modeling = $this->getPointModeling();
            if(empty($modeling)) exit();

            $this->exportToExcel($modeling);
            $this->info('Success Export to xslx');
            exit();
        }

        $snap = $this->getApprovedSnap();
        if(empty($snap)) exit();

        $today = Carbon::now()->format('Y-m-d H:i:s');
        $result = [];
        foreach ($snap as $s) {
            $func = sprintf('%s%s%s', 'get', ucfirst($s->snap_type), 'CategoryID');
            $category = $this->$func($s->id, $s->mode_type);

            $result[] = [
                'snap_code' => $s->request_code,
                'snap_file_id' => $s->id,
                'snap_file_code' => $s->file_code,
                'snap_type' => $s->snap_type,
                'file_path' => $s->file_path,
                'mode_type' => $s->mode_type,
                'category' => $category,
                'new_point' => $this->categories[$category],
                'created_at' => $today,
                'snapped_at' => $s->created_at,
                'email' => $s->email,
            ];
        }

        if(count($result) > 0 ) {
            /*$this->hasOption('export')
                ? $this->exportToExcel()
                : $this->insertToTable($result);*/
            $this->insertToTable($result);

            $this->info('Success.');
            exit();
        }

        $this->error('There is no data to process.');
        exit();
    }

    private function getApprovedSnap()
    {
        $sql = "select m.email, s.request_code, s.fixed_point, s.created_at, sf.id, sf.file_code, s.snap_type, sf.file_path, sf.mode_type, sf.image_point, sf.image_point
from snap_files as sf
inner join snaps as s on s.id = sf.snap_id
inner join members as m on m.id = s.member_id
where s.snap_type in ('handWritten', 'generalTrade', 'receipt') and
      s.status = 'approve'
order by s.snap_type, s.request_code asc
;";

        return DB::select($sql);
    }

    private function getSnapTags($snapFileId)
    {
        return DB::Select('Select id, current_signature, edited_signature from snap_tags where snap_file_id = :fileId', ['fileId' => $snapFileId]);
    }

    private function getHandWrittenCategoryID($snapFileId, $mode)
    {
        $tags = $this->getSnapTags($snapFileId);

        if(empty($tags)) return 5;

        foreach ($tags as $tag) {
            $current = trim(strtolower($tag->current_signature));

            switch ($mode) {
                case 'input':
                    if(null !== $tag->edited_signature
                        && $current != trim(strtolower($tag->edited_signature))
                    ) {
                        return 6;
                    }

                    return 7;
                    break;

                case 'audios':
                    return 9;
                    break;
            }
        }
    }

    private function getGeneralTradeCategoryID($snapFileId, $mode)
    {
        $tags = $this->getSnapTags($snapFileId);

        if(empty($tags) || 'no_mode' === $mode) return 10;

        foreach ($tags as $tag) {
            $current = trim(strtolower($tag->current_signature));

            switch ($mode) {
                case 'tags':
                    if(null !== $tag->edited_signature
                        && $current != trim(strtolower($tag->edited_signature))
                    ) {
                        return 11;
                    }

                    return 12;
                    break;

                case 'audios':
                    return 14;
                    break;
            }
        }
    }

    public function getReceiptCategoryID($snapFileId, $mode)
    {
        return 1;
    }

    private function insertToTable(array $data)
    {
        return DB::table('point_modeling')->insert($data);
    }

    private function exportToExcel($data)
    {
        if(! is_array($data)) {
            $data = collect($data)->toArray();
        }

        $data = collect($data)->map(function($entry) {
            return [
                'Member' => $entry['email'],
                'TRX ID' => strtoupper($entry['snap_file_code']),
                'Snap Date' => $entry['snapped_at'],
                'Transaction Description' => sprintf('Snap Type: %s with %s mode.', strtoupper($entry['snap_type']), strtoupper($entry['mode_type'])),
                'Category' => $entry['category'],
            ];
        });

        Excel::create('Point Modeling', function($excel) use($data) {
            $excel->sheet('Sheetname', function($sheet) use($data) {
                $sheet->fromArray($data);
            });
        })->store('xlsx');
    }

    private function getPointModeling()
    {
        DB::setFetchMode(\PDO::FETCH_ASSOC);

        return DB::select('Select * From point_modeling;');
    }
}
