<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUserPromoCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_promo_codes', function (Blueprint $table) {
            $table->dropColumn('times_used');  // Xóa cột times_used
            $table->dropColumn('limit');      // Xóa cột limit
            $table->boolean('is_used')->default(false);  // Thêm cột is_used
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_promo_codes', function (Blueprint $table) {
            $table->integer('times_used')->default(0);  // Thêm lại cột times_used
            $table->integer('limit')->nullable();      // Thêm lại cột limit
            $table->dropColumn('is_used');             // Xóa cột is_used
        });
    }
}
