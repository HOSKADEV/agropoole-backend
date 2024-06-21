<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
          $table->dropConstrainedForeignId('user_id');
          $table->dropConstrainedForeignId('cart_id');
          $table->unsignedBigInteger('buyer_id')->default(5)->after('id');
          $table->foreign('buyer_id')->references('id')->on('users');
          $table->unsignedBigInteger('seller_id')->default(4)->after('id');
          $table->foreign('seller_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            //
        });
    }
};
