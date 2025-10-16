@extends('layouts.admin.admin_dashboard')
@section('admin_dashboard_content')
    <div class="p-5">
        <p class="lg:text-lg font-semibold mb-3">Category List</p>

        <div class="card shadow-sm border border-base-300">
            <div class="card-body p-0 m-0 overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="w-[50px]">No.</th>
                            <th class="w-[200px]">Title</th>
                            <th class="w-[200px]">Description</th>
                            <th style="width:180px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($product_categories as $category)
                            <tr>
                                <td style="" class="">
                                    {{ $loop->iteration + ($product_categories->currentPage() - 1) * $product_categories->perPage() }}.
                                </td>

                                <td class="w-[200px] h-[30px] line-clamp-1">
                                    <div onclick="document.getElementById('detailModal{{ $category['id'] }}').showModal()"
                                        class="cursor-default hover:underline">{{ $category['name'] }}</div>
                                </td>
                                <td>{{ $category['description'] ?? 'No Description' }}</td>
                                <td>
                                    <div tabindex="0" role="button" class="dropdown dropdown-left">
                                        <div class="btn btn-square btn-sm btn-ghost">
                                            <i data-lucide="ellipsis-vertical" class="size-5"></i>
                                        </div>
                                        <ul tabindex="0"
                                            class="menu dropdown-content bg-base-100 border border-base-300 w-30 rounded-box p-1 shadow-sm">
                                            <li>
                                                <button
                                                    onclick="document.getElementById('detailModal{{ $category['id'] }}').showModal()">
                                                    View
                                                </button>
                                            </li>
                                            <li>
                                                <a
                                                    href="{{ route('admin.dashboard.category.edit.id.get', ['id' => $category['id']]) }}">Edit</a>
                                            </li>
                                            <li>
                                                <button type="button" class="text-error"
                                                    onclick="document.getElementById('deleteModal{{ $category['id'] }}').showModal()">
                                                    Delete
                                                </button>
                                            </li>
                                        </ul>
                                    </div>


                                    <dialog id="detailModal{{ $category['id'] }}" class="modal">
                                        <div class="modal-box max-h-[80vh]">
                                            <form method="dialog">
                                                <button
                                                    class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                            </form>
                                            <p class="text-lg font-semibold py-0">{{ $category['name'] }}</p>
                                            <div class="mt-4 space-y-2">
                                                <p><strong>ID:</strong> {{ $category['id'] ?? 'No ID' }}</p>

                                                <p><strong>Title:</strong>
                                                    {{ $category['name'] ?? 'No Title' }}</p>
                                                <p><strong>Description:</strong>
                                                    {{ $category['description'] ?? 'Description' }}</p>
                                            </div>
                                            <div class="modal-action mt-3">
                                                <form method="dialog">
                                                    <button class="btn lg:btn-md">Close</button>
                                                </form>
                                            </div>
                                        </div>

                                    </dialog>


                                    <dialog id="deleteModal{{ $category['id'] }}" class="modal">
                                        <div class="modal-box">
                                            <form method="dialog">
                                                <button
                                                    class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                            </form>
                                            <p class="text-lg font-semibold py-0">Confirm Delete</p>

                                            <p class="py-2 mb-0 text-sm">
                                                Are you sure you want to delete
                                                <span class="italic text-error">{{ $category['name'] }}</span> ?
                                            </p>
                                            <div class="modal-action mt-0">
                                                <form method="dialog">
                                                    <button class="btn  lg:btn-md">Close</button>
                                                </form>
                                                <form method="POST"
                                                    action="{{ route('admin.dashboard.category.id.delete', ['id' => $category['id']]) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn  lg:btn-md btn-error">Delete</button>
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
                        <span class="font-semibold">{{ $product_categories->firstItem() }}</span>
                        –
                        <span class="font-semibold">{{ $product_categories->lastItem() }}</span>
                        of
                        <span class="font-semibold">{{ $product_categories->total() }}</span>
                        results
                    </div>

                    <div class="join">
                        @if ($product_categories->onFirstPage())
                            <button class="join-item btn btn-sm btn-disabled">«</button>
                        @else
                            <a href="{{ $product_categories->previousPageUrl() }}" class="join-item btn btn-sm">«</a>
                        @endif

                        <a href="{{ $product_categories->url(1) }}"
                            class="join-item btn btn-sm {{ $product_categories->currentPage() === 1 ? 'btn-active' : '' }}">
                            1
                        </a>

                        @php
                            $start = max(2, $product_categories->currentPage() - 1);
                            $end = min($product_categories->lastPage() - 1, $product_categories->currentPage() + 1);
                        @endphp

                        @for ($i = $start; $i <= $end; $i++)
                            <a href="{{ $product_categories->url($i) }}"
                                class="join-item btn btn-sm {{ $product_categories->currentPage() === $i ? 'btn-active' : '' }}">
                                {{ $i }}
                            </a>
                        @endfor

                        @if ($product_categories->lastPage() > 1)
                            <a href="{{ $product_categories->url($product_categories->lastPage()) }}"
                                class="join-item btn btn-sm {{ $product_categories->currentPage() === $product_categories->lastPage() ? 'btn-active' : '' }}">
                                {{ $product_categories->lastPage() }}
                            </a>
                        @endif

                        @if ($product_categories->hasMorePages())
                            <a href="{{ $product_categories->nextPageUrl() }}" class="join-item btn btn-sm">»</a>
                        @else
                            <button class="join-item btn btn-sm btn-disabled">»</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
