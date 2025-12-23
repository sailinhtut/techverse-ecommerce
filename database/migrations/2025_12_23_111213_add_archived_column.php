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
        Schema::table('invoices', function (Blueprint $table) {
            $table->boolean('archived')->default(false);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->boolean('archived')->default(false);
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->boolean('archived')->default(false);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->boolean('archived')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
