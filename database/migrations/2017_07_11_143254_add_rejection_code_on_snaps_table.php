<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRejectionCodeOnSnapsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('snaps', function (Blueprint $table) {
            $table->string('rejection_code')
                ->after('reject_by')
                ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('snaps', function (Blueprint $table) {
            $table->dropColumn('rejection_code');
        });
    }
}
