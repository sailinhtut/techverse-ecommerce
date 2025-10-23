@extends('layouts.admin.admin_dashboard')

@section('admin_dashboard_content')
    <div class="p-5 min-h-screen">
        <p class="lg:text-lg font-semibold mb-3">Product Attributes</p>

        <button class="btn btn-primary" onclick="create_attribute_modal.showModal()">Create Attribute</button>

        <div class="card shadow-sm border border-base-300 mt-4">
            <div class="card-body p-0 m-0 overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Name</th>
                            <th>Values</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($attributes as $attribute)
                            <tr>
                                <td>{{ $loop->iteration + ($attributes->currentPage() - 1) * $attributes->perPage() }}</td>
                                <td>
                                    <p onclick="document.getElementById('detail_modal_{{ $attribute['id'] }}').showModal()"
                                        class="cursor-pointer hover:underline">{{ $attribute['name'] }}</p>
                                </td>
                                <td>{{ implode(', ', explode(',', $attribute['values'])) }}</td>
                                <td>
                                    <div tabindex="0" role="button" class="dropdown dropdown-left">
                                        <div class="btn btn-square btn-sm btn-ghost">
                                            <i data-lucide="ellipsis-vertical" class="size-5"></i>
                                        </div>
                                        <ul tabindex="0"
                                            class="menu dropdown-content bg-base-100 border border-base-300 w-30 rounded-box p-1 shadow-sm">
                                            <li>
                                                <button
                                                    onclick="document.getElementById('detail_modal_{{ $attribute['id'] }}').showModal()">View</button>
                                            </li>
                                            <li>
                                                <button
                                                    onclick="document.getElementById('edit_modal_{{ $attribute['id'] }}').showModal()">Edit</button>
                                            </li>
                                            <li>
                                                <button class="text-error"
                                                    onclick="document.getElementById('delete_modal_{{ $attribute['id'] }}').showModal()">Delete</button>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>

                            <dialog id="detail_modal_{{ $attribute['id'] }}" class="modal">
                                <div class="modal-box max-w-xl">
                                    <form method="dialog">
                                        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                    </form>
                                    <h3 class="text-lg font-semibold text-center mb-3">Attribute Details</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <div>
                                            <label class="text-sm">ID</label>
                                            <input type="text"
                                                class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                                value="{{ $attribute['id'] }}" readonly>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="text-sm">Name</label>
                                            <input type="text"
                                                class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                                value="{{ $attribute['name'] }}" readonly>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="text-sm">Values</label>
                                            <textarea class="textarea w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                                readonly>{{ implode(', ', explode(',', $attribute['values'])) }}</textarea>
                                        </div>
                                    </div>
                                    <div class="modal-action">
                                        <form method="dialog"><button class="btn">Close</button></form>
                                    </div>
                                </div>
                            </dialog>

                            <dialog id="edit_modal_{{ $attribute['id'] }}" class="modal">
                                <div class="modal-box max-w-xl">
                                    <form method="dialog">
                                        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                    </form>
                                    <h3 class="text-lg font-semibold text-center mb-3">Edit Attribute</h3>
                                    <form method="POST"
                                        action="{{ route('admin.dashboard.product.attribute.id.post', ['id' => $attribute['id']]) }}">
                                        @csrf
                                        @method('POST')
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                            <div class="md:col-span-2">
                                                <label class="text-sm">Name</label>
                                                <input name="name" class="input w-full" value="{{ $attribute['name'] }}"
                                                    required>
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="text-sm">Values (comma separated)</label>
                                                <textarea name="values" class="textarea w-full">{{ implode(', ', explode(',', $attribute['values'])) }}</textarea>
                                            </div>
                                        </div>
                                        <div class="modal-action mt-3">
                                            <button type="submit" class="btn btn-primary">Update Attribute</button>
                                        </div>
                                    </form>
                                </div>
                            </dialog>

                            <dialog id="delete_modal_{{ $attribute['id'] }}" class="modal">
                                <div class="modal-box">
                                    <form method="dialog"><button
                                            class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button></form>
                                    <p class="text-lg font-semibold">Confirm Delete</p>
                                    <p class="text-sm mb-4">Are you sure you want to delete <span
                                            class="text-error">{{ $attribute['name'] }}</span>?</p>
                                    <div class="modal-action">
                                        <form method="dialog"><button class="btn">Cancel</button></form>
                                        <form method="POST"
                                            action="{{ route('admin.dashboard.product.attribute.id.delete', ['id' => $attribute['id']]) }}">
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

                <div class="flex justify-between items-center py-3 px-5">
                    <div class="text-sm text-gray-500">
                        <span class="font-semibold">{{ $attributes->firstItem() }}</span> –
                        <span class="font-semibold">{{ $attributes->lastItem() }}</span> of
                        <span class="font-semibold">{{ $attributes->total() }}</span> results
                    </div>
                    <div class="join">
                        @if ($attributes->onFirstPage())
                            <button class="join-item btn btn-sm btn-disabled">«</button>
                        @else
                            <a href="{{ $attributes->previousPageUrl() }}" class="join-item btn btn-sm">«</a>
                        @endif
                        @for ($i = 1; $i <= $attributes->lastPage(); $i++)
                            <a href="{{ $attributes->url($i) }}"
                                class="join-item btn btn-sm {{ $attributes->currentPage() === $i ? 'btn-active' : '' }}">{{ $i }}</a>
                        @endfor
                        @if ($attributes->hasMorePages())
                            <a href="{{ $attributes->nextPageUrl() }}" class="join-item btn btn-sm">»</a>
                        @else
                            <button class="join-item btn btn-sm btn-disabled">»</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <dialog id="create_attribute_modal" class="modal">
            <div class="modal-box max-w-xl">
                <form method="dialog">
                    <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                </form>
                <h3 class="text-lg font-semibold text-center mb-3">Create Attribute</h3>
                <form method="POST" action="{{ route('admin.dashboard.product.attribute.post') }}">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div class="md:col-span-2">
                            <label class="text-sm">Name</label>
                            <input name="name" class="input w-full" placeholder="Attribute Name" required>
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-sm">Values (comma separated)</label>
                            <textarea name="values" class="textarea w-full"></textarea>
                        </div>
                    </div>
                    <div class="modal-action mt-3">
                        <button type="submit" class="btn btn-primary">Create Attribute</button>
                    </div>
                </form>
            </div>
        </dialog>
    </div>
@endsection
