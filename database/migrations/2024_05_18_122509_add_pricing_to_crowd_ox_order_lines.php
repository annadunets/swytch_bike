<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('crowd_ox_order_lines', function (Blueprint $table) {
          $table->integer('crowd_ox_line_type')->nullable();
          $table->integer('crowd_ox_price_product')->nullable();
          $table->integer('crowd_ox_price_shipping')->nullable();
          $table->integer('crowd_ox_price_total')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('crowd_ox_order_lines', function (Blueprint $table) {
          $table->dropColumn([
            'crowd_ox_line_type',
            'crowd_ox_price_product',
            'crowd_ox_price_shipping',
            'crowd_ox_price_total'
          ]);
        });
    }
};
