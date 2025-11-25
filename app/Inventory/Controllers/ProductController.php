<?php

namespace App\Inventory\Controllers;

use App\Auth\Models\Wishlist;
use App\Inventory\Models\Brand;
use App\Inventory\Models\Category;
use App\Inventory\Models\Product;
use App\Inventory\Models\ProductVariant;
use App\Payment\Models\PaymentMethod;
use App\Shipping\Models\ShippingClass;
use App\Store\Models\MediaImage;
use App\Store\Models\StoreBranch;
use App\Tax\Models\TaxClass;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController
{
    public function showProductListUser(Request $request)
    {
        $expiredProducts = Product::where('is_promotion', true)
            ->where('promotion_end_time', '<', now())
            ->get();

        foreach ($expiredProducts as $product) {
            $product->update([
                'is_promotion' => false,
                'sale_price' => null,
            ]);
            ProductVariant::where('product_id', $product->id)
                ->update(['sale_price' => 0]);
        }

        $products = Cache::remember(
            'products_page_' . (request('page', 1)),
            config('app.cache_time', 3600),
            fn() => Product::where('is_active', true)
                ->orderByRaw('ISNULL(priority), priority ASC')
                ->orderByDesc('id')
                ->paginate(20)
                ->through(fn($product) => $product->jsonResponse(['category', 'brand']))
        );

        if ($request->expectsJson()) {
            return response()->json($products);
        }

        $wishlists = [];
        if (auth()->check()) {
            $wishlists = Wishlist::where('user_id', auth()->id())->get();
            $wishlists = $wishlists->map(fn($w) => $w->jsonResponse());
        }

        $cacheTime = config('app.cache_time', 3600);

        $pinned_products = Cache::remember(
            'pinned_products',
            $cacheTime,
            fn() =>
            Product::where('is_active', true)
                ->where('is_pinned', true)
                ->orderByRaw('ISNULL(priority), priority ASC')
                ->orderByDesc('id')
                ->take(4)
                ->get()
                ->map(fn($e) => $e->jsonResponse())
        );

        $categories = Cache::remember(
            'categories',
            $cacheTime,
            fn() =>
            Category::whereNull('parent_id')
                ->orderByDesc('id')
                ->get()
                ->map(fn($e) => $e->jsonResponse())
        );

        $brands = Cache::remember(
            'brands',
            $cacheTime,
            fn() =>
            Brand::orderByDesc('id')
                ->get()
                ->map(fn($e) => $e->jsonResponse())
        );

        $tags = Cache::remember(
            'tags',
            $cacheTime,
            fn() =>
            Product::select('tags')
                ->pluck('tags')
                ->flatten()
                ->unique()
                ->values()
                ->all()
        );

        $carousel_images = Cache::remember(
            'carousel_images',
            $cacheTime,
            fn() =>
            MediaImage::activeType('carousel_slider')->get()->map->lightJsonResponse()
        );
        $popup_images = Cache::remember(
            'popup_images',
            $cacheTime,
            fn() =>
            MediaImage::activeType('landing_pop_up')->get()->map->lightJsonResponse()
        );
        $banner_images = Cache::remember(
            'banner_images',
            $cacheTime,
            fn() =>
            MediaImage::activeType('side_banner')->get()->map->lightJsonResponse()
        );

        $all_branch_count = Cache::remember('all_branch_count', $cacheTime, fn() => StoreBranch::count());
        $all_product_count = Cache::remember('all_product_count', $cacheTime, fn() => Product::where('is_active', true)->count());
        $all_categories_count = Cache::remember('all_categories_count', $cacheTime, fn() => Category::count());
        $all_brand_count = Cache::remember('all_brand_count', $cacheTime, fn() => Brand::count());


        return view(
            'pages.user.core.product_list',
            [
                'product_list_title' => "Products For You",
                'products' => $products->items(),
                'pinned_products' => $pinned_products,
                'pagination' => $products->toArray(),
                'wishlists' => $wishlists,
                'categories' => $categories,
                'brands' => $brands,
                'tags' => $tags,
                'carousel_images' => $carousel_images,
                'popup_images' => $popup_images,
                'banner_images' => $banner_images,
                'promotion_display' => true,
                'today_best_display' => true,
                'popular_display' => true,
                'all_branch_count' => $all_branch_count,
                'all_product_count' => $all_product_count,
                'all_categories_count' => $all_categories_count,
                'all_brand_count' => $all_brand_count,
            ]
        );
    }

    public function showSearchProductListUser(Request $request)
    {
        $searchQuery = $request->get('q');

        $searchProducts = $this->searchProductService($request);

        if ($request->expectsJson()) {
            return response()->json($searchProducts);
        }

        $wishlists = Wishlist::where('user_id', auth()->id())->get();
        $wishlists = $wishlists->map(fn($w) => $w->jsonResponse());

        $cacheTime = config('app.cache_time', 3600);

        $pinned_products = Cache::remember(
            'pinned_products',
            $cacheTime,
            fn() =>
            Product::where('is_active', true)
                ->where('is_pinned', true)
                ->orderByRaw('ISNULL(priority), priority ASC')
                ->orderByDesc('id')
                ->take(4)
                ->get()
                ->map(fn($e) => $e->jsonResponse())
        );

        $categories = Cache::remember(
            'categories',
            $cacheTime,
            fn() =>
            Category::whereNull('parent_id')
                ->orderByDesc('id')
                ->get()
                ->map(fn($e) => $e->jsonResponse())
        );

        $brands = Cache::remember(
            'brands',
            $cacheTime,
            fn() =>
            Brand::orderByDesc('id')
                ->get()
                ->map(fn($e) => $e->jsonResponse())
        );

        $tags = Cache::remember(
            'tags',
            $cacheTime,
            fn() =>
            Product::select('tags')
                ->pluck('tags')
                ->flatten()
                ->unique()
                ->values()
                ->all()
        );

        $carousel_images = Cache::remember(
            'carousel_images',
            $cacheTime,
            fn() =>
            MediaImage::activeType('carousel_slider')->get()->map->lightJsonResponse()
        );
        $popup_images = Cache::remember(
            'popup_images',
            $cacheTime,
            fn() =>
            MediaImage::activeType('landing_pop_up')->get()->map->lightJsonResponse()
        );
        $banner_images = Cache::remember(
            'banner_images',
            $cacheTime,
            fn() =>
            MediaImage::activeType('side_banner')->get()->map->lightJsonResponse()
        );

        $all_branch_count = Cache::remember('all_branch_count', $cacheTime, fn() => StoreBranch::count());
        $all_product_count = Cache::remember('all_product_count', $cacheTime, fn() => Product::where('is_active', true)->count());
        $all_categories_count = Cache::remember('all_categories_count', $cacheTime, fn() => Category::count());
        $all_brand_count = Cache::remember('all_brand_count', $cacheTime, fn() => Brand::count());

        return view(
            'pages.user.core.product_list',
            [
                'product_list_title' => "Result Product For \"" . ($searchQuery) . "\"",
                'products' => $searchProducts->items(),
                'pinned_products' => $pinned_products,
                'pagination' => $searchProducts->toArray(),
                'wishlists' => $wishlists,
                'categories' => $categories,
                'brands' => $brands,
                'tags' => $tags,
                'query' => $searchQuery,
                'promotion_display' => false,
                'today_best_display' => false,
                'popular_display' => false,
                'go_home_display' => true,
                'carousel_images' => $carousel_images,
                'popup_images' => $popup_images,
                'banner_images' => $banner_images,
                'all_branch_count' => $all_branch_count,
                'all_product_count' => $all_product_count,
                'all_categories_count' => $all_categories_count,
                'all_brand_count' => $all_brand_count,
            ]
        );
    }

    public function showSearchProductListByCategoryUser(Request $request, $category_slug)
    {
        $query = Product::where('is_active', true)->orderByRaw('ISNULL(priority), priority ASC')
            ->orderByDesc('id');

        $foundCategory = Category::where('slug', $category_slug)->first();
        $products = [];

        if ($foundCategory) {
            $query->where('category_id', $foundCategory->id);
            $products = $query->paginate(10);
            $products->getCollection()->transform(fn($p) => $p->jsonResponse(['category', 'brand']));
        }

        if ($request->expectsJson()) {
            return response()->json($products);
        }

        $wishlists = Wishlist::where('user_id', auth()->id())->get();
        $wishlists = $wishlists->map(fn($w) => $w->jsonResponse());

        $cacheTime = config('app.cache_time', 3600);

        $pinned_products = Cache::remember(
            'pinned_products',
            $cacheTime,
            fn() =>
            Product::where('is_active', true)
                ->where('is_pinned', true)
                ->orderByRaw('ISNULL(priority), priority ASC')
                ->orderByDesc('id')
                ->take(4)
                ->get()
                ->map(fn($e) => $e->jsonResponse())
        );

        $categories = Cache::remember(
            'categories',
            $cacheTime,
            fn() =>
            Category::whereNull('parent_id')
                ->orderByDesc('id')
                ->get()
                ->map(fn($e) => $e->jsonResponse())
        );

        $brands = Cache::remember(
            'brands',
            $cacheTime,
            fn() =>
            Brand::orderByDesc('id')
                ->get()
                ->map(fn($e) => $e->jsonResponse())
        );

        $tags = Cache::remember(
            'tags',
            $cacheTime,
            fn() =>
            Product::select('tags')
                ->pluck('tags')
                ->flatten()
                ->unique()
                ->values()
                ->all()
        );

        $carousel_images = Cache::remember(
            'carousel_images',
            $cacheTime,
            fn() =>
            MediaImage::activeType('carousel_slider')->get()->map->lightJsonResponse()
        );
        $popup_images = Cache::remember(
            'popup_images',
            $cacheTime,
            fn() =>
            MediaImage::activeType('landing_pop_up')->get()->map->lightJsonResponse()
        );
        $banner_images = Cache::remember(
            'banner_images',
            $cacheTime,
            fn() =>
            MediaImage::activeType('side_banner')->get()->map->lightJsonResponse()
        );

        $all_branch_count = Cache::remember('all_branch_count', $cacheTime, fn() => StoreBranch::count());
        $all_product_count = Cache::remember('all_product_count', $cacheTime, fn() => Product::where('is_active', true)->count());
        $all_categories_count = Cache::remember('all_categories_count', $cacheTime, fn() => Category::count());
        $all_brand_count = Cache::remember('all_brand_count', $cacheTime, fn() => Brand::count());

        return view(
            'pages.user.core.product_list',
            [
                'product_list_title' =>  "Search Result For Category \"" . ($foundCategory->name ?? $category_slug) . "\"",
                'products' => !empty($products) ? $products->items() : [],
                'pinned_products' => $pinned_products,
                'pagination' => $products ? $products->toArray() : null,
                'wishlists' => $wishlists,
                'categories' => $categories,
                'brands' => $brands,
                'tags' => $tags,
                'promotion_display' => false,
                'today_best_display' => false,
                'popular_display' => false,
                'go_home_display' => true,
                'carousel_images' => $carousel_images,
                'popup_images' => $popup_images,
                'banner_images' => $banner_images,
                'all_branch_count' => $all_branch_count,
                'all_product_count' => $all_product_count,
                'all_categories_count' => $all_categories_count,
                'all_brand_count' => $all_brand_count,
            ]
        );
    }

    public function showSearchProductListByBrandUser(Request $request, $brand_slug)
    {
        $query = Product::where('is_active', true)->orderByRaw('ISNULL(priority), priority ASC')
            ->orderByDesc('id');

        $foundBrand = Brand::where('slug', $brand_slug)->first();

        $products = [];

        if ($foundBrand) {
            $query->where('brand_id', $foundBrand->id);
            $products = $query->paginate(10);
            $products->getCollection()->transform(fn($p) => $p->jsonResponse(['category', 'brand']));
        }

        if ($request->expectsJson()) {
            return response()->json($products);
        }

        $wishlists = Wishlist::where('user_id', auth()->id())->get();
        $wishlists = $wishlists->map(fn($w) => $w->jsonResponse());

        $cacheTime = config('app.cache_time', 3600);

        $pinned_products = Cache::remember(
            'pinned_products',
            $cacheTime,
            fn() =>
            Product::where('is_active', true)
                ->where('is_pinned', true)
                ->orderByRaw('ISNULL(priority), priority ASC')
                ->orderByDesc('id')
                ->take(4)
                ->get()
                ->map(fn($e) => $e->jsonResponse())
        );

        $categories = Cache::remember(
            'categories',
            $cacheTime,
            fn() =>
            Category::whereNull('parent_id')
                ->orderByDesc('id')
                ->get()
                ->map(fn($e) => $e->jsonResponse())
        );

        $brands = Cache::remember(
            'brands',
            $cacheTime,
            fn() =>
            Brand::orderByDesc('id')
                ->get()
                ->map(fn($e) => $e->jsonResponse())
        );

        $tags = Cache::remember(
            'tags',
            $cacheTime,
            fn() =>
            Product::select('tags')
                ->pluck('tags')
                ->flatten()
                ->unique()
                ->values()
                ->all()
        );

        $carousel_images = Cache::remember(
            'carousel_images',
            $cacheTime,
            fn() =>
            MediaImage::activeType('carousel_slider')->get()->map->lightJsonResponse()
        );
        $popup_images = Cache::remember(
            'popup_images',
            $cacheTime,
            fn() =>
            MediaImage::activeType('landing_pop_up')->get()->map->lightJsonResponse()
        );
        $banner_images = Cache::remember(
            'banner_images',
            $cacheTime,
            fn() =>
            MediaImage::activeType('side_banner')->get()->map->lightJsonResponse()
        );

        $all_branch_count = Cache::remember('all_branch_count', $cacheTime, fn() => StoreBranch::count());
        $all_product_count = Cache::remember('all_product_count', $cacheTime, fn() => Product::where('is_active', true)->count());
        $all_categories_count = Cache::remember('all_categories_count', $cacheTime, fn() => Category::count());
        $all_brand_count = Cache::remember('all_brand_count', $cacheTime, fn() => Brand::count());

        return view(
            'pages.user.core.product_list',
            [
                'product_list_title' => "Search Result For Brand \"" . ($foundBrand->name ?? $brand_slug) . "\"",
                'products' => !empty($products) ? $products->items() : [],
                'pinned_products' => $pinned_products,
                'pagination' => $products ? $products->toArray() : null,
                'wishlists' => $wishlists,
                'categories' => $categories,
                'brands' => $brands,
                'tags' => $tags,
                'promotion_display' => false,
                'today_best_display' => false,
                'popular_display' => false,
                'go_home_display' => true,
                'carousel_images' => $carousel_images,
                'popup_images' => $popup_images,
                'banner_images' => $banner_images,
                'all_branch_count' => $all_branch_count,
                'all_product_count' => $all_product_count,
                'all_categories_count' => $all_categories_count,
                'all_brand_count' => $all_brand_count,
            ]
        );
    }

    public function showSearchProductListByTagUser(Request $request, $tag)
    {

        $query = Product::where('is_active', true)
            ->orderByRaw('ISNULL(priority), priority ASC')
            ->orderByDesc('id');

        $normalizedTag = str_replace('-', ' ', $tag);

        if ($normalizedTag) {
            $query->where(function ($q) use ($normalizedTag) {
                $q->whereJsonContains('tags', $normalizedTag)
                    ->orWhereJsonContains('tags', ucfirst($normalizedTag))
                    ->orWhereJsonContains('tags', ucwords($normalizedTag))
                    ->orWhereJsonContains('tags', strtolower($normalizedTag))
                    ->orWhereJsonContains('tags', strtoupper($normalizedTag));
            });
        }

        $products = $query->paginate(10);
        $products->getCollection()->transform(fn($p) => $p->jsonResponse(['category', 'brand']));

        if ($request->expectsJson()) {
            return response()->json($products);
        }

        $wishlists = Wishlist::where('user_id', auth()->id())->get();
        $wishlists = $wishlists->map(fn($w) => $w->jsonResponse());

        $cacheTime = config('app.cache_time', 3600);

        $pinned_products = Cache::remember(
            'pinned_products',
            $cacheTime,
            fn() =>
            Product::where('is_active', true)
                ->where('is_pinned', true)
                ->orderByRaw('ISNULL(priority), priority ASC')
                ->orderByDesc('id')
                ->take(4)
                ->get()
                ->map(fn($e) => $e->jsonResponse())
        );

        $categories = Cache::remember(
            'categories',
            $cacheTime,
            fn() =>
            Category::whereNull('parent_id')
                ->orderByDesc('id')
                ->get()
                ->map(fn($e) => $e->jsonResponse())
        );

        $brands = Cache::remember(
            'brands',
            $cacheTime,
            fn() =>
            Brand::orderByDesc('id')
                ->get()
                ->map(fn($e) => $e->jsonResponse())
        );

        $tags = Cache::remember(
            'tags',
            $cacheTime,
            fn() =>
            Product::select('tags')
                ->pluck('tags')
                ->flatten()
                ->unique()
                ->values()
                ->all()
        );

        $carousel_images = Cache::remember(
            'carousel_images',
            $cacheTime,
            fn() =>
            MediaImage::activeType('carousel_slider')->get()->map->lightJsonResponse()
        );
        $popup_images = Cache::remember(
            'popup_images',
            $cacheTime,
            fn() =>
            MediaImage::activeType('landing_pop_up')->get()->map->lightJsonResponse()
        );
        $banner_images = Cache::remember(
            'banner_images',
            $cacheTime,
            fn() =>
            MediaImage::activeType('side_banner')->get()->map->lightJsonResponse()
        );

        $all_branch_count = Cache::remember('all_branch_count', $cacheTime, fn() => StoreBranch::count());
        $all_product_count = Cache::remember('all_product_count', $cacheTime, fn() => Product::where('is_active', true)->count());
        $all_categories_count = Cache::remember('all_categories_count', $cacheTime, fn() => Category::count());
        $all_brand_count = Cache::remember('all_brand_count', $cacheTime, fn() => Brand::count());

        return view(
            'pages.user.core.product_list',
            [
                'product_list_title' => "Search Result For Tag \"" . (ucfirst($normalizedTag)) . "\"",
                'products' => !empty($products) ? $products->items() : [],
                'pinned_products' => $pinned_products,
                'pagination' => $products ? $products->toArray() : null,
                'wishlists' => $wishlists,
                'categories' => $categories,
                'brands' => $brands,
                'tags' => $tags,
                'promotion_display' => false,
                'today_best_display' => false,
                'popular_display' => false,
                'go_home_display' => true,
                'carousel_images' => $carousel_images,
                'popup_images' => $popup_images,
                'banner_images' => $banner_images,
                'all_branch_count' => $all_branch_count,
                'all_product_count' => $all_product_count,
                'all_categories_count' => $all_categories_count,
                'all_brand_count' => $all_brand_count,
            ]
        );
    }

    public function showProductDetail(Request $request, string $slug)
    {
        try {
            $variantId = $request->get('variant');
            $product = Product::with('category')->where('slug', $slug)->firstOrFail();

            if ($product->is_promotion && $product->promotion_end_time && $product->promotion_end_time < now()) {
                $product->update([
                    'is_promotion' => false,
                    'sale_price' => null,
                ]);

                ProductVariant::where('product_id', $product->id)
                    ->update(['sale_price' => 0]);
            }

            $variant = null;
            if ($variantId) {
                $variant =  $product->productVariants()->find($variantId)?->jsonResponse();
            }


            $product->increment('interest');
            $product = $product->jsonResponse(['category', 'brand', 'productVariants', 'crossSells', 'upSells', 'overall_review']);


            $wishlists = [];
            if (auth()->check()) {
                $wishlists = Wishlist::where('user_id', auth()->id())->get();
                $wishlists = $wishlists->map(fn($w) => $w->jsonResponse());
            }
            $currentUrl = url()->current();
            $shareTitle = urlencode($product['name']);
            $shareDesc = urlencode(substr(strip_tags($product['short_description'] ?? ''), 0, 100));
            $shareImage = isset($product['image']) ? urlencode($product['image']) : '';

            $socialShareLinks = [
                'facebook' => "https://www.facebook.com/sharer/sharer.php?u={$currentUrl}",
                'twitter' => "https://twitter.com/intent/tweet?url={$currentUrl}&text={$shareTitle}",
                'linkedin' => "https://www.linkedin.com/shareArticle?url={$currentUrl}&title={$shareTitle}&summary={$shareDesc}",
                'whatsapp' => "https://api.whatsapp.com/send?text={$shareTitle} {$currentUrl}",
                'telegram' => "https://t.me/share/url?url={$currentUrl}&text={$shareTitle}",
            ];


            return view('pages.user.core.product_detail', compact('product', 'wishlists', 'variant', 'socialShareLinks'));
        } catch (ModelNotFoundException $error) {
            return redirect()->back()->with('status', 'Not Found Product');
        } catch (Exception $error) {
            return redirect()->back()->with('status', 'Something Went Wrong');
        }
    }

    public function viewAdminProductListPage(Request $request)
    {
        $sortBy = $request->get('sortBy', 'last_updated');
        $orderBy = $request->get('orderBy', 'desc');
        $perPage = $request->get('perPage', 20);
        $search = $request->get('query', null);
        $product_type = $request->get('product_type', null);

        $query = Product::where('is_active', true);
        $category_slug = $request->get('category', null);
        $brand_slug = $request->get('brand', null);
        $is_sale = $request->boolean('isSale', false);
        $is_promotion = $request->boolean('isPromotion', false);
        $is_pinned = $request->boolean('isPinned', false);

        if ($product_type) {
            $query->where('product_type', $product_type);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")->orWhere('id', 'like', "%{$search}%");
            });
        }

        if ($category_slug) {
            $matched_category = Category::where('slug', $category_slug)->first();
            if ($matched_category) $query->where('category_id', $matched_category->id);
        }

        if ($brand_slug) {
            $matched_brand = Brand::where('slug', $brand_slug)->first();
            if ($matched_brand) $query->where('brand_id', $matched_brand->id);
        }

        if ($is_sale) {
            $query->where('sale_price', '>', 0);
        }

        if ($is_promotion) {
            $query->where('is_promotion', true);
        }

        if ($is_pinned) {
            $query->where('is_pinned', true);
        }

        switch ($sortBy) {
            case 'last_updated':
                $query->orderBy('updated_at', $orderBy)
                    ->orderBy('id', $orderBy);
                break;

            case 'last_created':
                $query->orderBy('created_at', $orderBy)->orderBy('id', $orderBy);
                break;

            case 'low_popularity':
                $query->orderBy('interest', 'asc')
                    ->orderBy('id', $orderBy);
                break;

            case 'high_popularity':
                $query->orderBy('interest', 'desc')
                    ->orderBy('id',  $orderBy);
                break;

            case 'low_priority':
                $query->orderByRaw('ISNULL(priority), priority ASC')
                    ->orderBy('id', $orderBy);
                break;

            case 'high_priority':
                $query->orderByRaw('ISNULL(priority), priority DESC')
                    ->orderBy('id', $orderBy);
                break;

            default:
                $query->orderBy('updated_at', 'desc')
                    ->orderBy('id', 'desc');
        }

        $products = $query->paginate($perPage);
        $products->appends(request()->query());

        $products->getCollection()->transform(function ($product) {
            return $product->jsonResponse(['category', 'brand', 'paymentMethods', 'shippingClass', 'taxClass']);
        });

        $categories = Category::orderBy('id', 'desc')->get();
        $brands = Brand::orderBy('id', 'desc')->get();
        $payment_methods = PaymentMethod::orderBy('id', 'desc')->get();
        $shipping_classes = ShippingClass::orderBy('id', 'desc')->get();

        return view('pages.admin.dashboard.product.product_list', [
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
            'payment_methods' => $payment_methods,
            'shipping_classes' => $shipping_classes,
        ]);
    }

    public function viewAdminProductAddPage()
    {
        $product_categories = Category::orderBy('id', 'desc')->get()->map(fn($c) => $c->jsonResponse());;

        $product_brands = Brand::orderBy('id', 'desc')->get()->map(fn($c) => $c->jsonResponse());;

        $payment_methods = PaymentMethod::where('enabled', true)->get()->map(fn($c) => $c->jsonResponse());;

        $shipping_classes = ShippingClass::orderBy('id', 'desc')->get()->map(fn($c) => $c->jsonResponse());

        $tax_classes = TaxClass::orderBy('id', 'desc')->get()->map(fn($c) => $c->jsonResponse());

        return view('pages.admin.dashboard.product.edit_product', [
            'product_categories' => $product_categories,
            'product_brands' => $product_brands,
            'payment_methods' => $payment_methods,
            'shipping_classes' => $shipping_classes,
            'tax_classes' => $tax_classes,
        ]);
    }

    public function viewAdminProductEditPage(Request $request, string $id)
    {
        $product_categories = Category::orderBy('id', 'desc')->get()->map(fn($c) => $c->jsonResponse());;

        $product_brands = Brand::orderBy('id', 'desc')->get()->map(fn($c) => $c->jsonResponse());;

        $payment_methods = PaymentMethod::where('enabled', true)->get()->map(fn($c) => $c->jsonResponse());;

        $shipping_classes = ShippingClass::orderBy('id', 'desc')->get()->map(fn($c) => $c->jsonResponse());

        $tax_classes = TaxClass::orderBy('id', 'desc')->get()->map(fn($c) => $c->jsonResponse());

        $edit_product = Product::find($id);
        if (!$edit_product) {
            return redirect()->back()->with('error', 'Not Found Product');
        }
        $edit_product = $edit_product->jsonResponse(['category', 'brand', 'paymentMethods', 'productVariants', 'crossSells', 'upSells']);

        return view('pages.admin.dashboard.product.edit_product', [
            'edit_product' => $edit_product,
            'product_categories' => $product_categories,
            'product_brands' => $product_brands,
            'payment_methods' => $payment_methods,
            'shipping_classes' => $shipping_classes,
            'tax_classes' => $tax_classes,
        ]);
    }


    public function searchProductService(Request $request)
    {
        $searchQuery = $request->get('q');
        $searchCategorySlug = $request->get('category');
        $searchBrandSlug = $request->get('brand');
        $searchTag = $request->get('tag');

        $query = Product::where('is_active', true)->orderByRaw('ISNULL(priority), priority ASC')
            ->orderByDesc('id');

        if ($searchQuery) {
            $query->where('name', 'like', '%' . $searchQuery . '%');
        }

        if ($searchCategorySlug) {
            $categoryId = Category::where('slug', $searchCategorySlug)->value('id');
            if ($categoryId) {
                $query->where('category_id', $categoryId);
            }
        }

        if ($searchBrandSlug) {
            $brandId = Brand::where('slug', $searchBrandSlug)->value('id');
            if ($brandId) {
                $query->where('brand_id', $brandId);
            }
        }

        $normalizedTag = str_replace('-', ' ', $searchTag);
        if ($normalizedTag) {
            $query->where(function ($q) use ($normalizedTag) {
                $q->whereJsonContains('tags', $normalizedTag)
                    ->orWhereJsonContains('tags', ucfirst($normalizedTag))
                    ->orWhereJsonContains('tags', ucwords($normalizedTag))
                    ->orWhereJsonContains('tags', strtolower($normalizedTag))
                    ->orWhereJsonContains('tags', strtoupper($normalizedTag));
            });
        }

        $products = $query->paginate(10);

        $products->getCollection()->transform(fn($p) => $p->jsonResponse(['category', 'brand']));

        return $products;
    }

    public function fetchPopularProductsByAPI()
    {
        try {
            // $popular_products = Product::where('is_active', true)
            //     ->orderByDesc('interest') 
            //     ->orderByRaw('ISNULL(priority), priority ASC')
            //     ->orderByDesc('id')
            //     ->paginate(10);

            $page = request()->get('page', 1);
            $perPage = 10;

            $popular_products = Cache::remember("popular_products_page_{$page}_per_{$perPage}", config('app.cache_time', 3600), function () use ($perPage) {
                $paginator = Product::where('is_active', true)
                    ->where('interest', '>', 0)
                    ->orderByDesc('interest')
                    ->paginate($perPage);

                $paginator->getCollection()->transform(fn($product) => $product->jsonResponse(['category', 'brand', 'overall_review']));
                return $paginator;
            });

            return response()->json($popular_products);
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function fetchPromotionProductsByAPI()
    {
        try {
            $page = request()->get('page', 1);
            $perPage = 10;

            $promotion_products = Cache::remember("promotion_products_page_{$page}_per_{$perPage}", config('app.cache_time', 3600), function () use ($perPage) {
                $paginator = Product::where('is_active', true)
                    ->where('is_promotion', true)
                    ->orderByRaw('ISNULL(priority), priority ASC')
                    ->orderByDesc('id')
                    ->paginate($perPage);

                $paginator->getCollection()->transform(fn($product) => $product->jsonResponse(['category', 'brand', 'overall_review']));
                return $paginator;
            });

            return response()->json($promotion_products);
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function fetchPinnedProductsByAPI()
    {
        try {
            $page = request()->get('page', 1);
            $perPage = 10;

            $pinned_products = Cache::remember("pinned_products_page_{$page}_per_{$perPage}", config('app.cache_time', 3600), function () use ($perPage) {
                $paginator = Product::where('is_active', true)
                    ->where('is_pinned', true)
                    ->orderByRaw('ISNULL(priority), priority ASC')
                    ->orderByDesc('id')
                    ->paginate($perPage);

                $paginator->getCollection()->transform(fn($product) => $product->jsonResponse(['category', 'brand', 'overall_review']));
                return $paginator;
            });

            return response()->json($pinned_products);
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function searchProductsByAPI(Request $request)
    {
        $keyword = $request->input('q');

        if (!$keyword || strlen($keyword) < 2) {
            return response()->json([
                'data' => [],
                'message' => 'Enter at least 2 characters'
            ]);
        }

        $products = Product::select('id', 'name', 'slug', 'regular_price')
            ->where('name', 'like', "%{$keyword}%")
            ->orWhere('slug', 'like', "%{$keyword}%")
            ->limit(10)
            ->get();

        return response()->json([
            'data' => $products,
        ]);
    }

    public function searchByIds(Request $request)
    {
        $ids = $request->input('ids', []);

        if (!is_array($ids) || empty($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'No valid product IDs provided.',
                'data' => []
            ], 400);
        }

        $products = Product::whereIn('id', $ids)
            ->select('id', 'name', 'regular_price', 'image')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }


    public function addProduct(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'short_description' => 'nullable|string:65535',
            'long_description' => 'nullable|string',
            'tags' => 'nullable|string',
            'buying_price' => 'required|numeric',
            'regular_price' => 'required|numeric',
            'sku' => 'required|string|max:255',
            'is_active' => 'nullable|boolean',
            'sale_price' => 'nullable|numeric',
            'enable_stock' => 'nullable|boolean',
            'stock' => 'nullable|integer',
            'image' => 'nullable|image|max:2048',
            'image_gallery' => 'nullable|array',
            'image_gallery.*.label' => 'required_with:image_gallery|string|max:255',
            'image_gallery.*.image' => 'required_with:image_gallery|image|max:2048',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'payment_methods' => 'nullable|array',
            'payment_methods.*' => 'exists:payment_methods,id',
            'shipping_class_id' => 'nullable|exists:shipping_classes,id',
            'tax_class_id' => 'nullable|exists:tax_classes,id',
            'product_type' => 'required|in:simple,variable',

            'product_variants' => 'nullable|array',
            'product_variants.*.sku' => 'required|string|max:255',
            'product_variants.*.regular_price' => 'required|numeric|min:0',
            'product_variants.*.sale_price' => 'nullable|numeric|min:0',
            'product_variants.*.enable_stock' => 'nullable|boolean',
            'product_variants.*.stock' => 'nullable|integer|min:0',
            'product_variants.*.weight' => 'nullable|numeric|min:0',
            'product_variants.*.combination' => 'nullable|array',
            'product_variants.*.image' => 'nullable|image|max:2048',
            'product_variants.*.remove_image' => 'nullable|boolean',

            'cross_sell_product_ids' => 'nullable|array',
            'up_sell_product_ids' => 'nullable|array',
            'priority' => 'nullable|integer',
            'is_pinned' => 'nullable|boolean',
            'is_promotion' => 'nullable|boolean',
            'promotion_end_time' => 'nullable|date',
            'interest' => 'nullable|integer',
            'specifications' => 'nullable|array',

            'length' => 'nullable|numeric',
            'width' => 'nullable|numeric',
            'height' => 'nullable|numeric',
            'weight' => 'nullable|numeric',

            'enable_review' => 'nullable|boolean',
        ], [
            'product_variants.array' => 'The product variants must be a valid array.',
            'product_variants.*.sku.required' => 'Each product variant must have a SKU.',
            'product_variants.*.sku.string' => 'The SKU must be a valid string.',
            'product_variants.*.sku.max' => 'The SKU cannot exceed 255 characters.',
            'product_variants.*.regular_price.required' => 'Each variant must have a regular price.',
            'product_variants.*.regular_price.numeric' => 'The regular price must be a valid number.',
            'product_variants.*.regular_price.min' => 'The regular price cannot be negative.',
            'product_variants.*.sale_price.numeric' => 'The sale price must be a valid number.',
            'product_variants.*.sale_price.min' => 'The sale price cannot be negative.',
            'product_variants.*.stock.required' => 'Each variant must have a stock quantity.',
            'product_variants.*.stock.integer' => 'The stock quantity must be a whole number.',
            'product_variants.*.stock.min' => 'The stock quantity cannot be negative.',
            'product_variants.*.weight.numeric' => 'The weight must be a valid number.',
            'product_variants.*.weight.min' => 'The weight cannot be negative.',
            'product_variants.*.combination.array' => 'The combination must be a valid array.',
            'product_variants.*.image.image' => 'The uploaded file must be an image.',
            'product_variants.*.image.max' => 'The image cannot exceed 2MB.',
            'product_variants.*.remove_image.boolean' => 'The remove_image field must be true or false.',
        ]);

        DB::beginTransaction();

        try {

            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = Storage::disk('public')->putFile('products/thumbnails', $request->file('image'));
            }

            $galleryPaths = [];
            if ($request->has('image_gallery')) {
                foreach ($request->file('image_gallery', []) as $index => $galleryItem) {
                    if (isset($galleryItem['image'])) {
                        $path = Storage::disk('public')->putFile(
                            'products/gallery',
                            $galleryItem['image']
                        );
                        $galleryPaths[] = [
                            'label' => $validated['image_gallery'][$index]['label'],
                            'image'  => $path,
                        ];
                    }
                }
            }

            $new_product = Product::create([
                'name' => $validated['name'],
                'short_description' => $validated['short_description'] ?? null,
                'long_description' => $validated['long_description'] ?? null,
                'tags' => $validated['tags'] ? explode(',', $validated['tags']) : null,
                'sku' => $validated['sku'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
                'priority' => $validated['priority'] ?? null,
                'is_pinned' => $validated['is_pinned'] ?? false,
                'is_promotion' => $validated['is_promotion'] ?? false,
                'promotion_end_time' => $validated['promotion_end_time']
                    ? Carbon::parse($validated['promotion_end_time'])
                    : null,
                'interest' => $validated['interest'] ?? 0,
                'buying_price' => $validated['buying_price'],
                'regular_price' => $validated['regular_price'],
                'sale_price' => $validated['sale_price'] ?? null,
                'enable_stock' => $validated['enable_stock'] ?? true,
                'stock' => $validated['stock'] ?? 0,
                'image' => $imagePath,
                'image_gallery' => $galleryPaths,
                'category_id' => $validated['category_id'],
                'brand_id' => $validated['brand_id'] ?? null,
                'shipping_class_id' => $validated['shipping_class_id'] ?? null,
                'tax_class_id' => $validated['tax_class_id'] ?? null,
                'product_type' => $validated['product_type'] ?? 'simple',
                'specifications' => $validated['specifications'] ?? null,
                'length' => $validated['length'] ?? null,
                'width' => $validated['width'] ?? null,
                'height' => $validated['height'] ?? null,
                'weight' => $validated['weight'] ?? null,
                'enable_review' => $validated['enable_review'],

            ]);

            if (!empty($validated['payment_methods'])) {
                $new_product->paymentMethods()->sync($validated['payment_methods']);
            }

            if (!empty($validated['cross_sell_product_ids'])) {
                $new_product->crossSells()->sync($validated['cross_sell_product_ids']);
            }

            if (!empty($validated['up_sell_product_ids'])) {
                $new_product->upSells()->sync($validated['up_sell_product_ids']);
            }

            DB::commit();

            $productVariants = $validated['product_variants'] ?? [];

            foreach ($productVariants as $variantData) {
                $variant = ProductVariant::where('product_id', $new_product->id)
                    ->where('sku', $variantData['sku'])
                    ->first();

                if ($variant) {
                    $variant->regular_price = $variantData['regular_price'] ?? 0;
                    $variant->sale_price = $variantData['sale_price'] ?? null;
                    $variant->enable_stock = $variantData['enable_stock'] ?? false;
                    $variant->stock = $variantData['stock'] ?? 0;
                    $variant->weight = $variantData['weight'] ?? 0;
                    $variant->combination = $variantData['combination'] ?? [];
                } else {
                    // Create new variant
                    $variant = new ProductVariant();
                    $variant->product_id = $new_product->id;
                    $variant->sku = $variantData['sku'] ?? null;
                    $variant->regular_price = $variantData['regular_price'] ?? 0;
                    $variant->sale_price = $variantData['sale_price'] ?? null;
                    $variant->enable_stock = $variantData['enable_stock'] ?? false;
                    $variant->stock = $variantData['stock'] ?? 0;
                    $variant->weight = $variantData['weight'] ?? 0;
                    $variant->combination = $variantData['combination'] ?? [];
                }

                $variant->save();

                if (!empty($variantData['remove_image']) && $variantData['remove_image'] && $variant->image) {
                    Storage::disk('public')->delete($variant->image);
                    $variant->image = null;
                    $variant->save();
                }

                if (!empty($variantData['image'])) {
                    if ($variant->image && Storage::disk('public')->exists($variant->image)) {
                        Storage::disk('public')->delete($variant->image);
                    }

                    $imagePath = Storage::disk('public')->putFile('products/product_variants', $variantData['image']);
                    $variant->image = $imagePath;
                    $variant->save();
                }
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Product Created Successfully',
                    'data' => $new_product
                ]);
            }

            return redirect()->back()->with('success', 'Product Created Successfully');
        } catch (Exception $error) {
            DB::rollBack();
            return handleErrors($error, "Something Went Wrong");
        }
    }

    public function updateProduct(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'short_description' => 'nullable|string:65535',
            'long_description' => 'nullable|string',
            'tags' => 'nullable|string',
            'sku' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
            'buying_price' => 'required|numeric',
            'regular_price' => 'required|numeric',
            'sale_price' => 'nullable|numeric',
            'enable_stock' => 'nullable|boolean',
            'stock' => 'nullable|integer',
            'image' => 'nullable|image|max:2048',
            'remove_image' => 'nullable|boolean',
            'image_gallery' => 'nullable|array',
            'image_gallery.*.label' => 'required_with:image_gallery|string|max:255',
            'image_gallery.*.image' => 'nullable|image|max:2048',
            'remove_gallery' => 'nullable|array',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'payment_methods' => 'nullable|array',
            'payment_methods.*' => 'exists:payment_methods,id',
            'shipping_class_id' => 'nullable|exists:shipping_classes,id',
            'tax_class_id' => 'nullable|exists:tax_classes,id',
            'product_type' => 'required|in:simple,variable',

            'product_variants' => 'nullable|array',
            'product_variants.*.sku' => 'required|string|max:255',
            'product_variants.*.regular_price' => 'required|numeric|min:0',
            'product_variants.*.sale_price' => 'nullable|numeric|min:0',
            'product_variants.*.enable_stock' => 'nullable|boolean',
            'product_variants.*.stock' => 'nullable|integer|min:0',
            'product_variants.*.weight' => 'nullable|numeric|min:0',
            'product_variants.*.combination' => 'nullable|array',
            'product_variants.*.image' => 'nullable|image|max:2048',
            'product_variants.*.remove_image' => 'nullable|boolean',

            'cross_sell_product_ids' => 'nullable|array',
            'up_sell_product_ids' => 'nullable|array',
            'priority' => 'nullable|integer',
            'is_pinned' => 'nullable|boolean',
            'is_promotion' => 'nullable|boolean',
            'promotion_end_time' => 'nullable|date',
            'interest' => 'nullable|integer',
            'specifications' => 'nullable|array',

            'length' => 'nullable|numeric',
            'width' => 'nullable|numeric',
            'height' => 'nullable|numeric',
            'weight' => 'nullable|numeric',

            'enable_review' => 'nullable|boolean',
        ], [
            'product_variants.array' => 'The product variants must be a valid array.',
            'product_variants.*.sku.required' => 'Each product variant must have a SKU.',
            'product_variants.*.sku.string' => 'The SKU must be a valid string.',
            'product_variants.*.sku.max' => 'The SKU cannot exceed 255 characters.',
            'product_variants.*.regular_price.required' => 'Each variant must have a regular price.',
            'product_variants.*.regular_price.numeric' => 'The regular price must be a valid number.',
            'product_variants.*.regular_price.min' => 'The regular price cannot be negative.',
            'product_variants.*.sale_price.numeric' => 'The sale price must be a valid number.',
            'product_variants.*.sale_price.min' => 'The sale price cannot be negative.',
            'product_variants.*.stock.required' => 'Each variant must have a stock quantity.',
            'product_variants.*.stock.integer' => 'The stock quantity must be a whole number.',
            'product_variants.*.stock.min' => 'The stock quantity cannot be negative.',
            'product_variants.*.weight.numeric' => 'The weight must be a valid number.',
            'product_variants.*.weight.min' => 'The weight cannot be negative.',
            'product_variants.*.combination.array' => 'The combination must be a valid array.',
            'product_variants.*.image.image' => 'The uploaded file must be an image.',
            'product_variants.*.image.max' => 'The image cannot exceed 2MB.',
            'product_variants.*.remove_image.boolean' => 'The remove_image field must be true or false.',
        ]);


        if ($validator->fails()) {
            return handleErrors(new Exception($validator->errors()->first()), 'Validation failed', 422);
        }

        $validated = $validator->validated();

        DB::beginTransaction();


        try {
            $product = Product::findOrFail($id);

            if ($request->has('remove_image') && $request->boolean('remove_image')) {
                Storage::disk('public')->delete($product->image);
                $product->image = null;
            }

            if ($request->hasFile('image')) {
                if ($product->image && Storage::disk('public')->exists($product->image)) {
                    Storage::disk('public')->delete($product->image);
                }
                $product->image = Storage::disk('public')
                    ->putFile('products/thumbnails', $request->file('image'));
            }

            $gallery = $product->image_gallery ?? [];
            if ($request->has('remove_gallery')) {
                foreach ($request->input('remove_gallery', []) as $removeKey) {
                    if (isset($gallery[$removeKey]['image'])) {
                        $oldPath = $gallery[$removeKey]['image'];
                        Storage::disk('public')->delete($oldPath);
                    }
                    unset($gallery[$removeKey]);
                }
                $gallery = array_values($gallery);
            }

            if ($request->has('image_gallery')) {
                foreach ($request->input('image_gallery') as $idx => $item) {
                    if ($request->hasFile("image_gallery.$idx.image")) {
                        $path = Storage::disk('public')
                            ->putFile('products/gallery', $request->file("image_gallery.$idx.image"));

                        $gallery[] = [
                            'label' => $item['label'],
                            'image' => $path
                        ];
                    }
                }
            }

            $product->fill([
                'name' => $validated['name'],
                'short_description' => $validated['short_description'] ?? null,
                'long_description' => $validated['long_description'] ?? null,
                'tags' => $validated['tags'] ? explode(',', $validated['tags']) : null,
                'sku' => $validated['sku'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
                'priority' => $validated['priority'] ?? null,
                'is_pinned' => $validated['is_pinned'] ?? false,
                'is_promotion' => $validated['is_promotion'] ?? false,
                'promotion_end_time' => $validated['promotion_end_time']
                    ? Carbon::parse($validated['promotion_end_time'])
                    : null,
                'interest' => $validated['interest'] ?? 0,
                'buying_price' => $validated['buying_price'],
                'regular_price' => $validated['regular_price'],
                'sale_price' => $validated['sale_price'] ?? null,
                'enable_stock' => $validated['enable_stock'] ?? false,
                'stock' => $validated['stock'] ?? null,
                'category_id' => $validated['category_id'] ?? null,
                'brand_id' => $validated['brand_id'] ?? null,
                'image_gallery' => $gallery,
                'shipping_class_id' => $validated['shipping_class_id'] ?? null,
                'tax_class_id' => $validated['tax_class_id'] ?? null,
                'product_type' => $validated['product_type'] ?? 'simple',
                'specifications' => $validated['specifications'] ?? null,
                'length' => $validated['length'] ?? null,
                'width' => $validated['width'] ?? null,
                'height' => $validated['height'] ?? null,
                'weight' => $validated['weight'] ?? null,
                'enable_review' => $validated['enable_review'],
            ]);

            $product->save();

            if (isset($validated['payment_methods'])) {
                $product->paymentMethods()->sync($validated['payment_methods']);
            } else {
                $product->paymentMethods()->sync([]);
            }

            $product->crossSells()->sync($validated['cross_sell_product_ids'] ?? []);
            $product->upSells()->sync($validated['up_sell_product_ids'] ?? []);

            DB::commit();

            $productVariants = $validated['product_variants'] ?? [];

            foreach ($productVariants as $variantData) {
                $variant = ProductVariant::where('product_id', $product->id)
                    ->where('sku', $variantData['sku'])
                    ->first();

                if ($variant) {
                    $variant->regular_price = $variantData['regular_price'] ?? 0;
                    $variant->sale_price = $variantData['sale_price'] ?? null;
                    $variant->enable_stock = $variantData['enable_stock'] ?? false;
                    $variant->stock = $variantData['stock'] ?? 0;
                    $variant->weight = $variantData['weight'] ?? 0;
                    $variant->combination = $variantData['combination'] ?? [];
                } else {
                    // Create new variant
                    $variant = new ProductVariant();
                    $variant->product_id = $product->id;
                    $variant->sku = $variantData['sku'] ?? null;
                    $variant->regular_price = $variantData['regular_price'] ?? 0;
                    $variant->sale_price = $variantData['sale_price'] ?? null;
                    $variant->enable_stock = $variantData['enable_stock'] ?? false;
                    $variant->stock = $variantData['stock'] ?? 0;
                    $variant->weight = $variantData['weight'] ?? 0;
                    $variant->combination = $variantData['combination'] ?? [];
                }

                $variant->save();

                if (!empty($variantData['remove_image']) && $variantData['remove_image'] && $variant->image) {
                    Storage::disk('public')->delete($variant->image);
                    $variant->image = null;
                    $variant->save();
                }

                if (!empty($variantData['image'])) {
                    if ($variant->image && Storage::disk('public')->exists($variant->image)) {
                        Storage::disk('public')->delete($variant->image);
                    }

                    $imagePath = Storage::disk('public')->putFile('products/product_variants', $variantData['image']);
                    $variant->image = $imagePath;
                    $variant->save();
                }
            }

            return redirect()->back()->with('success', 'Product updated successfully');
        } catch (\Exception $error) {
            DB::rollBack();
            return handleErrors($error, "Something Went Wrong");
        }
    }

    public function updateShippingClassSelected(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'shipping_class_id' => 'nullable|exists:shipping_classes,id',
        ]);

        if ($validator->fails()) {
            return handleErrors(new Exception($validator->errors()->first()), 'Validation failed', 422);
        }

        $validated = $validator->validated();

        try {
            $ids = $request->input('ids', []);

            if (empty($ids)) {
                return redirect()->back()->with('error', 'No products selected for deletion.');
            }

            $products = Product::whereIn('id', $ids)->get();

            foreach ($products as $product) {

                $product->fill([
                    'shipping_class_id' => $validated['shipping_class_id'] ?? null
                ]);

                $product->save();
            }

            return redirect()->back()->with('success', 'Selected products updated successfully.');
        } catch (\Exception $error) {
            return handleErrors($error, "Something went wrong while updating selected products.");
        }
    }

    public function updateShippingClassAll(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shipping_class_id' => 'nullable|exists:shipping_classes,id',
        ]);

        if ($validator->fails()) {
            return handleErrors(new Exception($validator->errors()->first()), 'Validation failed', 422);
        }

        $validated = $validator->validated();

        try {
            $products = Product::all();

            foreach ($products as $product) {

                $product->fill([
                    'shipping_class_id' => $validated['shipping_class_id'] ?? null
                ]);

                $product->save();
            }

            return redirect()->back()->with('success', 'All products deleted successfully.');
        } catch (\Exception $error) {
            return handleErrors($error, "Something went wrong while deleting all products.");
        }
    }

    public function updatePaymentMethodSelected(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'payment_methods' => 'nullable|array',
            'payment_methods.*' => 'exists:payment_methods,id',
        ]);

        if ($validator->fails()) {
            return handleErrors(new Exception($validator->errors()->first()), 'Validation failed', 422);
        }

        $validated = $validator->validated();

        try {
            $ids = $request->input('ids', []);

            if (empty($ids)) {
                return redirect()->back()->with('error', 'No products selected for deletion.');
            }

            $products = Product::whereIn('id', $ids)->get();

            foreach ($products as $product) {

                if (isset($validated['payment_methods'])) {
                    $product->paymentMethods()->sync($validated['payment_methods']);
                } else {
                    $product->paymentMethods()->sync([]);
                }
            }

            return redirect()->back()->with('success', 'Selected products updated successfully.');
        } catch (\Exception $error) {
            return handleErrors($error, "Something went wrong while updating selected products.");
        }
    }

    public function updatePaymentMethodAll(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_methods' => 'nullable|array',
            'payment_methods.*' => 'exists:payment_methods,id',
        ]);

        if ($validator->fails()) {
            return handleErrors(new Exception($validator->errors()->first()), 'Validation failed', 422);
        }

        $validated = $validator->validated();

        try {
            $products = Product::all();

            foreach ($products as $product) {

                if (isset($validated['payment_methods'])) {
                    $product->paymentMethods()->sync($validated['payment_methods']);
                } else {
                    $product->paymentMethods()->sync([]);
                }
            }

            return redirect()->back()->with('success', 'All products deleted successfully.');
        } catch (\Exception $error) {
            return handleErrors($error, "Something went wrong while deleting all products.");
        }
    }



    public function deleteProduct(Request $request, string $id)
    {
        try {
            $product = Product::with('productVariants')->findOrFail($id);

            // Delete main product image
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            // Delete product gallery images
            $image_gallery = $product->image_gallery ?? [];
            foreach ($image_gallery as $gallery) {
                if (!empty($gallery['image'])) {
                    Storage::disk('public')->delete($gallery['image']);
                }
            }

            // Delete all variant images
            foreach ($product->productVariants as $variant) {
                if ($variant->image) {
                    Storage::disk('public')->delete($variant->image);
                }
            }

            // Delete the product (variants will be deleted if you have cascade in DB or handle manually)
            $product->delete();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Product Deleted Successfully'
                ]);
            }

            return redirect()->back()->with('success', 'Product Deleted Successfully');
        } catch (\Exception $error) {
            return handleErrors($error, "Something Went Wrong");
        }
    }

    public function deleteSelectedProducts(Request $request)
    {
        try {
            $ids = $request->input('ids', []);

            if (empty($ids)) {
                return redirect()->back()->with('error', 'No products selected for deletion.');
            }

            $products = Product::with('productVariants')->whereIn('id', $ids)->get();

            foreach ($products as $product) {
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }

                $image_gallery = $product->image_gallery ?? [];
                foreach ($image_gallery as $gallery) {
                    if (!empty($gallery['image'])) {
                        Storage::disk('public')->delete($gallery['image']);
                    }
                }

                foreach ($product->productVariants as $variant) {
                    if ($variant->image) {
                        Storage::disk('public')->delete($variant->image);
                    }
                }

                $product->delete();
            }

            return redirect()->back()->with('success', 'Selected products deleted successfully.');
        } catch (\Exception $error) {
            return handleErrors($error, "Something went wrong while deleting selected products.");
        }
    }


    public function deleteAllProducts()
    {
        try {
            $products = Product::with('productVariants')->get();

            foreach ($products as $product) {
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }

                $image_gallery = $product->image_gallery ?? [];
                foreach ($image_gallery as $gallery) {
                    if (!empty($gallery['image'])) {
                        Storage::disk('public')->delete($gallery['image']);
                    }
                }

                foreach ($product->productVariants as $variant) {
                    if ($variant->image) {
                        Storage::disk('public')->delete($variant->image);
                    }
                }

                $product->delete();
            }

            return redirect()->back()->with('success', 'All products deleted successfully.');
        } catch (\Exception $error) {
            return handleErrors($error, "Something went wrong while deleting all products.");
        }
    }
}
