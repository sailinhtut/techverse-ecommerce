@extends('layouts.admin.admin_dashboard')

@section('admin_dashboard_content')
    <div class="p-5 min-h-screen">
        <p class="lg:text-lg font-semibold mb-3">Store Branches</p>

        <div class="flex gap-3 mb-3">
            <button class="btn btn-primary btn-sm" onclick="document.getElementById('create_store_modal').showModal()">Create Store
                Branch</button>
        </div>

        <div class="card shadow-sm border border-base-300 mt-4">
            <div class="card-body p-0 m-0 overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Branch Name</th>
                            <th>Address</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Active</th>
                            <th>Open Time</th>
                            <th>Close Time</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($branches as $store)
                            <tr>
                                <td>{{ $loop->iteration + ($branches->currentPage() - 1) * $branches->perPage() }}</td>
                                <td>
                                    <p onclick="document.getElementById('detail_modal_{{ $store['id'] }}').showModal()"
                                        class="cursor-pointer hover:underline">
                                        {{ $store['name'] ?? '-' }}
                                    </p>
                                </td>
                                <td class="max-w-[200px] truncate">{{ $store['address'] ?? '-' }}</td>
                                <td>{{ $store['phone'] ?? '-' }}</td>
                                <td>{{ $store['email'] ?? '-' }}</td>
                                <td>{{ $store['is_active'] ? 'Enabled' : 'Disabled' }}</td>
                                <td>{{ $store['open_time'] ? $store['open_time'] : '-' }}</td>
                                <td>{{ $store['close_time'] ? $store['close_time'] : '-' }}</td>
                                <td>
                                    <div tabindex="0" role="button" class="dropdown dropdown-left">
                                        <div class="btn btn-square btn-sm btn-ghost">
                                            <i data-lucide="ellipsis-vertical" class="size-5"></i>
                                        </div>
                                        <ul tabindex="0"
                                            class="menu dropdown-content bg-base-100 border border-base-300 w-36 rounded-box p-1 shadow-sm">
                                            <li><button
                                                    onclick="document.getElementById('edit_modal_{{ $store['id'] }}').showModal()">Edit</button>
                                            </li>
                                            <li><button class="text-error"
                                                    onclick="document.getElementById('delete_modal_{{ $store['id'] }}').showModal()">Delete</button>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>

                            <dialog id="detail_modal_{{ $store['id'] }}" class="modal">
                                <div class="modal-box max-w-3xl max-h-[85vh] overflow-y-auto">
                                    <form method="dialog">
                                        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                    </form>

                                    <h3 class="text-lg font-semibold text-center mb-3">Store Branch Details</h3>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <div>
                                            <label class="text-sm">ID</label>
                                            <input type="text" class="input w-full border-base-300"
                                                value="{{ $store['id'] }}" readonly>
                                        </div>
                                        <div>
                                            <label class="text-sm">Name</label>
                                            <input type="text" class="input w-full border-base-300"
                                                value="{{ $store['name'] }}" readonly>
                                        </div>

                                        <div>
                                            <label class="text-sm">Slug</label>
                                            <input type="text" class="input w-full border-base-300"
                                                value="{{ $store['slug'] }}" readonly>
                                        </div>

                                        <div>
                                            <label class="text-sm">Email</label>
                                            <input type="text" class="input w-full border-base-300"
                                                value="{{ $store['email'] ?? '-' }}" readonly>
                                        </div>

                                        <div>
                                            <label class="text-sm">Phone</label>
                                            <input type="text" class="input w-full border-base-300"
                                                value="{{ $store['phone'] ?? '-' }}" readonly>
                                        </div>

                                        <div>
                                            <label class="text-sm">Country</label>
                                            <input type="text" class="input w-full border-base-300"
                                                value="{{ $store['country'] ?? '-' }}" readonly>
                                        </div>

                                        <div>
                                            <label class="text-sm">State</label>
                                            <input type="text" class="input w-full border-base-300"
                                                value="{{ $store['state'] ?? '-' }}" readonly>
                                        </div>

                                        <div>
                                            <label class="text-sm">City</label>
                                            <input type="text" class="input w-full border-base-300"
                                                value="{{ $store['city'] ?? '-' }}" readonly>
                                        </div>

                                        <div>
                                            <label class="text-sm">Postal Code</label>
                                            <input type="text" class="input w-full border-base-300"
                                                value="{{ $store['postal_code'] ?? '-' }}" readonly>
                                        </div>

                                        <div class="md:col-span-2">
                                            <label class="text-sm">Address</label>
                                            <textarea class="textarea w-full border-base-300" readonly>{{ $store['address'] ?? '-' }}</textarea>
                                        </div>

                                        <div>
                                            <label class="text-sm">Latitude</label>
                                            <input type="text" class="input w-full border-base-300"
                                                value="{{ $store['latitude'] ?? '-' }}" readonly>
                                        </div>

                                        <div>
                                            <label class="text-sm">Longitude</label>
                                            <input type="text" class="input w-full border-base-300"
                                                value="{{ $store['longitude'] ?? '-' }}" readonly>
                                        </div>

                                        <div>
                                            <label class="text-sm">Open Time</label>
                                            <input type="time" class="input w-full border-base-300"
                                                value="{{ $store['open_time'] ?? '' }}" readonly>
                                        </div>

                                        <div>
                                            <label class="text-sm">Close Time</label>
                                            <input type="time" class="input w-full border-base-300"
                                                value="{{ $store['close_time'] ?? '' }}" readonly>
                                        </div>

                                        <div class="md:col-span-2">
                                            <label class="text-sm">Description</label>
                                            <textarea class="textarea w-full border-base-300" readonly>{{ $store['description'] ?? '-' }}</textarea>
                                        </div>

                                        <div>
                                            <label class="text-sm">Active Status</label>
                                            <input type="text" class="input w-full border-base-300"
                                                value="{{ $store['is_active'] ? 'Active' : 'Inactive' }}" readonly>
                                        </div>

                                        <div>
                                            <label class="text-sm">Created At</label>
                                            <input type="text" class="input w-full border-base-300"
                                                value="{{ \Carbon\Carbon::parse($store['created_at'])->format('Y-m-d H:i') }}"
                                                readonly>
                                        </div>
                                    </div>

                                    <div class="modal-action mt-3">
                                        <form method="dialog">
                                            <button class="btn">Close</button>
                                        </form>
                                    </div>
                                </div>
                            </dialog>


                            <!-- Edit Modal -->
                            <dialog id="edit_modal_{{ $store['id'] }}" class="modal">
                                <div class="modal-box max-w-2xl max-h-[85vh] overflow-y-auto">
                                    <form method="dialog">
                                        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                    </form>

                                    <h3 class="text-lg font-semibold text-center mb-3">Edit Store Branch</h3>

                                    <form method="POST"
                                        action="{{ route('admin.dashboard.store.id.post', $store['id']) }}">
                                        @csrf
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">

                                            <div class="flex flex-col gap-1">
                                                <label>Branch Name</label>
                                                <input type="text" name="name" class="input w-full"
                                                    value="{{ $store['name'] }}" required>
                                            </div>

                                            <div class="flex flex-col gap-1">
                                                <label>Email</label>
                                                <input type="email" name="email" class="input w-full"
                                                    value="{{ $store['email'] }}">
                                            </div>

                                            <div class="flex flex-col gap-1">
                                                <label>Phone</label>
                                                <input type="text" name="phone" class="input w-full"
                                                    value="{{ $store['phone'] }}">
                                            </div>

                                            <div class="flex flex-col gap-1">
                                                <label>Country</label>
                                                <input type="text" name="country" class="input w-full"
                                                    value="{{ $store['country'] }}">
                                            </div>

                                            <div class="flex flex-col gap-1">
                                                <label>State</label>
                                                <input type="text" name="state" class="input w-full"
                                                    value="{{ $store['state'] }}">
                                            </div>

                                            <div class="flex flex-col gap-1">
                                                <label>City</label>
                                                <input type="text" name="city" class="input w-full"
                                                    value="{{ $store['city'] }}">
                                            </div>

                                            <div class="flex flex-col gap-1">
                                                <label>Postal Code</label>
                                                <input type="text" name="postal_code" class="input w-full"
                                                    value="{{ $store['postal_code'] }}">
                                            </div>

                                            <div class="flex flex-col gap-1 md:col-span-2">
                                                <label>Address</label>
                                                <textarea name="address" class="textarea w-full" rows="2">{{ $store['address'] }}</textarea>
                                            </div>

                                            <div class="flex flex-col gap-1">
                                                <label>Latitude</label>
                                                <input type="text" name="latitude" class="input w-full"
                                                    value="{{ $store['latitude'] }}">
                                            </div>

                                            <div class="flex flex-col gap-1">
                                                <label>Longitude</label>
                                                <input type="text" name="longitude" class="input w-full"
                                                    value="{{ $store['longitude'] }}">
                                            </div>

                                            <div class="flex flex-col gap-1">
                                                <label>Open Time</label>
                                                <input type="time" name="open_time" class="input w-full"
                                                    value="{{ $store['open_time'] }}">
                                            </div>

                                            <div class="flex flex-col gap-1">
                                                <label>Close Time</label>
                                                <input type="time" name="close_time" class="input w-full"
                                                    value="{{ $store['close_time'] }}">
                                            </div>

                                            <div class="flex flex-col gap-1 md:col-span-2">
                                                <label>Description</label>
                                                <textarea name="description" class="textarea w-full" rows="3">{{ $store['description'] }}</textarea>
                                            </div>

                                            <div class="flex flex-col gap-1">
                                                <label>Active</label>
                                                <select name="is_active" class="select w-full">
                                                    <option value="1" {{ $store['is_active'] ? 'selected' : '' }}>
                                                        Enabled
                                                    </option>
                                                    <option value="0" {{ !$store['is_active'] ? 'selected' : '' }}>
                                                        Disabled
                                                    </option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="modal-action mt-3">
                                            <button type="submit" class="btn btn-primary">Update Store Branch</button>
                                        </div>
                                    </form>
                                </div>
                            </dialog>


                            <!-- Delete Modal -->
                            <dialog id="delete_modal_{{ $store['id'] }}" class="modal">
                                <div class="modal-box">
                                    <form method="dialog">
                                        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                    </form>
                                    <p class="text-lg font-semibold">Confirm Delete</p>
                                    <p class="text-sm mb-4">Are you sure you want to delete this store branch <span
                                            class="text-error">{{ $store['name'] }}</span>?</p>
                                    <div class="modal-action">
                                        <form method="dialog"><button class="btn">Cancel</button></form>
                                        <form method="POST"
                                            action="{{ route('admin.dashboard.store.id.delete', $store['id']) }}">
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
                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-3 py-3 px-5">
                    <div class="text-sm text-gray-500">
                        <span class="font-semibold">{{ $branches->firstItem() }}</span>
                        –
                        <span class="font-semibold">{{ $branches->lastItem() }}</span>
                        of
                        <span class="font-semibold">{{ $branches->total() }}</span>
                        results
                    </div>

                    <div class="join">
                        @if ($branches->onFirstPage())
                            <button class="join-item btn btn-sm btn-disabled">«</button>
                        @else
                            <a href="{{ $branches->previousPageUrl() }}" class="join-item btn btn-sm">«</a>
                        @endif

                        @for ($i = 1; $i <= $branches->lastPage(); $i++)
                            <a href="{{ $branches->url($i) }}"
                                class="join-item btn btn-sm {{ $branches->currentPage() === $i ? 'btn-active' : '' }}">{{ $i }}</a>
                        @endfor

                        @if ($branches->hasMorePages())
                            <a href="{{ $branches->nextPageUrl() }}" class="join-item btn btn-sm">»</a>
                        @else
                            <button class="join-item btn btn-sm btn-disabled">»</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Create Store Modal -->
        <dialog id="create_store_modal" class="modal">
            <div class="modal-box max-w-2xl max-h-[85vh] overflow-y-auto">
                <form method="dialog">
                    <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                </form>

                <h3 class="text-lg font-semibold text-center mb-3">Create Store Branch</h3>

                <form method="POST" action="{{ route('admin.dashboard.store.post') }}">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">

                        <div class="flex flex-col gap-1">
                            <label>Branch Name</label>
                            <input type="text" name="name" class="input w-full" required>
                        </div>

                        <div class="flex flex-col gap-1">
                            <label>Email</label>
                            <input type="email" name="email" class="input w-full">
                        </div>

                        <div class="flex flex-col gap-1">
                            <label>Phone</label>
                            <input type="text" name="phone" class="input w-full">
                        </div>

                        <div class="flex flex-col gap-1">
                            <label>Country</label>
                            <input type="text" name="country" class="input w-full">
                        </div>

                        <div class="flex flex-col gap-1">
                            <label>State</label>
                            <input type="text" name="state" class="input w-full">
                        </div>

                        <div class="flex flex-col gap-1">
                            <label>City</label>
                            <input type="text" name="city" class="input w-full">
                        </div>

                        <div class="flex flex-col gap-1">
                            <label>Postal Code</label>
                            <input type="text" name="postal_code" class="input w-full">
                        </div>

                        <div class="flex flex-col gap-1 md:col-span-2">
                            <label>Address</label>
                            <textarea name="address" class="textarea w-full" rows="2"></textarea>
                        </div>

                        <div class="flex flex-col gap-1">
                            <label>Latitude</label>
                            <input type="text" name="latitude" class="input w-full">
                        </div>

                        <div class="flex flex-col gap-1">
                            <label>Longitude</label>
                            <input type="text" name="longitude" class="input w-full">
                        </div>

                        <div class="flex flex-col gap-1">
                            <label>Open Time</label>
                            <input type="time" name="open_time" class="input w-full">
                        </div>

                        <div class="flex flex-col gap-1">
                            <label>Close Time</label>
                            <input type="time" name="close_time" class="input w-full">
                        </div>

                        <div class="flex flex-col gap-1 md:col-span-2">
                            <label>Description</label>
                            <textarea name="description" class="textarea w-full" rows="3"></textarea>
                        </div>

                        <div class="flex flex-col gap-1">
                            <label>Active</label>
                            <select name="is_active" class="select w-full">
                                <option value="1" selected>Enabled</option>
                                <option value="0">Disabled</option>
                            </select>
                        </div>
                    </div>

                    <div class="modal-action mt-3">
                        <button type="submit" class="btn btn-primary">Create Store Branch</button>
                    </div>
                </form>
            </div>
        </dialog>

    </div>
@endsection
