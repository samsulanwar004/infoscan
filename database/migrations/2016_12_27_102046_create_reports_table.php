<?php
    use Illuminate\Support\Facades\Schema;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Database\Migrations\Migration;

    class CreateReportsTable extends Migration {
        
        /**
        * Run the migrations.
        *
        * @return void
        */
        public function up() {
            Schema::create('reports', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->increments('id');
                $table->string('outlet_name', 100)->nullable();
                $table->string('products', 100)->nullable();
                $table->string('users_city', 100)->nullable();
                $table->string('age', 10)->nullable();
                $table->string('province', 100)->nullable();
                $table->string('gender', 10)->nullable();
                $table->string('usership', 100)->nullable();
                $table->string('sec', 100)->nullable();
                $table->string('outlet_type', 100)->nullable();
                $table->string('longitude', 100)->nullable();
                $table->string('latitude', 100)->nullable();
                $table->timestamp('snap_at');
                $table->timestamps();
            });
        }

        /**
        * Reverse the migrations.
        *
        * @return void
        */
        public function down() {
            Schema::dropIfExists('reports');
        }
    }