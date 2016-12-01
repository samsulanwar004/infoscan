<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSnapFilesTable extends Migration
{
    /**
     * Run the migrations.
     * @table snap_files
     *
     * @return void
     */
    public function up()
    {
        Schema::create('snap_tags', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('snap_file_id')->unsigned();
            $table->string('name', 255);
            $table->decimal('total_price', 15, 2)->default('0.00');
            $table->integer('quantity')->default('0');
            $table->timestamps();

            $table->foreign('snap_file_id', 'FK_snap_files_id_in_snap_tags')
                ->references('id')->on('snap_files')
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
       Schema::dropIfExists('snap_tags');
     }
}
