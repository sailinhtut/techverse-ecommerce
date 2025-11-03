@extends('layouts.admin.admin_dashboard')

@section('admin_dashboard_content')
    <div class="p-5 min-h-screen">
        <p class="lg:text-lg font-semibold mb-3">Product Categories</p>

        <button class="btn btn-primary" onclick="create_category_modal.showModal()">Create Category</button>

        <div class="card shadow-sm border border-base-300 mt-4">
            <div class="card-body p-0 m-0 overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Name</th>
                            <th>Parent</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($product_categories as $category)
                            <tr>
                                {{-- <td>{{ $loop->iteration + ($product_categories->currentPage() - 1) * $product_categories->perPage() }}
                                </td> --}}
                                <td>{{ $loop->iteration }}.
                                </td>
                               
                                <td>
                                    <p onclick="document.getElementById('detail_modal_{{ $category['id'] }}').showModal()"
                                        class="cursor-pointer hover:underline">{{ $category['name'] }}</p>
                                </td>
                                 <td>
                                    <p>{{ $category['parent']['name'] ?? '-' }}</p>
                                </td>
                                <td>{{ $category['description'] ?? '-' }}</td>
                                <td>
                                    <div tabindex="0" role="button" class="dropdown dropdown-left">
                                        <div class="btn btn-square btn-sm btn-ghost">
                                            <i data-lucide="ellipsis-vertical" class="size-5"></i>
                                        </div>
                                        <ul tabindex="0"
                                            class="menu dropdown-content bg-base-100 border border-base-300 w-30 rounded-box p-1 shadow-sm">
                                            <li><button
                                                    onclick="document.getElementById('detail_modal_{{ $category['id'] }}').showModal()">View</button>
                                            </li>
                                            <li><button
                                                    onclick="document.getElementById('edit_modal_{{ $category['id'] }}').showModal()">Edit</button>
                                            </li>
                                            <li><button class="text-error"
                                                    onclick="document.getElementById('delete_modal_{{ $category['id'] }}').showModal()">Delete</button>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>

                            {{-- Detail Modal --}}
                            <dialog id="detail_modal_{{ $category['id'] }}" class="modal">
                                <div class="modal-box max-w-3xl max-h-[85vh] overflow-y-auto">
                                    <form method="dialog">
                                        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                    </form>
                                    <h3 class="text-lg font-semibold text-center mb-3">Category Details</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <div>
                                            <label class="text-sm">ID</label>
                                            <input type="text"
                                                class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                                value="{{ $category['id'] }}" readonly>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="text-sm">Name</label>
                                            <input type="text"
                                                class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                                value="{{ $category['name'] }}" readonly>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="text-sm">Parent Category</label>
                                            <input type="text"
                                                class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                                value="{{ $category['parent']['name'] ?? '-' }}" readonly>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="text-sm">Description</label>
                                            <textarea class="textarea w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                                readonly>{{ $category['description'] }}</textarea>
                                        </div>
                                    </div>
                                    <div class="modal-action">
                                        <form method="dialog"><button class="btn">Close</button></form>
                                    </div>
                                </div>
                            </dialog>

                            @php
                                $excludeIds = [];
                                $addDescendants = function ($category) use (&$addDescendants, &$excludeIds) {
                                    if (!empty($category['children'])) {
                                        foreach ($category['children'] as $child) {
                                            $excludeIds[] = $child['id'];
                                            $addDescendants($child);
                                        }
                                    }
                                };

                                $excludeIds[] = $category['id'];
                                $addDescendants($category);
                            @endphp

                            {{-- Edit Modal --}}
                            <dialog id="edit_modal_{{ $category['id'] }}" class="modal">
                                <div class="modal-box max-w-3xl max-h-[85vh] overflow-y-auto">
                                    <form method="dialog">
                                        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                    </form>
                                    <h3 class="text-lg font-semibold text-center mb-3">Edit Category</h3>
                                    <form method="POST"
                                        action="{{ route('admin.dashboard.product.category.id.post', ['id' => $category['id']]) }}">
                                        @csrf
                                        @method('POST')

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                            <div class="md:col-span-2">
                                                <label class="text-sm">Name</label>
                                                <input name="name" class="input w-full" value="{{ $category['name'] }}"
                                                    required>
                                            </div>

                                            <div>
                                                <label class="text-sm">Parent Category</label>
                                                <select name="parent_id" class="select w-full border-base-300">
                                                    <div class="max-h-[200px] overflow-y-auto">
                                                        <option value="">-- None --</option>
                                                        @foreach ($product_categories as $cat)
                                                            @if (!in_array($cat['id'], $excludeIds))
                                                                <option value="{{ $cat['id'] }}"
                                                                    @if (isset($category) && $category['parent_id'] == $cat['id']) selected @endif>
                                                                    {{ $cat['name'] }}
                                                                </option>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </select>
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="text-sm">Description</label>
                                                <textarea name="description" class="textarea w-full">{{ $category['description'] }}</textarea>
                                            </div>
                                        </div>
                                        <div class="modal-action mt-3">
                                            <button type="submit" class="btn btn-primary">Update Category</button>
                                        </div>
                                    </form>
                                </div>
                            </dialog>

                            {{-- Delete Modal --}}
                            <dialog id="delete_modal_{{ $category['id'] }}" class="modal">
                                <div class="modal-box">
                                    <form method="dialog"><button
                                            class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button></form>
                                    <p class="text-lg font-semibold">Confirm Delete</p>
                                    <p class="text-sm mb-4">Are you sure you want to delete <span
                                            class="text-error">{{ $category['name'] }}</span>?</p>
                                    <div class="modal-action">
                                        <form method="dialog"><button class="btn">Cancel</button></form>
                                        <form method="POST"
                                            action="{{ route('admin.dashboard.product.category.id.delete', ['id' => $category['id']]) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-error">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </dialog>
                        @endforeach
                    </tbody>
                </table>

                {{-- Pagination --}}
                {{-- <div class="flex justify-between items-center py-3 px-5">
                    <div class="text-sm text-gray-500">
                        <span class="font-semibold">{{ $product_categories->firstItem() }}</span> –
                        <span class="font-semibold">{{ $product_categories->lastItem() }}</span> of
                        <span class="font-semibold">{{ $product_categories->total() }}</span> results
                    </div>
                    <div class="join">
                        @if ($product_categories->onFirstPage())
                            <button class="join-item btn btn-sm btn-disabled">«</button>
                        @else
                            <a href="{{ $product_categories->previousPageUrl() }}" class="join-item btn btn-sm">«</a>
                        @endif
                        @for ($i = 1; $i <= $product_categories->lastPage(); $i++)
                            <a href="{{ $product_categories->url($i) }}"
                                class="join-item btn btn-sm {{ $product_categories->currentPage() === $i ? 'btn-active' : '' }}">{{ $i }}</a>
                        @endfor
                        @if ($product_categories->hasMorePages())
                            <a href="{{ $product_categories->nextPageUrl() }}" class="join-item btn btn-sm">»</a>
                        @else
                            <button class="join-item btn btn-sm btn-disabled">»</button>
                        @endif
                    </div>
                </div> --}}
            </div>
        </div>

        {{-- Create Modal --}}
        <dialog id="create_category_modal" class="modal">
            <div class="modal-box max-w-3xl max-h-[85vh] overflow-y-auto">
                <form method="dialog">
                    <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                </form>
                <h3 class="text-lg font-semibold text-center mb-3">Create Category</h3>
                <form method="POST" action="{{ route('admin.dashboard.product.category.post') }}">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div class="md:col-span-2">
                            <label class="text-sm">Name</label>
                            <input name="name" class="input w-full" placeholder="Category Name" required>
                        </div>
                        <div>
                            <label class="text-sm">Parent Category</label>
                            <select name="parent_id" class="select w-full border-base-300 ">
                                <div class="max-h-[200px] overflow-y-auto">
                                    <option value="">-- None --</option>
                                    @foreach ($product_categories as $cat)
                                        <option value="{{ $cat['id'] }}">
                                            {{ $cat['name'] }}
                                        </option>
                                    @endforeach
                                </div>

                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-sm">Description</label>
                            <textarea name="description" class="textarea w-full"></textarea>
                        </div>
                    </div>
                    <div class="modal-action mt-3">
                        <button type="submit" class="btn btn-primary">Create Category</button>
                    </div>
                </form>
            </div>
        </dialog>
    </div>
@endsection
