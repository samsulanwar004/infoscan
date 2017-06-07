<?php

namespace App\Console\Commands;

use App\Jobs\PointCalculation;
use App\Services\SnapService;
use App\Transaction;
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
    protected $signature = 'gojago:point:model {--exportxls} {--clear-transaction} {--cleardb} {--update-point}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export current snap transaction point into excel';

    /**
     * @var array
     */
    private $results;

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
        $this->truncatePointModelTable();
        $this->warn('Clear point modeling table.');

        // truncate transaction table
        if ($this->option('clear-transaction')) {
            $this->truncateTransactionsTable();
        }

        $snap = $this->getApprovedSnap();
        if (empty($snap)) {
            exit();
        }

        $today = Carbon::now()->format('Y-m-d H:i:s');
        $result = [];
        foreach ($snap as $s) {
            $func = sprintf('%s%s%s', 'get', ucfirst($s->snap_type), 'CategoryID');
            $category = $this->$func($s->id, $s->mode_type);

            $result[] = [
                'snap_code' => $s->request_code,
                'snap_id' => $s->snap_id,
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
                'member_code' => $s->member_code,
            ];
        }

        if (count($result) > 0) {
            $this->results = $result;

            $this->insertToTable($result);
            $this->info('Success insert into point modeling table.');

            // export point_modeling table to excel
            if ($this->option('exportxls')) {
                $modeling = $this->getPointModeling();
                if (empty($modeling)) {
                    exit();
                }

                $this->exportToExcel($modeling);
                $this->info('Success Export to xslx.');
            }

            $this->updateNewMemberPoint();

            $this->truncatePointModelTable();
            exit();
        }

        $this->error('There is no data to process.');
        exit();
    }

    private function getApprovedSnap()
    {
        $sql = "select m.member_code, m.email, s.id as snap_id, s.request_code, s.fixed_point, s.created_at, sf.id, sf.file_code, s.snap_type, sf.file_path, sf.mode_type, sf.image_point, sf.image_point
from snap_files as sf
inner join snaps as s on s.id = sf.snap_id
inner join members as m on m.id = s.member_id
where s.snap_type in ('handWritten', 'generalTrade', 'receipt') and
      s.status = 'approve'
order by s.snap_type, s.request_code, s.created_at asc
;";

        return DB::select($sql);
    }

    private function getSnapTags($snapFileId)
    {
        return DB::Select('Select id, current_signature, edited_signature from snap_tags where snap_file_id = :fileId',
            ['fileId' => $snapFileId]);
    }

    private function getHandWrittenCategoryID($snapFileId, $mode)
    {
        $tags = $this->getSnapTags($snapFileId);

        if (empty($tags)) {
            return 5;
        }

        foreach ($tags as $tag) {
            $current = trim(strtolower($tag->current_signature));

            switch ($mode) {
                case 'input':
                    if (null !== $tag->edited_signature
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

        if (empty($tags) || 'no_mode' === $mode) {
            return 10;
        }

        foreach ($tags as $tag) {
            $current = trim(strtolower($tag->current_signature));

            switch ($mode) {
                case 'tags':
                    if (null !== $tag->edited_signature
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
        if (!is_array($data)) {
            $data = collect($data)->toArray();
        }

        $data = collect($data)->map(function ($entry) {
            return [
                'Member' => $entry['email'],
                'TRX ID' => strtoupper($entry['snap_file_code']),
                'Snap Date' => $entry['snapped_at'],
                'Transaction Description' => sprintf('Snap Type: %s with %s mode.', strtoupper($entry['snap_type']),
                    strtoupper($entry['mode_type'])),
                'Category' => $entry['category'],
            ];
        });

        $filename = 'PointModeling-' . date('YmdHis');
        Excel::create($filename, function ($excel) use ($data) {
            $excel->sheet('Model', function ($sheet) use ($data) {
                $sheet->fromArray($data);
            });
        })->store('xlsx');
    }

    private function getPointModeling()
    {
        DB::setFetchMode(\PDO::FETCH_ASSOC);

        return DB::select('Select * From point_modeling;');
    }

    private function truncateTransactionsTable()
    {
        try {
            DB::select('truncate table transaction_detail;');
            $this->info('clear transaction detail table.');

            DB::delete('delete from transactions;');
            $this->info('clear transaction table.');

            DB::select('ALTER TABLE transactions AUTO_INCREMENT = 1;');
            $this->info('reset auto increment start value.');
        } catch (\Exception $e) {
            logger($e->getMessage());
        }
    }

    private function truncatePointModelTable()
    {
        if ($this->option('cleardb')) {
            DB::table('point_modeling')->truncate();
            $this->info('Success clear point model table.');
        }
    }

    private function updateNewMemberPoint()
    {
        if (!$this->option('update-point')) {
            return false;
        }

        try {
            $results = $this->remapMemberPoint();
            $pointCalculation = new PointCalculation;

            foreach ($results as $key => $result) {
                $result = collect($result)->groupBy('snap_code');

                $currentPoint = 0;
                $fixedPoint = 0;
                $memberIncrement = 0;
                foreach ($result as $requestCode => $snap) {
                    foreach ($snap as $item) {
                        // snap file
                        DB::update(
                            'Update snap_files set image_point = :point where id = :id',
                            ['id' => $item['snap_file_id'], 'point' => $item['new_point']]
                        );

                        $currentPoint = 0 === $memberIncrement ? 0 : $currentPoint + $item['new_point'];
                        $fixedPoint = $snap->sum('new_point');
                    }

                    // update snap
                    // TODO: need update current_exchange_rate value and current_total_money value
                    /*DB::update(
                        'Update snaps set fixed_point = :fixed_point, current_point_member=:current_point  where request_code = :req',
                        ['fixed_point' => $fixedPoint, 'current_point' => $currentPoint, 'req' => $requestCode]
                    );*/

                    // create new transcation
                    $firstSnap = $snap->first();
                    $memberCode = $firstSnap['member_code'];
                    $snapId = $firstSnap['snap_id'];
                    $this->createNewTransaction($memberCode, $snapId);

                    // update member Total Temporary Point
                    /* DB::update(
                         'Update members set temporary_point = :current_point where email=:email',
                         ['email' => $key, 'current_point' => $currentPoint]
                     );*/


                    $pointCalculation
                        ->initialize(
                            (new SnapService)->getSnapByCode($requestCode),
                            $fixedPoint,
                            null
                        )->setIsSendNotification(false)
                        ->handle();

                    /*$job = (new PointCalculation((new SnapService)->getSnapByCode($requestCode), $fixedPoint, null))->onQueue($config)->onConnection(env('INFOSCAN_QUEUE'));
                    dispatch($job);*/

                    ++$memberIncrement;
                }
            }

            $this->info('Success update image point.');
        } catch (\Exception $e) {
            logger($e->getMessage() . ' File: ' . $e->getFile() . ' Line:' . $e->getLine());

            throw new \Exception();
        }
    }

    private function remapMemberPoint()
    {
        $results = collect($this->results)->sortBy(function ($entry, $key) {
            return $entry['snapped_at'];
        }, SORT_NUMERIC);

        // manually groupping by email
        $snaps = [];
        foreach ($results as $r) {
            $email = $r['email'];
            $snaps[$email][] = [
                'snap_id' => $r['snap_id'],
                'snap_code' => $r['snap_code'],
                'snap_file_id' => $r['snap_file_id'],
                'new_point' => $r['new_point'],
                'snapped_at' => $r['snapped_at'],
                'email' => $r['email'],
                'member_code' => $r['member_code'],
            ];
        }

        return $snaps;
    }

    private function createNewTransaction($memberCode, $snapId)
    {
        $t = new Transaction;
        $t->transaction_code = strtolower(str_random(10));
        $t->member_code = $memberCode;
        $t->transaction_type = '1';
        $t->snap_id = $snapId;
        $t->save();
    }

}
