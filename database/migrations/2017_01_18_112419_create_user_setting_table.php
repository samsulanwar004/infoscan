<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserSettingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchant_users_report_setting', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('report_template_id');
            $table->unsignedInteger('merchant_user_id');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('report_template_id', 'FK_report_template_id_in_murs')
                  ->references('id')
                  ->on('report_templates');
            $table->foreign('merchant_user_id', 'FK_merchant_user_id_in_murs')
                  ->references('merchant_id')
                  ->on('merchant_users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('merchant_users_report_setting');
    }
}
