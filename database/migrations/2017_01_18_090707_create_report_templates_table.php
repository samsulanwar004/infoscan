<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReportTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_templates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 10);
            $table->text('content');
            $table->unsignedInteger('created_by');
            $table->unsignedInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('created_by', 'FK_created_by_in_report_templates')
                  ->references('id')->on('users')
                  ->onDelete('no action')
                  ->onUpdate('no action');

            $table->foreign('updated_by', 'FK_updated_by_in_report_templates')
                  ->references('id')->on('users')
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
         Schema::dropIfExists('report_templates');
    }
}
