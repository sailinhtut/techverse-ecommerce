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
        Schema::create('store_branches', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('country')->nullable();
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_code')->nullable();
            $table->text('address')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->time('open_time')->nullable();
            $table->time('close_time')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('media_images', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('type');
            $table->string('image_path')->nullable();
            $table->string('link')->nullable();
            $table->integer('priority')->default(0);
            $table->boolean('is_active')->default(true);
            $table->dateTime('start_at')->nullable();
            $table->dateTime('end_at')->nullable();
            $table->timestamps();
        });

        Schema::create('frequent_questions', function (Blueprint $table) {
            $table->id();

            $table->string('question');
            $table->text('answer');

            $table->boolean('is_active')
                ->default(true)
                ->comment('Show or hide FAQ');

            $table->integer('sort_order')
                ->default(0)
                ->comment('Display order');

            $table->timestamps();
        });

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
        Schema::dropIfExists('frequent_questions');
        Schema::dropIfExists('media_images');
        Schema::dropIfExists('store_branches');
    }
};
