@extends('layouts.admin.admin_dashboard')
@section('admin_dashboard_content')
    <div class="p-5">
        <div class="w-fit max-w-full text-sm bg-base-300 rounded px-2 overflow-x-auto ">
            <div class="breadcrumbs text-sm my-0 py-1">
                <ul>
                    <li>
                        <a href="{{ route('admin.dashboard.product.get') }}" class="btn btn-xs btn-ghost">
                            Products
                        </a>
                    </li>

                    <li>
                        @isset($edit_product)
                            <a href="{{ route('admin.dashboard.product.add.get') }}" class="btn btn-xs btn-ghost">
                                {{ $edit_product['name'] ?? 'Edit Product' }}
                            </a>
                        @else
                            <a href="{{ route('admin.dashboard.product.add.get') }}" class="btn btn-xs btn-ghost">
                                Add Product
                            </a>
                        @endisset
                    </li>
                </ul>
            </div>
        </div>

        <a href="{{ route('admin.dashboard.product.get') }}" class="btn btn-sm mt-2">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="size-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            Back
        </a>
        <p class="font-semibold lg:text-lg mb-3 mt-5"> {{ isset($edit_product) ? 'Edit Product' : 'Add Product' }}</p>

        @if ($errors->any())
            <div class="alert alert-error max-w-[300px] p-2 my-2">
                <ul class="mb-0 p-0 text-xs text-justify">
                    <li class="font-bold mb-1">Errors</li>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form
            action="{{ isset($edit_product) ? route('admin.dashboard.product.id.post', ['id' => $edit_product['id']]) : route('admin.dashboard.product.post') }}"
            method="POST" class="max-w-md flex flex-col gap-3" enctype="multipart/form-data">
            @csrf

            {{-- Title --}}
            <label for="name" class="text-sm">Product Name</label>
            <input type="text" name="name" id="name" class="input input-sm w-full"
                value="{{ old('name', $edit_product['name'] ?? '') }}" required>

            <label class="label">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" class="toggle toggle-sm toggle-primary" name="is_active" value="1"
                    {{ old('is_active', $edit_product['is_active'] ?? 1) ? 'checked' : '' }} />
                Enable Stock
            </label>

            <label for="sku" class="text-sm">SKU (Stock Keeping
                Unit)</label>
            <input type="text" name="sku" id="sku" class="input input-sm w-full"
                value="{{ old('sku', $edit_product['sku'] ?? '') }}">

            {{-- Prices --}}
            <label for="regular_price" class="text-sm">Regular
                Price</label>
            <input type="number" name="regular_price" id="regular_price" class="input input-sm w-full"
                value="{{ old('regular_price', $edit_product['regular_price'] ?? '') }}" required>

            <label for="sale_price" class="text-sm">Sale Price</label>
            <input type="number" name="sale_price" id="sale_price" class="input input-sm w-full"
                value="{{ old('sale_price', $edit_product['sale_price'] ?? '') }}">

            <label for="image" class="text-sm">Thumbnail Image</label>
            @if (!empty($edit_product['image']))
                <div class="flex items-center gap-2 mb-2">
                    <img src="{{ $edit_product['image'] }}" class="size-12 object-contain border rounded">
                    <label class="flex items-center gap-1 text-xs">
                        <input type="checkbox" name="remove_image" value="1" class="checkbox checkbox-xs">
                        Remove this image
                    </label>
                </div>
            @endif
            <input type="file" name="image" id="image" class="file-input file-input-sm w-full">

            <div x-data="{
                galleryInputs: [],
                addRow() {
                    this.galleryInputs.push({
                        label: '',
                        file: null
                    });
                },
                removeRow(index) {
                    this.galleryInputs.splice(index, 1);
                }
            }">
                <label class="text-sm">Image Gallery</label>

                @php
                    $gallery = old('image_gallery', $edit_product['image_gallery'] ?? []);
                @endphp

                <div class="w-full mt-2 flex flex-col gap-2">
                    @foreach ($gallery as $index => $item)
                        <div class="flex justify-start items-center gap-3">
                            @if (!empty($item['image']))
                                <div class="tooltip" data-tip="{{ $item['label'] }}">
                                    <img src="{{ $item['image'] }}"
                                        class="size-8 object-cover rounded border border-base-300"
                                        title="{{ $item['label'] ?? 'No Label' }}">
                                </div>
                            @endif

                            <label class="flex items-center gap-1 text-xs">
                                <input type="checkbox" name="remove_gallery[]" value="{{ $index }}"
                                    class="checkbox checkbox-xs">
                                Remove
                            </label>
                        </div>
                    @endforeach

                    <!-- Dynamic gallery input rows -->
                    <div class="flex flex-col justify-start items-start gap-2" id="gallery-input-box">
                        <template x-for="(item, index) in galleryInputs" :key="index">
                            <div class="flex justify-center items-center gap-2 w-full">
                                <input type="text" :name="`image_gallery[${index}][label]`" placeholder="Title"
                                    class="w-25 input input-sm " required x-model="item.label">
                                <input type="file" :name="`image_gallery[${index}][image]`"
                                    class="file-input file-input-sm" required
                                    @change="item.file = $event.target.files[0]">
                                <button type="button" class="btn btn-sm btn-circle" @click="removeRow(index)">✕</button>
                            </div>
                        </template>
                    </div>
                </div>

                <button type="button" class="btn btn-sm mt-2 w-fit" @click="addRow()">+ Add Image</button>
            </div>


            <label class="label">
                <input type="hidden" name="enable_stock" value="0">
                <input type="checkbox" class="toggle toggle-sm toggle-primary" name="enable_stock" value="1"
                    {{ old('enable_stock', $edit_product['enable_stock'] ?? 1) ? 'checked' : '' }} />
                Enable Stock Availability
            </label>

            <label for="stock" class="text-sm">Stock</label>
            <input type="number" name="stock" id="stock" class="input input-sm w-full"
                value="{{ old('stock', $edit_product['stock'] ?? '') }}">


            <label class="text-sm">Category</label>
            <select name="category_id" class="select select-sm w-full" required>
                @foreach ($product_categories as $category)
                    <option value="{{ $category['id'] }}"
                        {{ old('category_id', $edit_product['category_id'] ?? '') == $category['id'] ? 'selected' : '' }}>
                        {{ $category['name'] }}
                    </option>
                @endforeach
            </select>

            <label class="text-sm">Brand</label>
            <select name="brand_id" class="select select-sm w-full" required>
                @foreach ($product_brands as $brand)
                    <option value="{{ $brand['id'] }}"
                        {{ old('brand_id', $edit_product['brand_id'] ?? '') == $brand['id'] ? 'selected' : '' }}>
                        {{ $brand['name'] }}
                    </option>
                @endforeach
            </select>

            <label for="short_description" class="text-sm">Short
                Description (Optional)</label>
            <textarea name="short_description" id="short_description" class="textarea textarea-sm w-full" rows="4">{{ old('short_description', $edit_product['short_description'] ?? '') }}</textarea>

            <label for="long_description" class="text-sm">Long Description
                (Optional)</label>
            <textarea name="long_description" id="long_description" class="textarea textarea-sm w-full" rows="10">{{ old('long_description', $edit_product['long_description'] ?? '') }}</textarea>

            <div x-data="paymentSelector()" class="mt-4">
                <p class="text-sm mb-3">Payment Methods</p>

                <div class="flex flex-wrap gap-4">
                    <template x-for="method in paymentMethods" :key="method.id">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" class="checkbox checkbox-sm" :value="method.id"
                                :checked="selected.includes(method.id)" @change="toggle(method.id)">
                            <span x-text="method.name" class="text-sm"></span>
                        </label>
                    </template>
                </div>

                <template x-for="id in selected" :key="id">
                    <input type="hidden" name="payment_methods[]" :value="id">
                </template>
            </div>


            <label class="text-sm">Shipping Class</label>
            <select name="shipping_class_id" class="select select-sm w-full">
                <option value="">No Shipping Class</option>
                @foreach ($shipping_classes as $class)
                    <option value="{{ $class['id'] }}"
                        {{ old('shipping_class_id', $edit_product['shipping_class_id'] ?? '') == $class['id'] ? 'selected' : '' }}>
                        {{ $class['name'] }}
                    </option>
                @endforeach
            </select>

            <label class="text-sm">Tax Class</label>
            <select name="tax_class_id" class="select select-sm w-full">
                <option value="">No Tax Class</option>
                @foreach ($tax_classes as $class)
                    <option value="{{ $class['id'] }}"
                        {{ old('tax_class_id', $edit_product['tax_class_id'] ?? '') == $class['id'] ? 'selected' : '' }}>
                        {{ $class['name'] }}
                    </option>
                @endforeach
            </select>

            <div class="w-full flex flex-col items-start gap-3" x-data="productVariantForm()">
                <label for="stock" class="text-sm">Product Variants</label>
                <template x-for="(variant, index) in variants" :key="index">
                    <div class="w-full border border-base-300 p-4 rounded-lg flex flex-col gap-2 bg-base-100">
                        <div class="flex justify-between items-center">
                            <p class="font-medium" x-text="`Product Variant ${index + 1}`"></p>
                            <button type="button" class="btn btn-square btn-sm btn-outline btn-error"
                                @click="variant.id ? document.getElementById('delete_modal_' + variant.id).showModal() : removeVariant(index)"><svg
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-4">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                </svg>
                            </button>
                        </div>

                        <dialog :id="`delete_modal_${variant.id}`" class="modal">
                            <div class="modal-box">
                                <form method="dialog">
                                    <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                </form>
                                <p class="text-lg font-semibold py-0">Confirm Delete</p>

                                <p class="py-2 mb-0 text-sm">
                                    Are you sure you want to delete
                                    <span class="italic text-error" x-text="`Product Variant ${variant.sku}`"></span> ?
                                </p>
                                <div class="modal-action mt-0">
                                    <form method="dialog">
                                        <button class="btn lg:btn-md">Close</button>
                                    </form>
                                    <form method="POST" :action="`/admin/dashboard/variant/${variant.id}`">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn lg:btn-md btn-error">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </dialog>


                        <div x-show="variant.image" class="flex items-center gap-2 mb-2">
                            <img :src="variant.image" class="size-12 object-contain border border-base-300 rounded">
                            <label class="flex items-center gap-1 text-xs">
                                <input type="checkbox" :name="`product_variants[${index}][remove_image]`" value="1"
                                    class="checkbox checkbox-xs">
                                Remove this image
                            </label>
                        </div>

                        <!-- SKU -->
                        <label class="text-sm">SKU</label>
                        <input type="text"
                            :class="`input w-full ${variant.id !== null ? 'focus:outline-none focus:ring-0 focus:border-base-300 cursor-default bg-base-200' : ''}`"
                            placeholder="SKU" :name="`product_variants[${index}][sku]`" x-model="variant.sku"
                            :readonly="variant.id !== null" required>

                        <!-- Regular Price -->
                        <label class="text-sm">Regular Price</label>
                        <input type="number" step="0.01" class="input input-bordered w-full"
                            placeholder="Regular Price" :name="`product_variants[${index}][regular_price]`"
                            x-model="variant.regular_price" required>

                        <!-- Sale Price -->
                        <label class="text-sm">Sale Price</label>
                        <input type="number" step="0.01" class="input input-bordered w-full"
                            placeholder="Sale Price" :name="`product_variants[${index}][sale_price]`"
                            x-model="variant.sale_price">

                        <!-- Stock -->

                        <label class="text-sm">Stock</label>
                        <input type="number" class="input input-bordered w-full" placeholder="Stock"
                            :name="`product_variants[${index}][stock]`" x-model="variant.stock" required>

                        <!-- Weight -->
                        <label class="text-sm">Weight</label>
                        <input type="number" step="0.01" class="input input-bordered w-full" placeholder="Weight"
                            :name="`product_variants[${index}][weight]`" x-model="variant.weight">

                        <!-- Image -->
                        <label class="text-sm">Image</label>
                        <input type="file" class="file-input file-input-bordered w-full"
                            :name="`product_variants[${index}][image]`">

                        <!-- Combination (nested adder) -->
                        <div class="pt-3">
                            <p class="font-semibold mb-2">Combinations</p>

                            <template x-for="(pair, cIndex) in variant.combination" :key="cIndex">
                                <div class="flex gap-2 items-center mb-2">
                                    <input type="text" class="input input-bordered w-1/2" x-model="pair.key"
                                        placeholder="Key (e.g. color)">
                                    <input type="text" class="input input-bordered w-1/2"
                                        :name="`product_variants[${index}][combination][${pair.key}]`"
                                        x-model="pair.value" placeholder="Value (e.g. Red)">
                                    <button type="button" class="btn btn-square btn-sm btn-outline btn-error"
                                        @click="removeCombination(index, cIndex)">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-4">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
                                    </button>
                                </div>
                            </template>

                            <button type="button" class="btn btn-sm btn-secondary" @click="addCombination(index)">+ Add
                                Combination</button>
                        </div>
                    </div>
                </template>
                <button class="btn btn-secondary btn-sm" type="button" @click="addVariant()">+ Add Variant
                    Group</button>
            </div>


            <button type="submit" class="mt-10 btn btn-primary w-fit">
                {{ isset($edit_product) ? 'Update Product' : 'Add Product' }}
            </button>
        </form>
    </div>
