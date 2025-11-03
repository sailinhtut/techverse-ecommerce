@extends('layouts.admin.admin_dashboard')

@section('admin_dashboard_content')
    <div class="p-5 min-h-screen">
        <p class="lg:text-lg font-semibold mb-3">Shipping Methods</p>

        <button class="btn btn-primary" onclick="create_shipping_method_modal.showModal()">Create Shipping Method</button>

        <div class="card shadow-sm border border-base-300 mt-4">
            <div class="card-body p-0 m-0 overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Enabled</th>
                            <th>Pricing</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($shipping_methods as $method)
                            <tr>
                                <td>{{ $loop->iteration + ($shipping_methods->currentPage() - 1) * $shipping_methods->perPage() }}
                                </td>
                                <td>
                                    <p onclick="document.getElementById('detail_modal_{{ $method['id'] }}').showModal()"
                                        class="cursor-pointer hover:underline">{{ $method['name'] }}</p>
                                </td>
                                <td>{{ $method['description'] ?? '-' }}</td>
                                <td>{{ $method['enabled'] ? 'Enabled' : 'Disabled' }}</td>
                                <td>{{ $method['is_free'] ? 'Free' : 'Cost' }}</td>
                                <td>
                                    <div tabindex="0" role="button" class="dropdown dropdown-left">
                                        <div class="btn btn-square btn-sm btn-ghost">
                                            <i data-lucide="ellipsis-vertical" class="size-5"></i>
                                        </div>
                                        <ul tabindex="0"
                                            class="menu dropdown-content bg-base-100 border border-base-300 w-30 rounded-box p-1 shadow-sm">
                                            <li><button
                                                    onclick="document.getElementById('detail_modal_{{ $method['id'] }}').showModal()">View</button>
                                            </li>
                                            <li><button
                                                    onclick="document.getElementById('edit_modal_{{ $method['id'] }}').showModal()">Edit</button>
                                            </li>
                                            <li><button class="text-error"
                                                    onclick="document.getElementById('delete_modal_{{ $method['id'] }}').showModal()">Delete</button>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>

                            <dialog id="detail_modal_{{ $method['id'] }}" class="modal">
                                <div class="modal-box max-w-3xl max-h-[85vh] overflow-y-auto">
                                    <form method="dialog">
                                        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                    </form>
                                    <h3 class="text-lg font-semibold text-center mb-3">Shipping Method Details</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <div>
                                            <label class="text-sm">ID</label>
                                            <input type="text"
                                                class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                                value="{{ $method['id'] }}" readonly>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="text-sm">Name</label>
                                            <input type="text"
                                                class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                                value="{{ $method['name'] }}" readonly>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="text-sm">Description</label>
                                            <textarea class="textarea w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                                readonly>{{ $method['description'] }}</textarea>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="text-sm">Enabled</label>
                                            <input type="text"
                                                class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                                value="{{ $method['enabled'] ? 'Enabled' : 'Disabled' }}" readonly>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="text-sm">Is Free Shipping</label>
                                            <input type="text"
                                                class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                                value="{{ $method['is_free'] ? 'Free Shipping' : 'Cost Shipping' }}"
                                                readonly>
                                        </div>
                                    </div>
                                    <div class="modal-action">
                                        <form method="dialog" class="w-full"><button class="btn">Close</button>
                                        </form>
                                    </div>
                                </div>
                            </dialog>

                            {{-- Edit Modal --}}
                            <dialog id="edit_modal_{{ $method['id'] }}" class="modal">
                                <div class="modal-box max-w-3xl max-h-[85vh] overflow-y-auto">
                                    <form method="dialog">
                                        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                    </form>
                                    <h3 class="text-lg font-semibold text-center mb-3">Edit Shipping Method</h3>

                                    <form method="POST"
                                        action="{{ route('admin.dashboard.shipping.shipping-method.id.post', ['id' => $method['id']]) }}">
                                        @csrf
                                        @method('POST')
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                            <div class="md:col-span-2">
                                                <label class="text-sm">Name</label>
                                                <input name="name" class="input w-full" value="{{ $method['name'] }}"
                                                    required>
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="text-sm">Description</label>
                                                <textarea name="description" class="textarea w-full">{{ $method['description'] }}</textarea>
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="label cursor-pointer mt-1.5">
                                                    <input type="hidden" name="enabled" value="0">
                                                    <input type="checkbox" name="enabled" value="1"
                                                        class="checkbox checkbox-sm" @checked($method['enabled']) />
                                                    Enabled
                                                </label>
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="label cursor-pointer mt-1.5">
                                                    <input type="hidden" name="is_free" value="0">
                                                    <input type="checkbox" name="is_free" value="1"
                                                        class="checkbox checkbox-sm" @checked($method['is_free']) />
                                                    Is Free Shipping
                                                </label>
                                            </div>
                                        </div>
                                        <div class="modal-action mt-3">
                                            <button type="submit" class="btn btn-primary">Update Shipping
                                                Method</button>

                                        </div>
                                    </form>
                                </div>
                            </dialog>

                            {{-- Delete Modal --}}
                            <dialog id="delete_modal_{{ $method['id'] }}" class="modal">
                                <div class="modal-box">
                                    <form method="dialog"><button
                                            class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button></form>
                                    <p class="text-lg font-semibold">Confirm Delete</p>
                                    <p class="text-sm mb-4">Are you sure you want to delete <span
                                            class="text-error">{{ $method['name'] }}</span>?</p>
                                    <div class="modal-action">
                                        <button type="button" class="btn"
                                            onclick="delete_modal_{{ $method['id'] }}.close()">Cancel</button>
                                        <form method="POST"
                                            action="{{ route('admin.dashboard.shipping.shipping-method.id.delete', ['id' => $method['id']]) }}">
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
                <div class="flex justify-between items-center py-3 px-5">
                    <div class="text-sm text-gray-500">
                        <span class="font-semibold">{{ $shipping_methods->firstItem() }}</span> –
                        <span class="font-semibold">{{ $shipping_methods->lastItem() }}</span> of
                        <span class="font-semibold">{{ $shipping_methods->total() }}</span> results
                    </div>
                    <div class="join">
                        @if ($shipping_methods->onFirstPage())
                            <button class="join-item btn btn-sm btn-disabled">«</button>
                        @else
                            <a href="{{ $shipping_methods->previousPageUrl() }}" class="join-item btn btn-sm">«</a>
                        @endif
                        @for ($i = 1; $i <= $shipping_methods->lastPage(); $i++)
                            <a href="{{ $shipping_methods->url($i) }}"
                                class="join-item btn btn-sm {{ $shipping_methods->currentPage() === $i ? 'btn-active' : '' }}">{{ $i }}</a>
                        @endfor
                        @if ($shipping_methods->hasMorePages())
                            <a href="{{ $shipping_methods->nextPageUrl() }}" class="join-item btn btn-sm">»</a>
                        @else
                            <button class="join-item btn btn-sm btn-disabled">»</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Create Modal --}}
        <dialog id="create_shipping_method_modal" class="modal">
            <div class="modal-box max-w-3xl max-h-[85vh] overflow-y-auto">
                <form method="dialog"><button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                </form>
                <h3 class="text-lg font-semibold text-center mb-3">Create Shipping Method</h3>
                <form method="POST" action="{{ route('admin.dashboard.shipping.shipping-method.post') }}">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div class="md:col-span-2">
                            <label class="text-sm">Name</label>
                            <input name="name" class="input w-full" placeholder="Method Name" required>
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-sm">Description</label>
                            <textarea name="description" class="textarea w-full"></textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label class="label cursor-pointer mt-1.5">
                                <input type="hidden" name="enabled" value="0">
                                <input type="checkbox" name="enabled" value="1" class="checkbox checkbox-sm"
                                    checked />
                                Enabled
                            </label>
                        </div>
                        <div class="md:col-span-2">
                            <label class="label cursor-pointer mt-1.5">
                                <input type="hidden" name="is_free" value="0">
                                <input type="checkbox" name="is_free" value="1" class="checkbox checkbox-sm"
                                    checked />
                                Is Free Shipping
                            </label>
                        </div>
                    </div>
                    <div class="modal-action mt-3">
                        <button type="submit" class="btn btn-primary">Create Shipping Method</button>
                    </div>
                </form>
            </div>
        </dialog>

    </div>
@endsection
