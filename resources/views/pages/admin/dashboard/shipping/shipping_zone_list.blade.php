@extends('layouts.admin.admin_dashboard')

@section('admin_dashboard_content')
    <div class="p-5 min-h-screen">
        <p class="lg:text-lg font-semibold mb-3">Shipping Zones</p>

        <button class="btn btn-primary" onclick="create_shipping_zone_modal.showModal()">Create Shipping Zone</button>

        <div class="card shadow-sm border border-base-300 mt-4">
            <div class="card-body p-0 m-0 overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Name</th>
                            <th>Country</th>
                            <th>State</th>
                            <th>City</th>
                            <th>Postal Code</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($shipping_zones as $zone)
                            <tr>
                                <td>{{ $loop->iteration + ($shipping_zones->currentPage() - 1) * $shipping_zones->perPage() }}
                                </td>
                                <td>
                                    <p onclick="document.getElementById('detail_modal_{{ $zone['id'] }}').showModal()"
                                        class="cursor-pointer hover:underline">{{ $zone['name'] }}</p>
                                </td>
                                <td>{{ $zone['country'] ?? '-' }}</td>
                                <td>{{ $zone['state'] ?? '-' }}</td>
                                <td>{{ $zone['city'] ?? '-' }}</td>
                                <td>{{ $zone['postal_code'] ?? '-' }}</td>
                                <td>{{ $zone['description'] ?? '-' }}</td>
                                <td>
                                    <div tabindex="0" role="button" class="dropdown dropdown-left">
                                        <div class="btn btn-square btn-sm btn-ghost">
                                            <i data-lucide="ellipsis-vertical" class="size-5"></i>
                                        </div>
                                        <ul tabindex="0"
                                            class="menu dropdown-content bg-base-100 border border-base-300 w-30 rounded-box p-1 shadow-sm">
                                            <li><button
                                                    onclick="document.getElementById('detail_modal_{{ $zone['id'] }}').showModal()">View</button>
                                            <li><button
                                                    onclick="document.getElementById('edit_modal_{{ $zone['id'] }}').showModal()">Edit</button>
                                            </li>
                                            <li><button class="text-error"
                                                    onclick="document.getElementById('delete_modal_{{ $zone['id'] }}').showModal()">Delete</button>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>

                            <dialog id="detail_modal_{{ $zone['id'] }}" class="modal">
                                <div class="modal-box max-w-xl">
                                    <form method="dialog">
                                        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                    </form>
                                    <h3 class="text-lg font-semibold text-center mb-3">Shipping Zone Details</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <div>
                                            <label class="text-sm">ID</label>
                                            <input type="text"
                                                class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                                value="{{ $zone['id'] }}" readonly>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="text-sm">Name</label>
                                            <input type="text"
                                                class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                                value="{{ $zone['name'] }}" readonly>
                                        </div>
                                        <div>
                                            <label class="text-sm">Country</label>
                                            <input type="text"
                                                class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                                value="{{ $zone['country'] ?? '-' }}" readonly>
                                        </div>
                                        <div>
                                            <label class="text-sm">State</label>
                                            <input type="text"
                                                class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                                value="{{ $zone['state'] ?? '-' }}" readonly>
                                        </div>
                                        <div>
                                            <label class="text-sm">City</label>
                                            <input type="text"
                                                class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                                value="{{ $zone['city'] ?? '-' }}" readonly>
                                        </div>
                                        <div>
                                            <label class="text-sm">Postal Code</label>
                                            <input type="text"
                                                class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                                value="{{ $zone['postal_code'] ?? '-' }}" readonly>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="text-sm">Description</label>
                                            <textarea class="textarea w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                                readonly>{{ $zone['description'] }}</textarea>
                                        </div>
                                    </div>
                                    <div class="modal-action">
                                        <form method="dialog" class="w-full">
                                            <button class="btn">Close</button>
                                        </form>
                                    </div>
                                </div>
                            </dialog>

                            <dialog id="edit_modal_{{ $zone['id'] }}" class="modal">
                                <div class="modal-box max-w-xl">
                                    <form method="dialog">
                                        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                    </form>
                                    <h3 class="text-lg font-semibold text-center mb-3">Edit Shipping Zone</h3>
                                    <form method="POST"
                                        action="{{ route('admin.dashboard.shipping.shipping-zone.id.post', ['id' => $zone['id']]) }}">
                                        @csrf
                                        @method('POST')

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                            <div class="md:col-span-2">
                                                <label class="text-sm">Name</label>
                                                <input name="name" class="input w-full" value="{{ $zone['name'] }}"
                                                    required>
                                            </div>
                                            <div>
                                                <label class="text-sm">Country</label>
                                                <input name="country" class="input w-full" value="{{ $zone['country'] }}">
                                            </div>
                                            <div>
                                                <label class="text-sm">State</label>
                                                <input name="state" class="input w-full" value="{{ $zone['state'] }}">
                                            </div>
                                            <div>
                                                <label class="text-sm">City</label>
                                                <input name="city" class="input w-full" value="{{ $zone['city'] }}">
                                            </div>
                                            <div>
                                                <label class="text-sm">Postal Code</label>
                                                <input name="postal_code" class="input w-full"
                                                    value="{{ $zone['postal_code'] }}">
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="text-sm">Description</label>
                                                <textarea name="description" class="textarea w-full">{{ $zone['description'] }}</textarea>
                                            </div>
                                        </div>
                                        <div class="modal-action mt-3">
                                            <button type="submit" class="btn btn-primary">Update Shipping
                                                Zone</button>
                                        </div>
                                    </form>
                                </div>
                            </dialog>

                            <dialog id="delete_modal_{{ $zone['id'] }}" class="modal">
                                <div class="modal-box">
                                    <form method="dialog">
                                        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                    </form>
                                    <p class="text-lg font-semibold">Confirm Delete</p>
                                    <p class="text-sm mb-4">Are you sure you want to delete <span
                                            class="text-error">{{ $zone['name'] }}</span>?</p>
                                    <div class="modal-action">
                                        <form method="dialog">
                                            <button class="btn">Cancel</button>
                                        </form>
                                        <form method="POST"
                                            action="{{ route('admin.dashboard.shipping.shipping-zone.id.delete', ['id' => $zone['id']]) }}">
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
                        <span class="font-semibold">{{ $shipping_zones->firstItem() }}</span> –
                        <span class="font-semibold">{{ $shipping_zones->lastItem() }}</span> of
                        <span class="font-semibold">{{ $shipping_zones->total() }}</span> results
                    </div>
                    <div class="join">
                        @if ($shipping_zones->onFirstPage())
                            <button class="join-item btn btn-sm btn-disabled">«</button>
                        @else
                            <a href="{{ $shipping_zones->previousPageUrl() }}" class="join-item btn btn-sm">«</a>
                        @endif
                        @for ($i = 1; $i <= $shipping_zones->lastPage(); $i++)
                            <a href="{{ $shipping_zones->url($i) }}"
                                class="join-item btn btn-sm {{ $shipping_zones->currentPage() === $i ? 'btn-active' : '' }}">{{ $i }}</a>
                        @endfor
                        @if ($shipping_zones->hasMorePages())
                            <a href="{{ $shipping_zones->nextPageUrl() }}" class="join-item btn btn-sm">»</a>
                        @else
                            <button class="join-item btn btn-sm btn-disabled">»</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Create Modal --}}
        <dialog id="create_shipping_zone_modal" class="modal">
            <div class="modal-box max-w-xl">
                <form method="dialog"><button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                </form>
                <h3 class="text-lg font-semibold text-center mb-3">Create Shipping Zone</h3>
                <form method="POST" action="{{ route('admin.dashboard.shipping.shipping-zone.post') }}">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div class="md:col-span-2">
                            <label class="text-sm">Name</label>
                            <input name="name" class="input w-full" placeholder="Zone Name" required>
                        </div>
                        <div>
                            <label class="text-sm">Country</label>
                            <input name="country" class="input w-full" placeholder="Country">
                        </div>
                        <div>
                            <label class="text-sm">State</label>
                            <input name="state" class="input w-full" placeholder="State">
                        </div>
                        <div>
                            <label class="text-sm">City</label>
                            <input name="city" class="input w-full" placeholder="City">
                        </div>
                        <div>
                            <label class="text-sm">Postal Code</label>
                            <input name="postal_code" class="input w-full" placeholder="Postal Code">
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-sm">Description</label>
                            <textarea name="description" class="textarea w-full"></textarea>
                        </div>
                    </div>
                    <div class="modal-action mt-3">
                        <button type="submit" class="btn btn-primary w-full">Create Shipping Zone</button>
                    </div>
                </form>
            </div>
        </dialog>
    </div>
@endsection
