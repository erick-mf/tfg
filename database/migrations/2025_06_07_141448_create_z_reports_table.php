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
        Schema::create('z_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->timestamp('start_date');
            $table->timestamp('end_date');
            $table->decimal('total_revenue', 10, 2);
            $table->decimal('expected_cash', 10, 2);
            $table->decimal('counted_cash', 10, 2);
            $table->decimal('cash_difference', 10, 2);
            $table->decimal('total_card', 10, 2);
            $table->integer('total_orders');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('z_reports');
    }
};
