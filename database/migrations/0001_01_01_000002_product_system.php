<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->enum('type', ['fixed', 'percentage']);
            $table->decimal('value', 10, 2);
            $table->enum('apply_to', ['product', 'category', 'cart']);
            $table->json('product_ids')->nullable();
            $table->json('category_ids')->nullable();
            $table->decimal('min_cart_value', 10, 2)->nullable();
            $table->dateTime('valid_from')->nullable();
            $table->dateTime('valid_to')->nullable();
            $table->integer('usage_limit')->nullable();
            $table->integer('used')->default(0);
            $table->timestamps();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->integer('interest')->default(0);
            $table->foreignId('parent_id')->nullable()->constrained('categories')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('interest')->default(0);
            $table->string('slug')->unique();
            $table->timestamps();
        });


        Schema::create('shipping_classes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::create('shipping_zones', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('country');
            $table->string('state');
            $table->string('city');
            $table->string('postal_code');
            $table->timestamps();
        });

        Schema::create('shipping_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('enabled')->default(true);
            $table->boolean('is_free')->default(false);
            $table->timestamps();
        });

        Schema::create('shipping_rates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();

            $table->foreignId('shipping_zone_id')->nullable()->constrained('shipping_zones')->cascadeOnDelete();
            $table->foreignId('shipping_method_id')->nullable()->constrained('shipping_methods')->cascadeOnDelete();
            $table->foreignId('shipping_class_id')->nullable()->constrained('shipping_classes')->cascadeOnDelete();

            $table->enum('type', ['per_item', 'per_quantity', 'per_weight'])->default('per_item');
            $table->boolean('is_percentage')->default(false);
            $table->decimal('cost', 10, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('tax_classes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::create('tax_zones', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('country');
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_code')->nullable();
            $table->timestamps();
        });

        Schema::create('tax_rates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();

            $table->foreignId('tax_zone_id')->nullable()->constrained('tax_zones')->cascadeOnDelete();
            $table->foreignId('tax_class_id')->nullable()->constrained('tax_classes')->cascadeOnDelete();

            $table->enum('type', ['per_item', 'per_quantity', 'per_weight'])->default('per_item');
            $table->boolean('is_percentage')->default(true);
            $table->decimal('rate', 10, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sku')->unique()->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('slug')->unique();
            $table->string('image')->nullable();
            $table->json('image_gallery')->nullable();
            $table->enum('product_type', ['simple', 'variable'])->default('simple');
            $table->text('short_description')->nullable();
            $table->longText('long_description')->nullable();
            $table->decimal('regular_price', 10, 2)->default(0);
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->boolean('enable_stock')->default(false);
            $table->integer('stock')->default(0);
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->foreignId('brand_id')->nullable()->constrained('brands')->onDelete('set null');
            $table->json('tags')->nullable();
            $table->json('specifications')->nullable();
            $table->integer('priority')->nullable();
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_promotion')->default(false);
             $table->dateTime('promotion_end_time')->nullable();
            $table->integer('interest')->default(0);

            $table->foreignId('shipping_class_id')->nullable()->constrained('shipping_classes')->onDelete('set null');
            $table->foreignId('tax_class_id')->nullable()->constrained('tax_classes')->onDelete('set null');

            $table->decimal('length', 10, 2)->nullable();
            $table->decimal('width', 10, 2)->nullable();
            $table->decimal('height', 10, 2)->nullable();
            $table->decimal('weight', 10, 2)->nullable();

            $table->timestamps();
        });

        Schema::create('product_attributes', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g. Color, Size
            $table->mediumText('values'); // e.g. ["Red","Blue","Green"]
            $table->timestamps();
        });

        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('sku')->unique();
            $table->json('combination'); // {"Color":"Red","Size":"M"}
            $table->decimal('regular_price', 10, 2)->default(0);
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->boolean('enable_stock')->default(false);
            $table->integer('stock')->default(0);
            $table->string('image')->nullable();
            $table->decimal('weight', 10, 2)->nullable();
            $table->timestamps();
        });

        Schema::create('wishlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('product_variant_id')->nullable()->constrained('product_variants')->onDelete('cascade');

            $table->string('note')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'product_id', 'product_variant_id'], 'unique_user_product_variant');
        });

        Schema::create('product_cross_sell', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('cross_sell_id')->constrained('products')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['product_id', 'cross_sell_id']);
        });

        Schema::create('product_up_sell', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('up_sell_id')->constrained('products')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['product_id', 'up_sell_id']);
        });


        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['online', 'manual'])->default('manual');
            $table->enum('code', ['cod', 'direct_bank_transfer']);
            $table->boolean('enabled')->default(true);
            $table->enum('priority', ['low', 'high'])->default('high');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('payment_method_attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_method_id')->nullable()->constrained('payment_methods')->cascadeOnDelete();
            $table->string('key');
            $table->mediumText('value');
            $table->timestamps();
        });

        Schema::create('product_payment_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->nullable()->constrained('products')->cascadeOnDelete();
            $table->foreignId('payment_method_id')->nullable()->constrained('payment_methods')->cascadeOnDelete();

            $table->timestamps();
        });

        Schema::create('product_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->decimal('rating', 3, 1)->unsigned();
            $table->text('comment')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->timestamps();
        });

        Schema::create('product_review_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_id')->constrained('product_reviews')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('reply');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_review_replies');
        Schema::dropIfExists('product_reviews');
        Schema::dropIfExists('product_payment_methods');
        Schema::dropIfExists('payment_method_attributes');
        Schema::dropIfExists('product_up_sell');
        Schema::dropIfExists('product_cross_sell');
        Schema::dropIfExists('wishlists');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('product_attributes');

        Schema::dropIfExists('products');

        Schema::dropIfExists('shipping_rates');
        Schema::dropIfExists('shipping_methods');
        Schema::dropIfExists('shipping_zones');
        Schema::dropIfExists('shipping_classes');

        Schema::dropIfExists('tax_rates');
        Schema::dropIfExists('tax_zones');
        Schema::dropIfExists('tax_classes');

        Schema::dropIfExists('payment_methods');
        Schema::dropIfExists('brands');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('coupons');
    }
};
