<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSnapTagsTable extends Migration
{
    /**
     * Run the migrations.
     * @table snap_tags
     *
     * @return void
     */
    public function up()
    {
        Schema::create('snap_files', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('file_code', 10)->index();
            $table->integer('snap_id')->unsigned();
            $table->string('file_path', 255);
            $table->string('file_mimes', 45)->nullable();
            $table->string('file_dimension', 12)->nullable(); // json string
            $table->string('mode_type', 10)->nullable()->default(NULL);
            $table->tinyInteger('is_need_recognition')->default('1');
            $table->text('recognition_text')->nullable();
            $table->integer('recognition_score')->default('0');
            $table->string('process_status', 15)->index();
            $table->decimal('total', 15,2)->default('0.00'); 
            $table->timestamps();

            $table->foreign('snap_id', 'FK_snap_id_in_snap_files')
                ->references('id')->on('snaps')
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
        Schema::dropIfExists('snap_files');
    }
}
