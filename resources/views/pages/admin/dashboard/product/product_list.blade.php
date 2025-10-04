@extends('layouts.admin.admin_dashboard')
@section('admin_dashboard_content')
    <div class="p-5 min-h-screen">

        <p class="lg:text-lg font-semibold mb-3">Product List</p>

        <div class="card shadow-sm border border-base-300">
            <div class="card-body p-0 m-0 overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="w-[50px]">No.</th>
                            <th class="w-[50px]">Image</th>
                            <th class="w-[200px]">Title</th>
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
                                        <img src="{{ $product['image'] }}" alt="{{ $product['title'] }}"
                                            class="w-[20px] h-auto object-contain">
                                    @else
                                        <img src="{{ asset('assets/images/techverse_green_logo.png') }}"
                                            alt="{{ $product['title'] }}" class="w-[30px] h-auto">
                                    @endif
                                </td>
                                <td class="w-[200px] h-[30px] line-clamp-1">
                                    <div onclick="document.getElementById('detailModal{{ $product['id'] }}').showModal()"
                                        class="cursor-default hover:underline">{{ $product['title'] }}</div>
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
                                                    onclick="document.getElementById('detailModal{{ $product['id'] }}').showModal()">
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


                                    <dialog id="detailModal{{ $product['id'] }}" class="modal">
                                        <div class="modal-box max-h-[80vh]">
                                            <form method="dialog">
                                                <button
                                                    class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                            </form>
                                            <p class="text-lg font-semibold py-0">{{ $product['title'] }}</p>
                                            <div class="mt-4 space-y-2">
                                                <p><strong>ID:</strong> {{ $product['id'] ?? 'No ID' }}</p>
                                                <p><strong>Slug:</strong> {{ $product['slug'] ?? 'No Slug' }}
                                                </p>
                                                <p><strong>Short Description:</strong>
                                                    {{ $product['short_description'] ?? 'No Description' }}</p>
                                                <p><strong>Long Description:</strong>
                                                    {{ $product['long_description'] ?? 'No Description' }}</p>
                                                <p><strong>Regular Price:</strong>
                                                    {{ $product['regular_price'] ?? 'No Price Set' }}</p>
                                                <p><strong>Sale Price:</strong>
                                                    {{ $product['sale_price'] ?? 'No Price Set' }}</p>
                                                <p><strong>Stock:</strong>
                                                    {{ $product['stock'] ?? 'No Stock Set' }}</p>
                                                <p><strong>Category ID:</strong>
                                                    {{ $product['category_id'] ?? 'No Category Set' }}</p>

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
                                                                    title="{{ $img['label'] ?? '' }}">
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
                                                <span class="italic text-error">{{ $product['title'] }}</span> ?
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
