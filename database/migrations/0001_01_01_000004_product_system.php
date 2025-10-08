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
            $table->string('code')->unique();            // Coupon code
            $table->enum('type', ['fixed', 'percentage']); // How discount is calculated
            $table->decimal('value', 10, 2);            // Discount amount or percentage
            $table->enum('apply_to', ['product', 'category', 'cart']); // Level of application
            $table->json('product_ids')->nullable();    // Optional: for per product
            $table->json('category_ids')->nullable();   // Optional: for per category
            $table->decimal('min_cart_value', 10, 2)->nullable(); // For per cart
            $table->dateTime('valid_from')->nullable();
            $table->dateTime('valid_to')->nullable();
            $table->integer('usage_limit')->nullable(); // Max uses
            $table->timestamps();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('categories')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('shipping_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['flat', 'per_item', 'weight_based', 'distance_based'])->default('flat');
            $table->decimal('cost', 10, 2)->default(0);
            $table->text('description')->nullable();
            $table->json('config')->nullable();
            $table->timestamps();
        });

        Schema::create('tax_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('rate', 5, 2)->default(0);
            $table->enum('type', ['inclusive', 'exclusive'])->default('exclusive');
            $table->string('country')->nullable();
            $table->timestamps();
        });

        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['online', 'offline'])->default('offline');
            $table->text('description')->nullable();
            $table->boolean('enabled')->default(true);
            $table->json('config')->nullable();
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sku')->unique()->nullable();
            $table->string('slug')->unique();
            $table->string('image')->nullable();
            $table->json('image_gallery')->nullable();
            $table->enum('product_type', ['simple', 'variable'])->default('simple');
            $table->text('short_description')->nullable();
            $table->longText('long_description')->nullable();
            $table->decimal('regular_price', 10, 2)->default(0);
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->boolean('enable_stock')->default(true);
            $table->integer('stock')->default(0);
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->foreignId('brand_id')->nullable()->constrained('brands')->onDelete('set null');
            $table->json('tags')->nullable();
            $table->json('specifications')->nullable();
            $table->json('shipping_methods')->nullable(); // store selected IDs
            $table->json('tax_methods')->nullable();
            $table->json('payment_methods')->nullable();
            $table->timestamps();
        });

        // ======================
        // 7️⃣ Product Variant Attributes
        // ======================
        Schema::create('product_variant_attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('name'); // e.g. Color, Size
            $table->json('values'); // e.g. ["Red","Blue","Green"]
            $table->timestamps();
        });

        // ======================
        // 8️⃣ Product Variants
        // ======================
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('sku')->unique();
            $table->json('combination'); // {"Color":"Red","Size":"M"}
            $table->decimal('regular_price', 10, 2)->default(0);
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->integer('stock')->default(0);
            $table->string('image')->nullable();
            $table->decimal('weight', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('product_variant_attributes');
        Schema::dropIfExists('products');
        Schema::dropIfExists('payment_methods');
        Schema::dropIfExists('tax_methods');
        Schema::dropIfExists('shipping_methods');
        Schema::dropIfExists('brands');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('coupons');
    }
};