@endsection

@push('script')
    <script>
        function paymentSelector() {
            return {
                paymentMethods: @json($payment_methods),
                selected: @json(collect($edit_product['payment_methods'] ?? [])->pluck('id')->toArray()),
                toggle(id) {
                    if (this.selected.includes(id)) {
                        this.selected = this.selected.filter(i => i !== id);
                    } else {
                        this.selected.push(id);
                    }
                }
            }
        }

        function productVariantForm() {
            let existingVariants = @json($edit_product['product_variants'] ?? []);

            existingVariants = existingVariants.map(variant => {
                return {
                    id: variant.id ?? null,
                    sku: variant.sku || '',
                    regular_price: variant.regular_price || 0,
                    sale_price: variant.sale_price || 0,
                    stock: variant.stock || 0,
                    weight: variant.weight || 0,
                    image: variant.image || null,
                    combination: variant.combination ? Object.entries(variant.combination).map(([key, value]) => ({
                        key,
                        value
                    })) : []
                };
            });

            return {
                variants: existingVariants,

                addVariant() {
                    this.variants.push({
                        id: null,
                        sku: '',
                        regular_price: '',
                        sale_price: '',
                        stock: '',
                        weight: '',
                        combination: []
                    });
                },

                removeVariant(index) {
                    this.variants.splice(index, 1);
                },

                addCombination(vIndex) {
                    this.variants[vIndex].combination.push({
                        key: '',
                        value: ''
                    });
                },

                removeCombination(vIndex, cIndex) {
                    this.variants[vIndex].combination.splice(cIndex, 1);
                },
            }
        }
    </script>
@endpush
