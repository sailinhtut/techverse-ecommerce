@extends('layouts.admin.admin_dashboard')
@section('admin_dashboard_content')
    <div class="p-3">
        <div class="mb-3">
            <button onclick="history.back()" class="btn btn-sm flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Back
            </button>
        </div>

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
                            <a href="{{ route('admin.dashboard.product.edit.id.get', $edit_product['id']) }}"
                                class="btn btn-xs btn-ghost">
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
            method="POST" class="w-full flex flex-col gap-3" enctype="multipart/form-data" x-data="{ submitting: false }"
            @submit="submitting=true">
            @csrf

            <div class="tabs tabs-box bg-base-100 shadow-none" x-data='{ productType: @json($edit_product['product_type'] ?? 'simple') }'>
                <input type="radio" name="active_tab" class="tab" aria-label="General" checked="checked" />
                <div class="tab-content">
                    <div class="w-full flex flex-col items-start gap-3 pt-3">
                        <div class="max-w-xs w-full flex flex-col gap-2">
                            <label for="product_type" class="text-sm">Product Type</label>
                            <select name="product_type" class="select w-[200px]" required x-model="productType">
                                <option value="simple">Simple Product</option>
                                <option value="variable">Variable Product</option>
                            </select>
                        </div>

                        <div class="w-full flex flex-col items-start gap-3 rounded-box border border-base-300 p-3">
                            <label for="image" class="text-sm">Thumbnail Image</label>
                            @if (!empty($edit_product['image']))
                                <div class="flex items-center gap-2">
                                    <img src="{{ $edit_product['image'] }}" class="size-12 object-contain border rounded">
                                    <label class="flex items-center gap-1 text-xs">
                                        <input type="checkbox" name="remove_image" value="1"
                                            class="checkbox checkbox-xs">
                                        Remove this image
                                    </label>
                                </div>
                            @endif
                            <input type="file" name="image" id="image" class="file-input file-input-sm ">
                        </div>

                        <div class="w-full flex flex-col items-start gap-3 rounded-box border border-base-300 p-3"
                            x-data="{
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

                            <div class="flex flex-col gap-2">
                                @foreach ($gallery as $index => $item)
                                    <div class="flex justify-start items-center gap-3">
                                        @if (!empty($item['image']))
                                            <div class="tooltip" data-tip="{{ $item['label'] }}">
                                                <img src="{{ $item['image'] }}"
                                                    class="size-12 object-cover rounded border border-base-300"
                                                    title="{{ $item['label'] ?? 'No Label' }}">
                                            </div>
                                        @endif

                                        <label class="flex items-center gap-1 text-xs">
                                            <input type="checkbox" name="remove_gallery[]" value="{{ $index }}"
                                                class="checkbox checkbox-xs">
                                            Remove this image
                                        </label>
                                    </div>
                                @endforeach

                                <div class="flex flex-col justify-start items-start gap-2" id="gallery-input-box">
                                    <template x-for="(item, index) in galleryInputs" :key="index">
                                        <div class="flex justify-center items-center gap-2 w-full">
                                            <input type="text" :name="`image_gallery[${index}][label]`"
                                                placeholder="Title" class="w-25 input input-sm " required
                                                x-model="item.label">
                                            <input type="file" :name="`image_gallery[${index}][image]`"
                                                class="file-input file-input-sm" required
                                                @change="item.file = $event.target.files[0]">
                                            <button type="button" class="btn btn-sm btn-circle"
                                                @click="removeRow(index)">✕</button>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <button type="button" class="btn btn-sm w-fit" @click="addRow()">+ Add Image</button>
                        </div>


                        <div
                            class="w-full p-3 border border-base-300 rounded-box grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                            <div class="w-full flex flex-col gap-2">
                                <label for="name" class="text-sm">Product Name*</label>
                                <input type="text" name="name" id="name" class="input w-full"
                                    value="{{ old('name', $edit_product['name'] ?? '') }}" required>
                            </div>
                            <div x-data='{sku : "{{ old('sku', $edit_product['sku'] ?? '') }}"}'
                                class="w-full flex flex-col gap-2">
                                <label for="sku" class="text-sm">SKU (Stock Keeping Unit)*</label>
                                <div class="flex gap-2">
                                    <input type="text" name="sku" id="sku" x-model="sku"
                                        class="input w-full" placeholder="Enter or generate SKU" required>
                                    <button type="button" class="btn btn-outline btn-primary"
                                        @click="sku = '{{ str_replace(' ', '-', strtoupper(old('name', $edit_product['name'] ?? 'SKU'))) }}' +'-'+ Math.random().toString(36).substring(2, 8).toUpperCase()">
                                        Generate
                                    </button>
                                </div>
                            </div>
                            <div class="w-full flex flex-col gap-2">
                                <label class="text-sm">Category*</label>
                                <select name="category_id" class="select w-full" required>
                                    @foreach ($product_categories as $category)
                                        <option value="{{ $category['id'] }}"
                                            {{ old('category_id', $edit_product['category_id'] ?? '') == $category['id'] ? 'selected' : '' }}>
                                            {{ $category['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="w-full flex flex-col gap-2">
                                <label class="text-sm">Brand*</label>
                                <select name="brand_id" class="select w-full" required>
                                    @foreach ($product_brands as $brand)
                                        <option value="{{ $brand['id'] }}"
                                            {{ old('brand_id', $edit_product['brand_id'] ?? '') == $brand['id'] ? 'selected' : '' }}>
                                            {{ $brand['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="w-full flex flex-col gap-2">
                                <label class="text-sm">Tags (Optional, Comma Separated)</label>
                                <input type="text" name="tags" class="input w-full"
                                    value="{{ old('tags', implode(',', $edit_product['tags'] ?? [])) }}" required>
                            </div>
                            <div class="w-full flex flex-col gap-2 md:col-span-2 lg:col-span-3">
                                <label for="short_description" class="text-sm">Short
                                    Description (Optional)</label>
                                <textarea name="short_description" id="short_description" class="textarea w-full" rows="4">{{ old('short_description', $edit_product['short_description'] ?? '') }}</textarea>
                            </div>
                        </div>

                        <div x-data="{ enable_stock: @json($edit_product['enable_stock'] ?? false) }"
                            class="w-full p-3 border border-base-300 rounded-box grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">

                            <div class="w-full flex flex-col gap-2">
                                <label for="buying_price" class="text-sm">Buying
                                    Price (Optional)</label>
                                <input type="number" name="buying_price" id="buying_price"
                                    class="input input-sm w-full"
                                    value="{{ old('buying_price', $edit_product['buying_price'] ?? '') }}" step="any"
                                    required>
                            </div>

                            <div class="w-full flex flex-col gap-2">
                                <label for="regular_price" class="text-sm">Regular
                                    Price*</label>
                                <input type="number" name="regular_price" id="regular_price"
                                    class="input input-sm w-full"
                                    value="{{ old('regular_price', $edit_product['regular_price'] ?? '') }}"
                                    step="any" required>
                            </div>

                            <div class="w-full flex flex-col gap-2">
                                <label for="sale_price" class="text-sm">Sale Price (Optional)</label>
                                <input type="number" name="sale_price" id="sale_price" class="input input-sm w-full"
                                    value="{{ old('sale_price', $edit_product['sale_price'] ?? '') }}" step="any">
                            </div>

                            <div class="w-full flex flex-col gap-2">
                                <div x-show="enable_stock" x-cloak x-transition>
                                    <label for="stock" class="text-sm">Stock (Optional)</label>
                                    <input type="number" name="stock" id="stock" class="input input-sm w-full"
                                        value="{{ old('stock', $edit_product['stock'] ?? '0') }}">
                                </div>
                            </div>

                            <div class="w-full flex flex-col gap-2 self-end">
                                <label class="label text-sm">
                                    <input type="hidden" name="enable_stock" value="0">
                                    <input type="checkbox" class="toggle toggle-sm toggle-primary" name="enable_stock"
                                        value="1" x-model="enable_stock" />
                                    Enable Stock Availability
                                </label>
                            </div>
                        </div>

                        <div class="p-3 rounded-box border border-base-300 w-full flex flex-col gap-2">
                            <label class="text-sm flex items-center gap-2">Long Description (Optional)<span
                                    class="tooltip font-normal" data-tip="{{ config('app.template_usage_tooltip') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                                    </svg>
                                </span></label>
                            <div x-data="quillSetUpData()">
                                <div x-ref="editor" class=""></div>
                                <textarea class="hidden" name="long_description" x-ref="input">
                                    {{ old('long_description', $edit_product['long_description'] ?? '') }}
                                </textarea>
                            </div>
                            {{-- <textarea name="long_description" id="long_description" class="textarea w-full" rows="10"></textarea> --}}
                        </div>
                    </div>
                </div>

                <input type="radio" name="active_tab" class="tab" aria-label="Specifications" />
                <div class="tab-content" x-data="specificationForm(@js($edit_product['specifications'] ?? []))">
                    <div class="mt-3 w-full flex flex-col gap-2 rounded-box border border-base-300 p-3">
                        <p class="text-sm">Product Specification</p>
                        <button class="btn btn-secondary btn-sm w-fit" type="button" @click="addSpecification('','')">+
                            Add Specification</button>
                        <div class="mt-3 w-full flex flex-col gap-2">
                            <template x-for="(spec,index) in specifications" :key="index">
                                <div class="flex flex-col gap-2 mb-3">
                                    <p class="text-sm">Specification <span x-text="index+1"></span></p>
                                    <div class="w-full grid grid-cols-1 md:grid-cols-[200px_1fr_50px] items-start gap-3">
                                        <input type="text" :name="`specifications[${index}][key]`"
                                            x-model="specifications[index].key" placeholder="Enter Title" class="input">
                                        <textarea type="text" :name="`specifications[${index}][value]`" x-model='spec.value' placeholder="Enter Value"
                                            class="textarea w-full"></textarea>
                                        <button type="button" class="btn btn-square btn-sm btn-outline btn-error"
                                            @click="removeSpecification(spec.key)">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-4">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <input x-show="productType === 'variable'" type="radio" name="active_tab" class="tab"
                    aria-label="Product Variants" />
                <div class="tab-content">
                    <div class="mt-3 w-full flex flex-col items-start gap-3 rounded-box border border-base-300 p-3"
                        x-data="productVariantForm()">
                        <label for="stock" class="text-sm">Product Variants</label>
                        <button class="btn btn-secondary btn-sm" type="button" @click="addVariant()">+ Add Variant
                            Group</button>

                        <div class="w-full grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
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
                                                <button
                                                    class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                            </form>
                                            <p class="text-lg font-semibold py-0">Confirm Delete</p>

                                            <p class="py-2 mb-0 text-sm">
                                                Are you sure you want to delete
                                                <span class="italic text-error"
                                                    x-text="`Product Variant ${variant.sku}`"></span>
                                                ?
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
                                        <img :src="variant.image"
                                            class="size-12 object-contain border border-base-300 rounded">
                                        <label class="flex items-center gap-1 text-xs">
                                            <input type="checkbox" :name="`product_variants[${index}][remove_image]`"
                                                value="1" class="checkbox checkbox-xs">
                                            Remove this image
                                        </label>
                                    </div>

                                    <!-- SKU -->
                                    <label class="text-sm">SKU</label>
                                    <div class="flex gap-2">
                                        <input type="text"
                                            :class="`input w-full ${variant.id !== null ? 'focus:outline-none focus:ring-0 focus:border-base-300 cursor-default bg-base-200' : ''}`"
                                            placeholder="SKU" :name="`product_variants[${index}][sku]`"
                                            x-model="variant.sku" :readonly="variant.id !== null" required>
                                        <button x-show="variant.id === null" type="button"
                                            class="btn btn-outline btn-primary"
                                            @click="variant.sku='{{ str_replace(' ', '-', strtoupper(old('name', $edit_product['name'] ?? 'SKU'))) }}' + '-VARIANT-' + Math.random().toString(36).substring(2, 8).toUpperCase()">
                                            Generate
                                        </button>
                                    </div>

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

                                    <label class="text-sm">Weight</label>
                                    <input type="number" step="0.01" class="input input-bordered w-full"
                                        placeholder="Weight" :name="`product_variants[${index}][weight]`"
                                        x-model="variant.weight">

                                    <!-- Stock -->
                                    <div class="flex flex-col gap-2">
                                        <label class="text-sm">Enable Stock</label>
                                        <input type="hidden" :name="`product_variants[${index}][enable_stock]`"
                                            value="0">
                                        <input type="checkbox" :name="`product_variants[${index}][enable_stock]`"
                                            value="1" class="toggle toggle-sm toggle-primary"
                                            x-model="variant.enable_stock">
                                    </div>

                                    <div class="flex flex-col gap-2" x-show="variant.enable_stock">
                                        <label class="text-sm">Stock</label>
                                        <input type="number" class="input input-bordered w-full" placeholder="Stock"
                                            :name="`product_variants[${index}][stock]`" x-model="variant.stock">
                                    </div>



                                    <!-- Image -->
                                    <label class="text-sm">Image</label>
                                    <input type="file" class="file-input file-input-bordered w-full"
                                        :name="`product_variants[${index}][image]`">

                                    <!-- Combination (nested adder) -->
                                    <div class="pt-3">
                                        <p class="font-semibold mb-2">Combinations</p>

                                        <template x-for="(pair, cIndex) in variant.combination" :key="cIndex">
                                            <div class="flex gap-2 items-center mb-2">
                                                <input type="text" class="input input-bordered w-1/2"
                                                    x-model="pair.key" placeholder="Key (e.g. color)">
                                                <input type="text" class="input input-bordered w-1/2"
                                                    :name="`product_variants[${index}][combination][${pair.key}]`"
                                                    x-model="pair.value" placeholder="Value (e.g. Red)">
                                                <button type="button" class="btn btn-square btn-sm btn-outline btn-error"
                                                    @click="removeCombination(index, cIndex)">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                        class="size-4">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </template>

                                        <button type="button" class="btn btn-sm btn-secondary"
                                            @click="addCombination(index)">+
                                            Add
                                            Combination</button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>



                <input type="radio" name="active_tab" class="tab" aria-label="Shipping Class" />
                <div class="tab-content">
                    <div
                        class="mt-3 w-full grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 rounded-box border border-base-300 p-3">
                        <div class="w-full flex flex-col gap-2">
                            <label class="text-sm">Width (*cm) (Optional)</label>
                            <input type="number" step="any" class="input input-bordered w-full"
                                placeholder="Product Width" name="width"
                                value="{{ old('width', $edit_product['width'] ?? '') }}">
                        </div>
                        <div class="w-full flex flex-col gap-2">
                            <label class="text-sm">Length (*cm) (Optional)</label>
                            <input type="number" step="any" class="input input-bordered w-full"
                                placeholder="Product Length" name="length"
                                value="{{ old('length', $edit_product['length'] ?? '') }}">
                        </div>
                        <div class="w-full flex flex-col gap-2">
                            <label class="text-sm">Height (*cm) (Optional)</label>
                            <input type="number" step="any" class="input input-bordered w-full"
                                placeholder="Product Height" name="height"
                                value="{{ old('height', $edit_product['height'] ?? '') }}">
                        </div>
                        <div class="w-full flex flex-col gap-2">
                            <label class="text-sm">Weight (*kg) (Optional)</label>
                            <input type="number" step="any" class="input input-bordered w-full"
                                placeholder="Product Weight" name="weight"
                                value="{{ old('weight', $edit_product['weight'] ?? '') }}">
                        </div>
                        <div class="w-full flex flex-col gap-2">
                            <label class="text-sm">Shipping Class</label>
                            <select name="shipping_class_id" class="select  w-full">
                                <option value="">No Shipping Class</option>
                                @foreach ($shipping_classes as $class)
                                    <option value="{{ $class['id'] }}" @selected(old('shipping_class_id', $edit_product['shipping_class_id'] ?? '') == $class['id'])>
                                        {{ $class['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <input type="radio" name="active_tab" class="tab" aria-label="Tax Class" />
                <div class="tab-content">
                    <div
                        class="mt-3 w-full grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 rounded-box border border-base-300 p-3">
                        <div class="w-full flex flex-col gap-2">
                            <label class="text-sm">Tax Class</label>
                            <select name="tax_class_id" class="select w-full">
                                <option value="">No Tax Class</option>
                                @foreach ($tax_classes as $class)
                                    <option value="{{ $class['id'] }}" @selected(old('tax_class_id', $edit_product['tax_class_id'] ?? '') == $class['id'])>
                                        {{ $class['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <input type="radio" name="active_tab" class="tab" aria-label="Payment Method" />
                <div class="tab-content" x-data="paymentSelector()">
                    <div
                        class="mt-3 w-full grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 rounded-box border border-base-300 p-3">
                        <div class="w-full flex flex-col gap-2">
                            <label class="text-sm mb-3">Payment Methods</label>
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
                    </div>
                </div>

                <input type="radio" name="active_tab" class="tab" aria-label="Marketing" />
                <div class="tab-content">
                    <div
                        class="mt-3 w-full p-3 border border-base-300 rounded-box grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        <div class="w-full flex flex-col gap-2">
                            <label for="priority" class="text-sm flex items-center gap-3">Pinned Product
                                <span class="tooltip font-normal"
                                    data-tip="Pinned Products will be displayed in Today Best Product List">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                                    </svg>
                                </span>
                            </label>

                            <select name="is_pinned" class="select w-full" required>
                                <option value="1" @selected(old('is_pinned', $edit_product['is_pinned'] ?? false))>
                                    Pinned
                                </option>
                                <option value="0" @selected(!old('is_pinned', $edit_product['is_pinned'] ?? false))>
                                    Normal
                                </option>
                            </select>
                        </div>
                        <div class="w-full flex flex-col gap-2">
                            <label for="priority" class="text-sm flex items-center gap-3">Promotion Product
                                <span class="tooltip font-normal"
                                    data-tip="Promotion Products will be displayed in Promotion Product List">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                                    </svg>
                                </span>
                            </label>

                            <select name="is_promotion" class="select w-full" required>
                                <option value="1" @selected(old('is_promotion', $edit_product['is_promotion'] ?? false))>
                                    Promotion
                                </option>
                                <option value="0" @selected(!old('is_promotion', $edit_product['is_promotion'] ?? false))>
                                    Normal
                                </option>
                            </select>
                        </div>
                        <div class="w-full flex flex-col gap-2">
                            <label for="promotion_end_time" class="text-sm flex items-center gap-3">Promotion End Time
                                <span class="tooltip font-normal"
                                    data-tip="Promotion status will be reset when prmotion end time has ended. Leave empty not to reset.">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                                    </svg>
                                </span>
                            </label>

                            <input type="datetime-local" name="promotion_end_time"
                                value="{{ old('promotion_end_time', isset($edit_product['promotion_end_time']) ? $edit_product['promotion_end_time']->format('Y-m-d\TH:i') : '') }}"
                                class="input input-bordered w-full" />
                        </div>

                        <div class="w-full flex flex-col gap-2">
                            <label for="interest" class="text-sm flex items-center gap-3">Popularity Interest
                                <span class="tooltip font-normal"
                                    data-tip="Each time a user visits a product detail page, its interest count is incremented by 1. Products with higher interest will appear on the popular products list. If no products have any interest yet, those with the highest priority will be displayed instead.">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                                    </svg>
                                </span>
                            </label>
                            <input type="number" name="interest" id="interest" class="input w-full"
                                value="{{ old('interest', $edit_product['interest'] ?? '0') }}">
                        </div>
                    </div>
                    <div
                        class="mt-3 w-full grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 rounded-box border border-base-300 p-3">
                        <div class="w-full flex flex-col gap-2" x-data="marketingForm({{ json_encode($edit_product['cross_sell_product_ids'] ?? []) }})">
                            <label class="text-sm font-semibold mb-1 flex items-center gap-3">Cross Sell Products
                                <span class="tooltip font-normal"
                                    data-tip="Cross-sell products (related items often bought together)">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                                    </svg>
                                </span>
                            </label>
                            <input type="text" x-model.debounce.400ms="productQuery"
                                placeholder="Type product name..." class="input input-bordered w-full" />

                            <template x-if="productLoading">
                                <p class="text-gray-500 text-sm mt-3">Searching products...</p>
                            </template>

                            <template x-if="productResults.length > 0">
                                <ul
                                    class="border border-base-300 rounded-box mt-3 divide-y divide-base-300 max-h-48 overflow-y-auto">
                                    <template x-for="item in productResults" :key="item.id">
                                        <li @click="addProduct(item)"
                                            class="px-3 py-2 cursor-pointer hover:bg-base-200 flex justify-between text-sm">
                                            <span x-text="item.name"></span>
                                            <span class="text-gray-500">$<span x-text="item.regular_price"></span></span>
                                        </li>
                                    </template>
                                </ul>
                            </template>

                            <div class="pt-2">
                                <p class="font-medium mb-1 text-sm">Selected Products</p>
                                <template x-if="selectedProducts.length === 0">
                                    <p class="text-sm text-gray-500">No product selected</p>
                                </template>
                                <template x-for="(product, index) in selectedProducts" :key="product.id">
                                    <div
                                        class="flex justify-between items-center mb-1 border border-base-300 rounded-box py-1 px-3">
                                        <span class="text-xs" x-text="product.name"></span>
                                        <button type="button" @click="removeProduct(index)"
                                            class="btn btn-xs btn-ghost btn-square">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-4 stroke-error">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                        <input type="hidden" name="cross_sell_product_ids[]" :value="product.id">
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                    <div
                        class="mt-3 w-full grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 rounded-box border border-base-300 p-3">
                        <div class="w-full flex flex-col gap-2" x-data="marketingForm({{ json_encode($edit_product['up_sell_product_ids'] ?? []) }})">
                            <label class="text-sm font-semibold mb-1 flex items-center gap-3">Up Sell Products
                                <span class="tooltip font-normal"
                                    data-tip="Up-sell products (premium or alternative upgrades)">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                                    </svg>
                                </span>
                            </label>
                            <input type="text" x-model.debounce.400ms="productQuery"
                                placeholder="Type product name..." class="input input-bordered w-full" />

                            <template x-if="productLoading">
                                <p class="text-gray-500 text-sm mt-3">Searching products...</p>
                            </template>

                            <template x-if="productResults.length > 0">
                                <ul
                                    class="border border-base-300 rounded-box mt-3 divide-y divide-base-300 max-h-48 overflow-y-auto">
                                    <template x-for="item in productResults" :key="item.id">
                                        <li @click="addProduct(item)"
                                            class="px-3 py-2 cursor-pointer hover:bg-base-200 flex justify-between text-sm">
                                            <span x-text="item.name"></span>
                                            <span class="text-gray-500">$<span x-text="item.regular_price"></span></span>
                                        </li>
                                    </template>
                                </ul>
                            </template>

                            <div class="pt-2">
                                <p class="font-medium mb-1 text-sm">Selected Products</p>
                                <template x-if="selectedProducts.length === 0">
                                    <p class="text-sm text-gray-500">No product selected</p>
                                </template>
                                <template x-for="(product, index) in selectedProducts" :key="product.id">
                                    <div
                                        class="flex justify-between items-center mb-1 border border-base-300 rounded-box py-1 px-3">
                                        <span class="text-xs" x-text="product.name"></span>
                                        <button type="button" @click="removeProduct(index)"
                                            class="btn btn-xs btn-ghost btn-square">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-4 stroke-error">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                        <input type="hidden" name="up_sell_product_ids[]" :value="product.id">
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <input type="radio" name="active_tab" class="tab" aria-label="Sorting Order" />
                <div class="tab-content">
                    <div class="w-full flex flex-col items-start gap-3 pt-3">
                        <div
                            class="w-full p-3 border border-base-300 rounded-box grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                            <div class="w-full flex flex-col gap-2">
                                <label for="priority" class="text-sm flex items-center gap-3">Priority Display (Optional)
                                    <span class="tooltip font-normal"
                                        data-tip="Highest Order:
                                        1, Leave Empty for Normal Ordering Procedure">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-4">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                                        </svg>
                                    </span>
                                </label>
                                <input type="number" name="priority" id="priority" class="input w-full"
                                    value="{{ old('priority', $edit_product['priority'] ?? '') }}">
                            </div>
                        </div>
                    </div>
                </div>

                <input type="radio" name="active_tab" class="tab" aria-label="Review" />
                <div class="tab-content">
                    <div
                        class="mt-3 w-full p-3 border border-base-300 rounded-box grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        <div class="w-full flex flex-col gap-2">
                            <label for="priority" class="text-sm flex items-center gap-3">Enable Product Review
                                <span class="tooltip font-normal"
                                    data-tip="If enabled, product review section will appear in product detail page.">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                                    </svg>
                                </span>
                            </label>
                            <select name="enable_review" class="select w-full" required>
                                <option value="1" @selected(old('enable_review', $edit_product['enable_review'] ?? true))>
                                    Enabled
                                </option>
                                <option value="0" @selected(!old('enable_review', $edit_product['enable_review'] ?? true))>
                                    Disabled
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" class="mt-10 btn btn-primary w-fit" :disabled="submitting">
                <span x-show="submitting" class="loading loading-spinner loading-sm mr-2"></span>
                <span x-show="submitting">{{ isset($edit_product) ? 'Saving Product' : 'Adding Product' }}</span>
                <span x-show="!submitting">
                    {{ isset($edit_product) ? 'Update Product' : 'Add Product' }}
                </span>
            </button>
        </form>
    </div>
@endsection

@push('script')
    <script src="https://cdn.jsdelivr.net/gh/scrapooo/quill-resize-module@1.0.2/dist/quill-resize-module.js"></script>
    <script>
        function specificationForm(existingSpecification) {

            return {
                specifications: existingSpecification ?? [],
                addSpecification(key, value) {
                    this.specifications.push({
                        key: key,
                        value: value
                    });
                },
                removeSpecification(key) {
                    this.specifications = this.specifications.filter((e) => e.key != key);
                }
            }
        }

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
                    enable_stock: variant.enable_stock || false,
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
                        enable_stock: false,
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

        function marketingForm(initialProductIds = []) {
            return {
                productQuery: '',
                productResults: [],
                selectedProducts: [],
                productLoading: false,
                initialProductIds: initialProductIds,

                async searchProducts() {
                    if (this.productQuery.length < 2) {
                        this.productResults = [];
                        return;
                    }
                    this.productLoading = true;
                    try {
                        const response = await axios.get(`/admin/dashboard/product/search?q=${this.productQuery}`);
                        this.productResults = response.data.data ?? [];
                    } catch (e) {
                        console.error(e);
                    } finally {
                        this.productLoading = false;
                    }
                },

                addProduct(product) {
                    if (!this.selectedProducts.find(p => p.id === product.id)) {
                        this.selectedProducts.push(product);
                    }
                    this.productQuery = '';
                    this.productResults = [];
                },

                removeProduct(index) {
                    this.selectedProducts.splice(index, 1);
                },

                async init() {
                    this.$watch('productQuery', () => this.searchProducts());

                    if (this.initialProductIds.length > 0) {
                        try {
                            const res = await axios.post('/admin/dashboard/product/search-ids', {
                                ids: this.initialProductIds
                            });
                            this.selectedProducts = res.data.data ?? [];
                        } catch (e) {
                            console.error('Failed to load existing products', e);
                        }
                    }
                }
            };
        }

        const sitePrimaryColor = "{{ $settings['site_primary_color'] ?? config('app.primary_color') }}";

        const sitePrimaryContentColor =
            "{{ $settings['site_primary_content_color'] ?? config('app.site_primary_content_color') }}";

        const colorPalette = [
            // Site branding colors first
            sitePrimaryColor,
            sitePrimaryContentColor,

            // GREEN
            'HoneyDew', 'PaleGreen', 'LightGreen', 'Lime', 'LimeGreen', 'MediumSeaGreen', 'SeaGreen', 'ForestGreen',
            'Green', 'DarkGreen', 'Olive', 'OliveDrab', 'YellowGreen',

            // RED / PINK
            'MistyRose', 'LightCoral', 'Salmon', 'DarkSalmon', 'Red', 'FireBrick', 'Crimson', 'Pink', 'HotPink',
            'DeepPink', 'Fuchsia', 'Magenta',

            // BLUE / CYAN
            'LightCyan', 'PaleTurquoise', 'Cyan', 'Aqua', 'Aquamarine', 'Turquoise', 'MediumTurquoise', 'DarkTurquoise',
            'LightBlue', 'SkyBlue', 'DeepSkyBlue', 'DodgerBlue', 'CornflowerBlue', 'RoyalBlue', 'Blue', 'MediumBlue',
            'DarkBlue', 'Navy', 'MidnightBlue',

            // PURPLE / VIOLET
            'Lavender', 'Thistle', 'Plum', 'Orchid', 'Violet', 'MediumOrchid', 'DarkOrchid', 'Purple', 'RebeccaPurple',
            'DarkViolet', 'MediumPurple', 'SlateBlue', 'MediumSlateBlue',

            // YELLOW / ORANGE / BROWN
            'LightYellow', 'LemonChiffon', 'LightGoldenRodYellow', 'PapayaWhip', 'Moccasin', 'PeachPuff',
            'PaleGoldenRod', 'Khaki', 'DarkKhaki', 'Yellow', 'Gold', 'GoldenRod', 'Orange', 'DarkOrange', 'OrangeRed',
            'Peru', 'Chocolate', 'SandyBrown', 'BurlyWood', 'Tan', 'RosyBrown', 'Salmon', 'Sienna',

            // NEUTRALS / BLACK / WHITE / GRAY
            'Snow', 'White', 'WhiteSmoke', 'Gainsboro', 'LightGray', 'Silver', 'DarkGray', 'Gray', 'DimGray', 'Black'
        ];

        document.addEventListener("alpine:init", () => {
            // Register with Quill v2
            Quill.register("modules/resize", window.QuillResizeModule);
        });

        function quillSetUpData() {
            return {
                init() {
                    const toolbar = [
                        [{
                            'font': []
                        }],
                        [{
                            'size': ['small', false, 'large', 'huge']
                        }],
                        ['bold', 'italic', 'underline', 'strike', 'code'],
                        [{
                            'color': colorPalette
                        }, {
                            'background': colorPalette
                        }],
                        [{
                            'script': 'sub'
                        }, {
                            'script': 'super'
                        }],
                        [{
                            'header': 1
                        }, {
                            'header': 2
                        }, 'blockquote', 'code-block'],
                        [{
                            'list': 'ordered'
                        }, {
                            'list': 'bullet'
                        }, {
                            'indent': '-1'
                        }, {
                            'indent': '+1'
                        }],
                        ['direction', {
                            'align': []
                        }],
                        ['link', 'image', 'video'],
                        ['clean']
                    ];

                    let initial = (this.$refs.input.value || '').trim();

                    let editor = new Quill(this.$refs.editor, {
                        theme: 'snow',
                        placeholder: 'Write something...',
                        modules: {
                            toolbar: {
                                container: toolbar,
                                handlers: {
                                    image: function() {
                                        const input = document.createElement('input');
                                        input.setAttribute('type', 'file');
                                        input.setAttribute('accept', 'image/*');
                                        input.click();

                                        input.onchange = () => {
                                            const file = input.files[0];
                                            if (file) {
                                                if (file.size > 200 * 1024) {
                                                    alert(
                                                        "Image is too large. Maximum allowed size is 200 KB."
                                                    );
                                                    return;
                                                }

                                                const reader = new FileReader();
                                                reader.onload = (e) => {
                                                    const range = editor.getSelection(true);
                                                    editor.insertEmbed(range.index, 'image', e.target
                                                        .result, 'user');
                                                    editor.setSelection(range.index + 1, 0);
                                                };
                                                reader.readAsDataURL(file);
                                            }
                                        };
                                    }

                                }
                            },
                            resize: {
                                parchment: Quill.import('parchment'),
                                displayStyles: {
                                    backgroundColor: 'black',
                                    border: 'none',
                                    color: 'white'
                                }
                            }
                        },
                        formats: [
                            'size', 'bold', 'italic', 'underline', 'color', 'list', 'link', 'image',
                            'video', 'background', 'script', 'header', 'blockquote', 'code-block', 'code',
                            'direction',
                            'align', 'font', 'strike', 'indent'
                        ]
                    });

                    // Set initial content
                    editor.root.innerHTML = initial;

                    if (initial.length === 0) {
                        editor.setSelection(0, 0);
                    }

                    editor.on('text-change', () => {
                        this.$refs.input.value = editor.root.innerHTML.trim();
                    });
                }
            }
        }
    </script>
@endpush
