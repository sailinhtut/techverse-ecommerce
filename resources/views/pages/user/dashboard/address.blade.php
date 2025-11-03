@extends('layouts.user.user_dashboard')

@section('user_dashboard_content')
    <div class="p-3 lg:p-5">
        <div class="flex flex-row justify-between">
            <p class="lg:text-lg font-semibold ">Addresses</p>
            <button onclick="create_address_modal.showModal()" class="btn btn-primary">Add Address</button>
        </div>

        <div class="card shadow-sm border border-base-300">
            <div class="card-body p-0 m-0 overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="w-[50px]">No.</th>
                            <th class="w-[50px]">Address</th>
                            <th class="w-[200px]">Recipient</th>
                            <th class="w-[200px]">Type</th>
                            <th style="width:180px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($addresses as $address)
                            <tr>
                                <td style="" class="">
                                    {{ $loop->iteration + ($addresses->currentPage() - 1) * $addresses->perPage() }}.
                                </td>

                                <td class="w-[50px]">
                                    <div onclick="document.getElementById('detailModal{{ $address['id'] }}').showModal()"
                                        class="cursor-default hover:underline">{{ $address['label'] }}</div>
                                </td>

                                <td class="w-[200px] h-[30px] line-clamp-1">
                                    <div>{{ $address['recipient_name'] ?? 'Not Set' }}</div>
                                </td>

                                <td>
                                    @if ($address['is_default_shipping'])
                                        <div class="badge badge-success badge-outline">Default Shipping
                                        </div>
                                    @endif
                                    @if ($address['is_default_billing'])
                                        <div class="badge badge-info badge-outline">Default Billing</div>
                                    @endif
                                </td>
                                <td>
                                    <div tabindex="0" role="button" class="dropdown dropdown-left">
                                        <div class="btn btn-square btn-sm btn-ghost">
                                            <i data-lucide="ellipsis-vertical" class="size-5"></i>
                                        </div>
                                        <ul tabindex="0"
                                            class="menu dropdown-content bg-base-100 border border-base-300 w-30 rounded-box p-1 shadow-sm">
                                            <li>
                                                <button
                                                    onclick="document.getElementById('detailModal{{ $address['id'] }}').showModal()">
                                                    View
                                                </button>
                                            </li>
                                            <li>
                                                <button
                                                    onclick="document.getElementById('editModal{{ $address['id'] }}').showModal()">
                                                    Edit
                                                </button>
                                            </li>
                                            <li>
                                                <button type="button" class="text-error"
                                                    onclick="document.getElementById('deleteModal{{ $address['id'] }}').showModal()">
                                                    Delete
                                                </button>
                                            </li>
                                        </ul>
                                    </div>


                                    <dialog id="detailModal{{ $address['id'] }}" class="modal">
                                        <div class="modal-box max-h-[85vh]  max-w-md">
                                            <form method="dialog">
                                                <button
                                                    class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                            </form>

                                            {{-- Title --}}
                                            <h3 class="text-lg font-semibold text-center mb-2">
                                                {{ $address['label'] ?? 'Unnamed Address' }}</h3>

                                            {{-- Address Info --}}
                                            <div class="space-y-2 text-sm">
                                                <p><strong>ID:</strong> {{ $address['id'] }}</p>
                                                <p><strong>Recipient:</strong> {{ $address['recipient_name'] ?? '—' }}</p>
                                                <p><strong>Phone:</strong> {{ $address['phone'] ?? '—' }}</p>
                                                <p><strong>Street:</strong> {{ $address['street_address'] ?? '—' }}</p>
                                                <p><strong>City:</strong> {{ $address['city'] ?? '—' }}</p>
                                                <p><strong>State:</strong> {{ $address['state'] ?? '—' }}</p>
                                                <p><strong>Postal Code:</strong> {{ $address['postal_code'] ?? '—' }}</p>
                                                <p><strong>Country:</strong> {{ $address['country'] ?? '—' }}</p>

                                                @if ($address['latitude'] || $address['longitude'])
                                                    <p>
                                                        <strong>Location:</strong>
                                                        {{ $address['latitude'] ?? '?' }},
                                                        {{ $address['longitude'] ?? '?' }}
                                                    </p>
                                                @endif
                                            </div>

                                            {{-- Type badges --}}
                                            <div class='flex flex-row gap-2 mt-3'>
                                                <div><strong>Type:</strong></div>
                                                <div class="flex flex-wrap gap-2">
                                                    @if ($address['is_default_shipping'])
                                                        <div class="badge badge-success badge-outline">Default Shipping
                                                        </div>
                                                    @endif
                                                    @if ($address['is_default_billing'])
                                                        <div class="badge badge-info badge-outline">Default Billing</div>
                                                    @endif

                                                </div>
                                            </div>


                                            {{-- Footer --}}
                                            <div class="modal-action mt-6">
                                                <form method="dialog">
                                                    <button class="btn btn-primary w-full">Close</button>
                                                </form>
                                            </div>
                                        </div>
                                    </dialog>

                                    <dialog id="editModal{{ $address['id'] }}" class="modal">
                                        <div class="modal-box max-w-lg">
                                            <form method="dialog">
                                                <button
                                                    class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                            </form>

                                            <h3 class="text-lg font-semibold mb-4">Edit Address</h3>

                                            <form method="POST" id="editForm{{ $address['id'] }}"
                                                action="{{ route('address.id.post', ['id' => $address['id']]) }}"
                                                class="flex flex-col gap-3">
                                                @csrf

                                                <div class="form-control">
                                                    <label class="label"><span class="label-text">Label</span></label>
                                                    <input type="text" name="label" value="{{ $address['label'] }}"
                                                        class="input input-bordered w-full" />
                                                </div>

                                                <div class="form-control">
                                                    <label class="label"><span class="label-text">Recipient
                                                            Name</span></label>
                                                    <input type="text" name="recipient_name"
                                                        value="{{ $address['recipient_name'] }}"
                                                        class="input input-bordered w-full" required />
                                                </div>

                                                <div class="form-control">
                                                    <label class="label"><span class="label-text">Phone</span></label>
                                                    <input type="text" name="phone" value="{{ $address['phone'] }}"
                                                        class="input input-bordered w-full" />
                                                </div>

                                                <div class="form-control">
                                                    <label class="label"><span class="label-text">Street
                                                            Address</span></label>
                                                    <input type="text" name="street_address"
                                                        value="{{ $address['street_address'] }}"
                                                        class="input input-bordered w-full" required />
                                                </div>

                                                <div class="grid grid-cols-2 gap-3">
                                                    <div class="form-control">
                                                        <label class="label"><span class="label-text">City</span></label>
                                                        <input type="text" name="city" value="{{ $address['city'] }}"
                                                            class="input input-bordered w-full" required />
                                                    </div>
                                                    <div class="form-control">
                                                        <label class="label"><span class="label-text">State</span></label>
                                                        <input type="text" name="state"
                                                            value="{{ $address['state'] }}"
                                                            class="input input-bordered w-full" />
                                                    </div>
                                                </div>

                                                <div class="grid grid-cols-2 gap-3">
                                                    <div class="form-control">
                                                        <label class="label"><span class="label-text">Postal
                                                                Code</span></label>
                                                        <input type="text" name="postal_code"
                                                            value="{{ $address['postal_code'] }}"
                                                            class="input input-bordered w-full" />
                                                    </div>
                                                    <div class="form-control">
                                                        <label class="label"><span
                                                                class="label-text">Country</span></label>
                                                        <input type="text" name="country"
                                                            value="{{ $address['country'] }}"
                                                            class="input input-bordered w-full" />
                                                    </div>
                                                </div>

                                                <div class="grid grid-cols-2 gap-3">
                                                    <div class="form-control">
                                                        <label class="label"><span
                                                                class="label-text">Latitude</span></label>
                                                        <input type="number" name="latitude" step="0.0000001"
                                                            value="{{ $address['latitude'] }}"
                                                            class="input input-bordered w-full" />
                                                    </div>
                                                    <div class="form-control">
                                                        <label class="label"><span
                                                                class="label-text">Longitude</span></label>
                                                        <input type="number" name="longitude" step="0.0000001"
                                                            value="{{ $address['longitude'] }}"
                                                            class="input input-bordered w-full" />
                                                    </div>
                                                </div>

                                                <div class="form-control flex flex-row items-center gap-4 mt-2">
                                                    <label class="cursor-pointer flex items-center gap-2">
                                                        <input type="hidden" name="is_default_shipping" value="0">
                                                        <input type="checkbox" name="is_default_shipping" value="1"
                                                            class="checkbox checkbox-sm"
                                                            {{ $address['is_default_shipping'] ? 'checked' : '' }} />
                                                        <span class="label-text">Default Shipping</span>
                                                    </label>
                                                    <label class="cursor-pointer flex items-center gap-2">
                                                        <input type="hidden" name="is_default_billing" value="0">
                                                        <input type="checkbox" name="is_default_billing" value="1"
                                                            class="checkbox checkbox-sm"
                                                            {{ $address['is_default_billing'] ? 'checked' : '' }} />
                                                        <span class="label-text">Default Billing</span>
                                                    </label>
                                                </div>
                                            </form>

                                            <div class="modal-action">
                                                <form method="dialog">
                                                    <button class="btn">Cancel</button>
                                                </form>
                                                <button
                                                    onclick="document.getElementById('editForm{{ $address['id'] }}').submit()"
                                                    class="btn btn-primary">Save</button>
                                            </div>
                                        </div>
                                    </dialog>



                                    <dialog id="deleteModal{{ $address['id'] }}" class="modal">
                                        <div class="modal-box">
                                            <form method="dialog">
                                                <button
                                                    class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                            </form>
                                            <p class="text-lg font-semibold py-0">Confirm Delete</p>

                                            <p class="py-2 mb-0 text-sm">
                                                Are you sure you want to delete
                                                <span class="italic text-error">{{ $address['label'] }}</span> ?
                                            </p>
                                            <div class="modal-action mt-0">
                                                <form method="dialog">
                                                    <button class="btn  lg:btn-md">Close</button>
                                                </form>
                                                <form method="POST"
                                                    action="{{ route('address.id.delete', ['id' => $address['id']]) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="btn  lg:btn-md btn-error">Delete</button>
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
                        <span class="font-semibold">{{ $addresses->firstItem() }}</span>
                        –
                        <span class="font-semibold">{{ $addresses->lastItem() }}</span>
                        of
                        <span class="font-semibold">{{ $addresses->total() }}</span>
                        results
                    </div>

                    <div class="join">
                        @if ($addresses->onFirstPage())
                            <button class="join-item btn btn-sm btn-disabled">«</button>
                        @else
                            <a href="{{ $addresses->previousPageUrl() }}" class="join-item btn btn-sm">«</a>
                        @endif

                        <a href="{{ $addresses->url(1) }}"
                            class="join-item btn btn-sm {{ $addresses->currentPage() === 1 ? 'btn-active' : '' }}">
                            1
                        </a>

                        @php
                            $start = max(2, $addresses->currentPage() - 1);
                            $end = min($addresses->lastPage() - 1, $addresses->currentPage() + 1);
                        @endphp

                        @for ($i = $start; $i <= $end; $i++)
                            <a href="{{ $addresses->url($i) }}"
                                class="join-item btn btn-sm {{ $addresses->currentPage() === $i ? 'btn-active' : '' }}">
                                {{ $i }}
                            </a>
                        @endfor

                        @if ($addresses->lastPage() > 1)
                            <a href="{{ $addresses->url($addresses->lastPage()) }}"
                                class="join-item btn btn-sm {{ $addresses->currentPage() === $addresses->lastPage() ? 'btn-active' : '' }}">
                                {{ $addresses->lastPage() }}
                            </a>
                        @endif

                        @if ($addresses->hasMorePages())
                            <a href="{{ $addresses->nextPageUrl() }}" class="join-item btn btn-sm">»</a>
                        @else
                            <button class="join-item btn btn-sm btn-disabled">»</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>




        <dialog id="create_address_modal" class="modal">
            <div class="modal-box max-w-lg">
                <form method="dialog">
                    <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                </form>

                <h3 class="text-lg font-semibold mb-4">Add New Address</h3>

                <form method="POST" id="create_address_form" action="{{ route('address.post') }}"
                    class="flex flex-col gap-3">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ auth()->id() ?? -1 }}">

                    <div class="form-control">
                        <label class="label"><span class="label-text">Label (optional)</span></label>
                        <input type="text" name="label" placeholder="e.g. Home, Office"
                            class="input input-bordered w-full" />
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text">Recipient Name</span></label>
                        <input type="text" name="recipient_name" class="input input-bordered w-full" required />
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text">Phone</span></label>
                        <input type="text" name="phone" class="input input-bordered w-full" />
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text">Street Address</span></label>
                        <input type="text" name="street_address" class="input input-bordered w-full" required />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="form-control">
                            <label class="label"><span class="label-text">City</span></label>
                            <input type="text" name="city" class="input input-bordered w-full" required />
                        </div>
                        <div class="form-control">
                            <label class="label"><span class="label-text">State</span></label>
                            <input type="text" name="state" class="input input-bordered w-full" />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="form-control">
                            <label class="label"><span class="label-text">Postal Code</span></label>
                            <input type="text" name="postal_code" class="input input-bordered w-full" />
                        </div>
                        <div class="form-control">
                            <label class="label"><span class="label-text">Country</span></label>
                            <input type="text" name="country" class="input input-bordered w-full" value="Myanmar"
                                required />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="form-control">
                            <label class="label"><span class="label-text">Latitude</span></label>
                            <input type="number" name="latitude" step="0.0000001"
                                class="input input-bordered w-full" />
                        </div>
                        <div class="form-control">
                            <label class="label"><span class="label-text">Longitude</span></label>
                            <input type="number" name="longitude" step="0.0000001"
                                class="input input-bordered w-full" />
                        </div>
                    </div>

                    <div class="form-control flex flex-row items-center gap-4 mt-2">
                        <label class="cursor-pointer flex items-center gap-2">
                            <input type="hidden" name="is_default_shipping" value="0">
                            <input type="checkbox" name="is_default_shipping" value="1" class="checkbox checkbox-sm" />
                            <span class="label-text">Default Shipping</span>
                        </label>
                        <label class="cursor-pointer flex items-center gap-2">
                              <input type="hidden" name="is_default_billing" value="0">
                            <input type="checkbox" name="is_default_billing" value="1" class="checkbox checkbox-sm" />
                            <span class="label-text">Default Billing</span>
                        </label>
                    </div>
                </form>

                <div class="modal-action">
                    <form method="dialog">
                        <button class="btn">Cancel</button>
                    </form>
                    <button onclick="document.getElementById('create_address_form').submit()"
                        class="btn btn-primary">Save</button>
                </div>
            </div>
        </dialog>
    </div>
@endsection
