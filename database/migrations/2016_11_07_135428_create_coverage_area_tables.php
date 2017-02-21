<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCoverageAreaTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('provinces', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->char('id', 2);
            $table->string('name', 255);

            $table->primary('id');
        });

        Schema::create('regencies', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->char('id', 4);
            $table->char('province_id', 2);
            $table->string('name', 255);

            $table->primary('id');

            $table->foreign('province_id', 'FK_regencies_province_id_index')
                ->references('id')->on('provinces')
                ->onDelete('NO ACTION')
                ->onUpdate('NO ACTION');
        });

        Schema::create('districts', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->char('id', 7);
            $table->char('regency_id', 4);
            $table->string('name', 255);

            $table->primary('id');

            $table->foreign('regency_id', 'FK_districts_id_index')
                ->references('id')->on('regencies')
                ->onDelete('NO ACTION')
                ->onUpdate('NO ACTION');
        });

        Schema::create('villages', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->char('id', 10);
            $table->char('district_id', 7);
            $table->string('name', 255);

            $table->primary('id');

            $table->foreign('district_id', 'FK_villages_district_id_index')
                ->references('id')->on('districts')
                ->onDelete('NO ACTION')
                ->onUpdate('NO ACTION');
        });

        Schema::create('coverage_areas', function (Blueprint $t) {
            $t->increments('id');
            $t->string('name');
            $t->unsignedInteger('created_by');
            $t->boolean('is_active')->default(1);
            $t->softDeletes();
            $t->timestamps();

            $t->foreign('created_by', 'FK_user_id_on_coverage_areas')
                ->references('id')
                ->on('users')
                ->onUpdate('NO ACTION')
                ->onDelete('NO ACTION');
        });

        Schema::create('coverage_area_details', function (Blueprint $t) {
            $t->increments('id');
            $t->unsignedInteger('coverage_area_id');

            // if user doesn't select a specific district
            // then copy all of districts (by selected regency)
            $t->char('district_id', 7);
            $t->softDeletes();
            $t->timestamps();

            $t->foreign('district_id', 'FK_district_id_on_coverage_area_details')
                ->references('id')->on('districts')
                ->onUpdate('NO ACTION')
                ->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('villages');
        Schema::dropIfExists('districts');
        Schema::dropIfExists('regencies');
        Schema::dropIfExists('provinces');
    }
}
