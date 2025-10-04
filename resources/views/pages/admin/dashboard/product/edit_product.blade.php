@extends('layouts.admin.admin_dashboard')
@section('admin_dashboard_content')
    <div class="p-5">
        <p class="font-semibold lg:text-lg mb-3">
            {{ isset($edit_product) ? 'Edit Product' : 'Add Product' }}
        </p>

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
            method="POST" class="lg:w-[300px]  flex flex-col gap-3" enctype="multipart/form-data">
            @csrf

            {{-- Title --}}
            <label for="title" class="text-sm">Product Name</label>
            <input type="text" name="title" id="title" class="input input-sm"
                value="{{ old('title', $edit_product['title'] ?? '') }}" required>

            {{-- Prices --}}
            <label for="regular_price" class="text-sm">Regular Price</label>
            <input type="number" name="regular_price" id="regular_price" class="input input-sm"
                value="{{ old('regular_price', $edit_product['regular_price'] ?? '') }}" required>

            <label for="sale_price" class="text-sm">Sale Price</label>
            <input type="number" name="sale_price" id="sale_price" class="input input-sm"
                value="{{ old('sale_price', $edit_product['sale_price'] ?? '') }}">

            {{-- Thumbnail --}}
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
            <input type="file" name="image" id="image" class="file-input file-input-sm">

            <div>
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
                                        class="size-8 object-cover rounded border border-base-300 "
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


                    <div class=" flex flex-col justify-start items-start gap-2" id='gallery-input-box'> </div>
                </div>

                <button type='button' class="btn btn-sm mt-2 w-fit" id="add-gallery-row">+ Add Image</button>
            </div>

            {{-- Stock --}}
            <label for="stock" class="text-sm">Stock</label>
            <input type="number" name="stock" id="stock" class="input input-sm"
                value="{{ old('stock', $edit_product['stock'] ?? '') }}">

            {{-- Category --}}
            <label for="stock" class="text-sm">Category</label>
            <select name="category_id" class="select select-sm" required>
                @foreach ($product_categories as $category)
                    <option value="{{ $category['id'] }}"
                        {{ old('category_id', $edit_product['category_id'] ?? '') == $category['id'] ? 'selected' : '' }}>
                        {{ $category['title'] }}
                    </option>
                @endforeach
            </select>

            {{-- Descriptions --}}
            <label for="short_description" class="text-sm">Short Description (Optional)</label>
            <textarea name="short_description" id="short_description" class="textarea textarea-sm" rows="4">{{ old('short_description', $edit_product['short_description'] ?? '') }}</textarea>

            <label for="long_description" class="text-sm">Long Description (Optional)</label>
            <textarea name="long_description" id="long_description" class="textarea textarea-sm" rows="10">{{ old('long_description', $edit_product['long_description'] ?? '') }}</textarea>

            <button type="submit" class="btn btn-primary w-fit">
                {{ isset($edit_product) ? 'Update Product' : 'Add Product' }}
            </button>
        </form>
    </div>
@endsection

@push('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('add-gallery-row').addEventListener('click', function() {
                const galleryInputBox = document.getElementById('gallery-input-box');
                const index = galleryInputBox.children.length;
                const div = document.createElement('div');
                div.className = " flex justify-center items-center gap-2";
                div.innerHTML = `
                    <input type="text" name="image_gallery[${index}][label]" placeholder="Title" class="w-25 input input-sm" required>
                    <input type="file" name="image_gallery[${index}][image]" class=" file-input file-input-sm" required>
                    <button type="button" class="btn btn-sm btn-circle remove-gallery-row">✕</button>
                `;
                galleryInputBox.appendChild(div);
            });

            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-gallery-row')) {
                    e.target.parentElement.remove();
                }
            });
        });
    </script>
@endpush
