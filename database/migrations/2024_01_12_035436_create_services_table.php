<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id('service_id');
            $table->string('service_name');
            $table->text('description')->nullable();
            $table->integer('duration'); // Duration in minutes, adjust data type as needed
            $table->boolean('is_available')->default(true);
            $table->decimal('price_per_unit', 10, 2);
            $table->string('unit_type'); // Adjust data type as needed
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
        Schema::dropIfExists('services');
    }
}
