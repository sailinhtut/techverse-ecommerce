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
        Schema::create('product_inventory_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')
                ->constrained('products')
                ->onDelete('cascade');

            $table->enum('action_type', ['in', 'out', 'adjustment'])->comment('Stock movement type');

            $table->integer('quantity')->comment('Quantity changed (+ or - based on action)');

            $table->integer('stock_before');
            $table->integer('stock_after');

            $table->string('reference_type')->nullable()
                ->comment('Order, Return, Manual, etc');

            $table->unsignedBigInteger('reference_id')->nullable()
                ->comment('Related record ID');

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');

            $table->text('note')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_inventory_logs');
    }
};
