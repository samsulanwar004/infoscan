<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSnapsTable extends Migration
{
    /**
     * Run the migrations.
     * @table snaps
     *
     * @return void
     */
    public function up()
    {
        Schema::create('snaps', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('request_code', 10)->unique();
            $table->integer('member_id')->unsigned();
            $table->string('snap_type', 15);
            $table->string('mode_type', 10)->nullable();
            $table->string('status', 10)->default('new');
            $table->string('approved_by', 100)->nullable()->index();
            $table->string('reject_by', 100)->nullable()->index();
            $table->string('check_by', 100)->nullable()->index();
            $table->text('comment')->nullable();
            $table->string('receipt_id', 100)->nullable();
            $table->string('location', 255)->nullable();
            $table->datetime('purchase_time')->nullable(); 
            $table->string('outlet_name', 100)->nullable();
            $table->string('outlet_type', 100)->nullable();
            $table->string('outlet_city', 100)->nullable();
            $table->string('outlet_province', 100)->nullable();
            $table->string('outlet_zip_code', 6)->nullable();
            $table->decimal('total_value', 15, 2)->default('0.00');   
            $table->string('payment_method', 100)->nullable();  
            $table->float('estimated_point')->nullable();  
            $table->decimal('longitude', 11, 8)->nullable();
            $table->decimal('latitude', 11, 8)->nullable();
            $table->timestamps();

            $table->foreign('member_id', 'FK_member_id_in_snaps')
                  ->references('id')->on('members')
                  ->onDelete('no action')
                  ->onUpdate('no action');

        });
    }

    /**
    * Reverse the migrations.
    *
    * @return void
    */
    public function down()
    {
        Schema::dropIfExists('snaps');
    }
}
