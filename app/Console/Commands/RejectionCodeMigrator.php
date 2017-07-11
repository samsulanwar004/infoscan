<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use DB;
use App\Snap;
use App\Setting;

class RejectionCodeMigrator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'snaps-rejection-code:migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate command for data integrity of rejection code on snaps table and settings table';

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

        // show warning
        $this->error('Warning: Running this command will change data on \'settings\' table and \'snaps\' table. ' .
            'Please make sure you have a database backup before running this command, because this command ' .
            'doesn\'t has rollback functionality');

        $continue = $this->choice('Are you sure you want to continue ?', [
            'Y' => 'Yes',
            'N' => 'No'
        ], 'N');

        DB::beginTransaction();
        try {
            $this->settingsTableMigration();
            $this->snapsTableMigration();
            DB::commit();
            $this->info('Migration finish.');
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function settingsTableMigration()
    {
        $this->info('Migrating rejection code data on settings table.');

        foreach ($this->reasons()  as $reason) {
            $setting = Setting::where('setting_group', '=', 'snap_reason')
                ->where('setting_value', '=', $reason)->first();

            if (!$setting) {
                $this->info('Inserting rejection code: ' . $reason);
                $setting = new Setting();
                $setting->setting_group = 'snap_reason';
                $setting->setting_display_name = 'Snap Reason';
                $setting->setting_value = trim($reason);
                $setting->setting_type = 'toggle';
                $setting->is_visible = 1;
                $setting->created_by = 1;
                $setting->save();

                // generate rejection_code
                $setting->setting_name = 'R' . str_pad($setting->id, 3, '0', STR_PAD_LEFT);
                $setting->save();
            } elseif($setting->setting_name == 'snap reason') {
                $this->info('Updating rejection code: ' . $reason);
                $setting->setting_name = 'R' . str_pad($setting->id, 3, '0', STR_PAD_LEFT);
                $setting->save();
            }
        }
    }

    private function snapsTableMigration()
    {
        $this->info('Migrating rejection code on snaps table');

        foreach ($this->reasons() as $reason) {

            $setting = Setting::where('setting_group', '=', 'snap_reason')
                ->where('setting_value', '=', $reason)->first();

            if ($setting) {
                $this->info('Updating rejection code: "' . $reason . '" for snaps');

                $updated = DB::table('snaps')
                    ->where('status', '=', 'reject')
                    ->where('comment', 'LIKE', '%' . $reason)
                    ->update(['rejection_code' => $setting->setting_name]);

                $this->info($updated . ' snaps updated.');
            }
        }
    }

    /**
     * Get reasons from snaps table
     */
    private function reasons()
    {
        $reasons = Snap::where('status', '=', 'reject')
            ->where('comment', 'LIKE', '%Alasan :%')
            ->whereNotNull('comment')
            ->groupBy('comment')
            ->get();

        $sanitizedReasons = [];

        // sanitizing data because comment field contains string like this: "Sayang sekali, transaksi kamu gagal. Ayo coba lagi! \n Alasan :Tidak menyebutkan harga produk"
        // we don't need the "Sayang sekali, transaksi kamu gagal. Ayo coba lagi! \n Alasan :Tidak menyebutkan harga produk" string
        foreach($reasons->pluck('comment') as $reason) {
            $reason = explode('Alasan :', $reason);

            if (!empty($reason[1])) {
                $sanitizedReasons[] = $reason[1];
            }

        }

        return $sanitizedReasons;

    }
}
