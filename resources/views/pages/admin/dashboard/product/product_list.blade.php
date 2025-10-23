@extends('layouts.admin.admin_dashboard')
@section('admin_dashboard_content')
    <div class="p-5 min-h-screen">

        <p class="lg:text-lg font-semibold mb-3">Product List</p>

        <a href="{{ route('admin.dashboard.product.add.get') }}" class="btn btn-primary mb-3">Add Product</a>

        <div class="card shadow-sm border border-base-300">
            <div class="card-body p-0 m-0 overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="w-[50px]">No.</th>
                            <th class="w-[50px]">Image</th>
                            <th class="w-[200px]">name</th>
                            <th>Stock</th>
                            <th>Price</th>
                            <th>Sale</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                            <tr>
                                <td>
                                    {{ $loop->iteration + ($products->currentPage() - 1) * $products->perPage() }}.
                                </td>
                                <td class="w-[50px]">

                                    @if ($product['image'])
                                        <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}"
                                            class="w-[20px] h-auto object-contain">
                                    @else
                                        <img src="{{ asset(config('app.app_logo_bare_path')) }}"
                                            alt="{{ $product['name'] }}" class="w-[30px] h-auto">
                                    @endif
                                </td>
                                <td class="w-[200px] h-[30px] line-clamp-1">
                                    <div onclick="document.getElementById('detail_modal_{{ $product['id'] }}').showModal()"
                                        class="cursor-default hover:underline">{{ $product['name'] }}</div>
                                </td>
                                <td>{{ $product['stock'] ?? 'Non Stock' }}</td>
                                <td>{{ $product['regular_price'] ?? '-' }}</td>
                                <td>{{ $product['sale_price'] ?? '-' }}</td>
                                <td>
                                    <div tabindex="0" role="button" class="dropdown dropdown-left">
                                        <div class="btn btn-square btn-sm btn-ghost">
                                            <i data-lucide="ellipsis-vertical" class="size-5"></i>
                                        </div>
                                        <ul tabindex="0"
                                            class="menu dropdown-content bg-base-100 border border-base-300 w-30 rounded-box p-1 shadow-sm">
                                            <li>
                                                <button
                                                    onclick="document.getElementById('detail_modal_{{ $product['id'] }}').showModal()">
                                                    View
                                                </button>
                                            </li>
                                            <li>
                                                <a
                                                    href="{{ route('admin.dashboard.product.edit.id.get', ['id' => $product['id']]) }}">Edit</a>
                                            </li>
                                            <li>
                                                <button type="button" class="text-error"
                                                    onclick="document.getElementById('deleteModal{{ $product['id'] }}').showModal()">
                                                    Delete
                                                </button>
                                            </li>
                                        </ul>
                                    </div>


                                    {{-- <dialog id="detailModal{{ $product['id'] }}" class="modal">
                                        <div class="modal-box max-h-[80vh]">
                                            <form method="dialog">
                                                <button
                                                    class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                            </form>
                                            <p class="text-lg font-semibold py-0">{{ $product['name'] }}</p>
                                            <div class="mt-4 space-y-2">
                                                <p><strong>ID:</strong> {{ $product['id'] ?? 'No ID' }}</p>
                                                <p><strong>Status:</strong>
                                                    {{ $product['is_active'] ? 'Live' : 'Disable' }}</p>
                                                <p><strong>Slug:</strong> {{ $product['slug'] ?? 'No Slug' }}
                                                </p>
                                                <p><strong>Short Description:</strong>
                                                    {{ $product['short_description'] ?? 'No Description' }}</p>
                                                <p><strong>Long Description:</strong>
                                                    {{ $product['long_description'] ?? 'No Description' }}</p>
                                                <p><strong>SKU (Stock Keeping Unit):</strong>
                                                    {{ $product['sku'] ?? 'Not Set' }}</p>
                                                <p><strong>Regular Price:</strong>
                                                    {{ $product['regular_price'] ?? 'No Price Set' }}</p>
                                                <p><strong>Regular Price:</strong>
                                                    {{ $product['regular_price'] ?? 'No Price Set' }}</p>
                                                <p><strong>Sale Price:</strong>
                                                    {{ $product['sale_price'] ?? 'No Price Set' }}</p>
                                                <p><strong>Stock:</strong>
                                                    {{ $product['stock'] ?? 'No Stock Set' }}</p>
                                                <p><strong>Stock Status:</strong>
                                                    {{ $product['enable_stock'] ? 'Enable' : 'Disable' }}</p>
                                                <p>
                                                    <strong>Category ID:</strong>
                                                    {{ $product['category_id'] ?? 'No Category Set' }}
                                                </p>
                                                <p>
                                                    <strong>Category:</strong>
                                                    {{ isset($product['category']) ? $product['category']['name'] : '' }}
                                                </p>

                                                <!-- Main Image -->
                                                <p><strong>Image:</strong></p>
                                                @if ($product['image'])
                                                    <img src="{{ $product['image'] }}" alt="Product Image"
                                                        class="rounded-lg max-w-[120px]">
                                                @else
                                                    <span>No Image Set</span>
                                                @endif

                                                <!-- Image Gallery -->
                                                <p><strong>Image Gallery:</strong></p>
                                                @if (!empty($product['image_gallery']))
                                                    <div class="flex flex-wrap gap-2">
                                                        @foreach ($product['image_gallery'] as $img)
                                                            <div class="tooltip" data-tip="{{ $img['label'] }}">
                                                                <img src="{{ $img['image'] }}"
                                                                    alt="{{ $img['label'] ?? 'Gallery Image' }}"
                                                                    class="object-cover w-[100px] h-[100px] rounded-lg"
                                                                    name="{{ $img['label'] ?? '' }}">
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <span>No Images in Gallery</span>
                                                @endif
                                            </div>
                                            <div class="modal-action mt-3">
                                                <form method="dialog">
                                                    <button class="btn lg:btn-md">Close</button>
                                                </form>
                                            </div>
                                        </div>

                                    </dialog> --}}

                                    <dialog id="detail_modal_{{ $product['id'] }}" class="modal">
                                        <div class="modal-box max-w-3xl max-h-[85vh] overflow-y-auto">
                                            <form method="dialog">
                                                <button
                                                    class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                            </form>

                                            <h3 class="text-lg font-semibold text-center mb-4">
                                                {{ $product['name'] }}
                                            </h3>

                                            {{-- Basic Product Info --}}
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm mb-4">
                                                <div>
                                                    <label class="text-sm">Product ID</label>
                                                    <input type="text" value="{{ $product['id'] }}" readonly
                                                        class="input w-full cursor-default select-none focus:outline-none focus:ring-0 focus:border-base-300">
                                                </div>
                                                <div>
                                                    <label class="text-sm">Slug</label>
                                                    <input type="text" value="{{ $product['slug'] ?? '-' }}" readonly
                                                        class="input w-full cursor-default select-none focus:outline-none focus:ring-0 focus:border-base-300">
                                                </div>
                                                <div>
                                                    <label class="text-sm">SKU</label>
                                                    <input type="text" value="{{ $product['sku'] ?? '-' }}" readonly
                                                        class="input w-full cursor-default select-none focus:outline-none focus:ring-0 focus:border-base-300">
                                                </div>
                                                <div>
                                                    <label class="text-sm">Status</label>
                                                    <input type="text"
                                                        value="{{ $product['is_active'] ? 'Live' : 'Disabled' }}" readonly
                                                        class="input w-full cursor-default select-none focus:outline-none focus:ring-0 focus:border-base-300">
                                                </div>
                                                <div>
                                                    <label class="text-sm">Product Type</label>
                                                    <input type="text"
                                                        value="{{ ucfirst($product['product_type'] ?? '-') }}" readonly
                                                        class="input w-full cursor-default select-none focus:outline-none focus:ring-0 focus:border-base-300">
                                                </div>
                                                <div>
                                                    <label class="text-sm">Stock</label>
                                                    <input type="text" value="{{ $product['stock'] ?? 'N/A' }}" readonly
                                                        class="input w-full cursor-default select-none focus:outline-none focus:ring-0 focus:border-base-300">
                                                </div>
                                                <div>
                                                    <label class="text-sm">Stock Status</label>
                                                    <input type="text"
                                                        value="{{ $product['enable_stock'] ? 'Enabled' : 'Disabled' }}"
                                                        readonly
                                                        class="input w-full cursor-default select-none focus:outline-none focus:ring-0 focus:border-base-300">
                                                </div>
                                                <div>
                                                    <label class="text-sm">Regular Price</label>
                                                    <input type="text"
                                                        value="${{ number_format($product['regular_price'], 2) }}" readonly
                                                        class="input w-full cursor-default select-none focus:outline-none focus:ring-0 focus-border-base-300">
                                                </div>
                                                <div>
                                                    <label class="text-sm">Sale Price</label>
                                                    <input type="text"
                                                        value="${{ number_format($product['sale_price'], 2) }}" readonly
                                                        class="input w-full cursor-default select-none focus:outline-none focus:ring-0 focus-border-base-300">
                                                </div>
                                            </div>


                                            {{-- Category & Brand --}}
                                            <p class="font-semibold mb-2">Category & Brand</p>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                                                <div>
                                                    <label class="text-sm">Category</label>
                                                    <input type="text"
                                                        value="{{ $product['category']['name'] ?? 'No Category Set' }}"
                                                        readonly
                                                        class="input w-full cursor-default select-none focus:outline-none focus:ring-0 focus:border-base-300">
                                                </div>
                                                <div>
                                                    <label class="text-sm">Brand</label>
                                                    <input type="text"
                                                        value="{{ $product['brand']['name'] ?? 'No Brand Set' }}" readonly
                                                        class="input w-full cursor-default select-none focus:outline-none focus:ring-0 focus:border-base-300">
                                                </div>
                                            </div>


                                            {{-- Shipping & Tax Classes --}}
                                            <p class="font-semibold mb-2">Shipping & Tax Classes</p>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                                                <div>
                                                    <label class="text-sm">Shipping Class</label>
                                                    <input type="text"
                                                        value="{{ $product['shipping_class']['name'] ?? 'No Shipping Class' }}"
                                                        readonly
                                                        class="input w-full cursor-default select-none focus:outline-none focus:ring-0 focus:border-base-300">
                                                </div>
                                                <div>
                                                    <label class="text-sm">Tax Class</label>
                                                    <input type="text"
                                                        value="{{ $product['tax_class']['name'] ?? 'No Tax Class' }}"
                                                        readonly
                                                        class="input w-full cursor-default select-none focus:outline-none focus:ring-0 focus:border-base-300">
                                                </div>
                                            </div>


                                            {{-- Descriptions --}}
                                            <p class="font-semibold mb-2">Descriptions</p>
                                            <div class="space-y-2 mb-3">
                                                <div>
                                                    <label class="text-sm">Short Description</label>
                                                    <textarea readonly
                                                        class="textarea textarea-bordered w-full min-h-[60px] focus:outline-none focus:ring-0 focus:border-base-300">{{ $product['short_description'] ?? '-' }}</textarea>
                                                </div>
                                                <div>
                                                    <label class="text-sm">Long Description</label>
                                                    <textarea readonly
                                                        class="textarea textarea-bordered w-full min-h-[100px] focus:outline-none focus:ring-0 focus:border-base-300">{{ $product['long_description'] ?? '-' }}</textarea>
                                                </div>
                                            </div>


                                            {{-- Images --}}
                                            <p class="font-semibold mb-2">Images</p>
                                            <div class="flex flex-wrap gap-3 mb-3">
                                                @if ($product['image'])
                                                    <div class="tooltip" data-tip="Main Image">
                                                        <img src="{{ $product['image'] }}" alt="Main Image"
                                                            class="rounded-lg w-[100px] h-[100px] object-cover">
                                                    </div>
                                                @endif

                                                @if (!empty($product['image_gallery']))
                                                    @foreach ($product['image_gallery'] as $img)
                                                        <div class="tooltip"
                                                            data-tip="{{ $img['label'] ?? 'Gallery Image' }}">
                                                            <img src="{{ $img['image'] }}"
                                                                alt="{{ $img['label'] ?? 'Gallery Image' }}"
                                                                class="rounded-lg w-[100px] h-[100px] object-cover">
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <p class="text-gray-500 italic">No gallery images available.</p>
                                                @endif
                                            </div>


                                            {{-- Payment Methods --}}
                                            <p class="font-semibold mb-2">Payment Methods</p>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                                                @forelse ($product['payment_methods'] ?? [] as $method)
                                                    <div class="bg-base-200 rounded-box p-3">
                                                        <p class="font-medium">{{ $method['name'] }}</p>
                                                        <p class="text-xs text-gray-600">
                                                            {{ $method['description'] ?? '-' }}</p>
                                                    </div>
                                                @empty
                                                    <div class="md:col-span-2 text-gray-500 italic">No payment methods
                                                        linked.</div>
                                                @endforelse
                                            </div>

                                            <div class="modal-action mt-6">
                                                <form method="dialog">
                                                    <button class="btn btn-primary w-full">Close</button>
                                                </form>
                                            </div>
                                        </div>
                                    </dialog>




                                    <dialog id="deleteModal{{ $product['id'] }}" class="modal">
                                        <div class="modal-box">
                                            <form method="dialog">
                                                <button
                                                    class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                            </form>
                                            <p class="text-lg font-semibold py-0">Confirm Delete</p>

                                            <p class="py-2 mb-0 text-sm">
                                                Are you sure you want to delete
                                                <span class="italic text-error">{{ $product['name'] }}</span> ?
                                            </p>
                                            <div class="modal-action mt-0">
                                                <form method="dialog">
                                                    <button class="btn lg:btn-md">Close</button>
                                                </form>
                                                <form method="POST"
                                                    action="{{ route('admin.dashboard.product.id.delete', ['id' => $product['id']]) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn lg:btn-md btn-error">Delete</button>
                                                </form>
                                            </div>
                                        </div>
                                    </dialog>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-3 py-3 px-5">
                    <div class="text-sm text-gray-500">
                        <span class="font-semibold">{{ $products->firstItem() }}</span>
                        –
                        <span class="font-semibold">{{ $products->lastItem() }}</span>
                        of
                        <span class="font-semibold">{{ $products->total() }}</span>
                        results
                    </div>

                    <div class="join">
                        @if ($products->onFirstPage())
                            <button class="join-item btn btn-sm btn-disabled">«</button>
                        @else
                            <a href="{{ $products->previousPageUrl() }}" class="join-item btn btn-sm">«</a>
                        @endif

                        <a href="{{ $products->url(1) }}"
                            class="join-item btn btn-sm {{ $products->currentPage() === 1 ? 'btn-active' : '' }}">
                            1
                        </a>

                        @php
                            $start = max(2, $products->currentPage() - 1);
                            $end = min($products->lastPage() - 1, $products->currentPage() + 1);
                        @endphp

                        @for ($i = $start; $i <= $end; $i++)
                            <a href="{{ $products->url($i) }}"
                                class="join-item btn btn-sm {{ $products->currentPage() === $i ? 'btn-active' : '' }}">
                                {{ $i }}
                            </a>
                        @endfor

                        @if ($products->lastPage() > 1)
                            <a href="{{ $products->url($products->lastPage()) }}"
                                class="join-item btn btn-sm {{ $products->currentPage() === $products->lastPage() ? 'btn-active' : '' }}">
                                {{ $products->lastPage() }}
                            </a>
                        @endif

                        @if ($products->hasMorePages())
                            <a href="{{ $products->nextPageUrl() }}" class="join-item btn btn-sm">»</a>
                        @else
                            <button class="join-item btn btn-sm btn-disabled">»</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
