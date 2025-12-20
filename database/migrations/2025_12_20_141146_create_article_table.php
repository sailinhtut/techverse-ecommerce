<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->longText('content');
            $table->json('tags')->nullable();

            $table->string('image')->nullable();

            $table->enum('status', ['draft', 'published', 'archived'])
                ->default('draft');

            $table->boolean('is_featured')->default(false);

            $table->timestamp('published_at')->nullable();

            $table->unsignedBigInteger('view_count')
                ->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
