<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('members', function (Blueprint $t) {
            $t->increments('id');
            $t->string('member_code', 10)->unique();
            $t->string('name', 200)->index();
            $t->string('email', 150)->index();
            $t->string('password', 150)->nullable();
            $t->string('gender', 1)->index();
            $t->string('avatar')->nullable();
            $t->string('status', 10)->index()->default('active');
            $t->boolean('is_login_by_social_media')->default(0);
            $t->string('social_media_url')->nullable();
            $t->string('social_media_type')->nullable();
            $t->date('dob')->index()->nullable();
            $t->integer('monthly_expense')->index()->default(0);
            $t->tinyInteger('person_in_house', false, true)->index()->default(1);
            $t->string('city', 200)->nullable();
            $t->string('occupation', 100)->nullable();
            $t->string('last_education', 5)->index()->nullable();
            $t->string('api_token', 60)->unique();
            $t->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('members');
    }
}
