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
        Schema::create('members', function (Blueprint $table) {
            $table->increments('id');
            $table->string('member_code', 10)->unique();
            $table->string('name', 200)->index();
            $table->string('email', 150)->index();
            $table->string('password', 150)->nullable();
            $table->string('gender', 1)->index();
            $table->string('avatar')->nullable();
            $table->string('status', 10)->index()->default('active');
            $table->boolean('is_login_by_social_media')->default(0);
            $table->string('social_media_url')->nullable();
            $table->string('social_media_type')->nullable();
            $table->string('api_token', 60)->unique();
            $table->timestamps();
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
