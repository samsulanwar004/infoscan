<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMobileRequestLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mobile_request_logs', function(Blueprint $t) {
            $t->increments('id');
            $t->string('rc', 10)->index();
            $t->string('method', 6)->index(); // http method
            $t->string('type', 8)->index(); // request, response
            $t->text('content')->nullable();
            $t->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mobile_request_logs');
    }
}
