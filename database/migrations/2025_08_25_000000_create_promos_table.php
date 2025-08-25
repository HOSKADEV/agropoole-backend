<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('promos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_id')->constrained('stocks')->cascadeOnDelete();
            $table->unsignedInteger('target_quantity');
            $table->double('new_price');
            $table->timestamps();
            $table->softDeletes();

            $table->unique('stock_id'); // ensure only one promo per stock
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promos');
    }
};
