@extends('layouts.admin.admin_dashboard')

@section('admin_dashboard_content')
    <div class="p-5 min-h-screen">
        <p class="lg:text-lg font-semibold mb-3">Shipping Rates</p>

        <button class="btn btn-primary" onclick="create_shipping_rate_modal.showModal()">Create Shipping Rate</button>

        <div class="card shadow-sm border border-base-300 mt-4">
            <div class="card-body p-0 m-0 overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Name</th>
                            <th>Zone</th>
                            <th>Method</th>
                            <th>Class</th>
                            <th>Rate</th>
                            <th>Type</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($shipping_rates as $rate)
                            <tr>
                                <td>{{ $loop->iteration + ($shipping_rates->currentPage() - 1) * $shipping_rates->perPage() }}
                                </td>
                                <td>
                                    <p onclick="document.getElementById('detail_modal_{{ $rate['id'] }}').showModal()"
                                        class="cursor-pointer hover:underline">
                                        {{ $rate['name'] }}
                                    </p>
                                </td>
                                <td>{{ $rate['zone']['name'] ?? '*' }}</td>
                                <td>{{ $rate['method']['name'] ?? '*' }}</td>
                                <td>{{ $rate['class']['name'] ?? '*' }}</td>
                                <td>{{ number_format($rate['cost'], 2) }}</td>
                                <td>{{ ucwords(str_replace('_', ' ', $rate['type'])) }}</td>
                                <td>
                                    <div tabindex="0" role="button" class="dropdown dropdown-left">
                                        <div class="btn btn-square btn-sm btn-ghost">
                                            <i data-lucide="ellipsis-vertical" class="size-5"></i>
                                        </div>
                                        <ul tabindex="0"
                                            class="menu dropdown-content bg-base-100 border border-base-300 w-30 rounded-box p-1 shadow-sm">
                                            <li><button
                                                    onclick="document.getElementById('detail_modal_{{ $rate['id'] }}').showModal()">View</button>
                                            </li>
                                            <li><button
                                                    onclick="document.getElementById('edit_modal_{{ $rate['id'] }}').showModal()">Edit</button>
                                            </li>
                                            <li><button class="text-error"
                                                    onclick="document.getElementById('delete_modal_{{ $rate['id'] }}').showModal()">Delete</button>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>

                            <dialog id="detail_modal_{{ $rate['id'] }}" class="modal">
                                <div class="modal-box max-w-xl">
                                    <form method="dialog">
                                        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                    </form>

                                    <h3 class="text-lg font-semibold text-center mb-3">Shipping Rate Details</h3>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <div>
                                            <label class="text-sm">ID</label>
                                            <input type="text"
                                                class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                                value="{{ $rate['id'] }}" readonly>
                                        </div>

                                        <div>
                                            <label class="text-sm">Name</label>
                                            <input type="text"
                                                class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                                value="{{ $rate['name'] }}" readonly>
                                        </div>

                                        <div>
                                            <label class="text-sm">Shipping Zone</label>
                                            <input type="text"
                                                class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                                value="{{ $rate['zone']['name'] ?? '*' }}" readonly>
                                        </div>

                                        <div>
                                            <label class="text-sm">Shipping Method</label>
                                            <input type="text"
                                                class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                                value="{{ $rate['method']['name'] ?? '*' }}" readonly>
                                        </div>

                                        <div class="md:col-span-2">
                                            <label class="text-sm">Shipping Class</label>
                                            <input type="text"
                                                class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                                value="{{ $rate['class']['name'] ?? '*' }}" readonly>
                                        </div>

                                        <div class="md:col-span-2">
                                            <label class="text-sm">Description</label>
                                            <textarea class="textarea w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                                readonly>{{ $rate['description'] }}</textarea>
                                        </div>

                                        <div>
                                            <label class="text-sm">Rate</label>
                                            <input type="text"
                                                class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                                value="{{ $rate['cost'] }}" readonly>
                                        </div>

                                        <div>
                                            <label class="text-sm">Type</label>
                                            <input type="text"
                                                class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                                value="{{ ucwords(str_replace('_', ' ', $rate['type'])) }}" readonly>
                                        </div>
                                        <div>
                                            <label class="text-sm">Percentage Rate (%)</label>
                                            <input type="text"
                                                class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                                value="{{ $rate['is_percentage'] ? 'Enabled' : 'Disabled' }}" readonly>
                                        </div>
                                    </div>

                                    <div class="modal-action mt-3">
                                        <form method="dialog" class="w-full">
                                            <button class="btn">Close</button>
                                        </form>
                                    </div>
                                </div>
                            </dialog>


                            <dialog id="edit_modal_{{ $rate['id'] }}" class="modal">
                                <div class="modal-box max-w-xl">
                                    <form method="dialog">
                                        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                    </form>

                                    <h3 class="text-lg font-semibold text-center mb-3">Edit Shipping Rate</h3>

                                    <form method="POST"
                                        action="{{ route('admin.dashboard.shipping.shipping-rate.id.post', ['id' => $rate['id']]) }}">
                                        @csrf
                                        @method('POST')

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                            <div class="md:col-span-2">
                                                <label class="text-sm">Name</label>
                                                <input name="name" class="input w-full border-base-300"
                                                    placeholder="Rate name" value="{{ $rate['name'] }}" >
                                            </div>

                                            <div>
                                                <label class="text-sm">Shipping Zone</label>
                                                <select name="shipping_zone_id" class="select w-full border-base-300"
                                                    required>
                                                    <option disabled>Select Zone</option>
                                                    <option value="">*</option>
                                                    @foreach ($shipping_zones as $zone)
                                                        <option value="{{ $zone['id'] }}" @selected($rate['shipping_zone_id'] == $zone['id'])>
                                                            {{ $zone['name'] }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div>
                                                <label class="text-sm">Shipping Method</label>
                                                <select name="shipping_method_id" class="select w-full border-base-300"
                                                    required>
                                                    <option disabled>Select Method</option>
                                                    <option value="">*</option>
                                                    @foreach ($shipping_methods as $method)
                                                        <option value="{{ $method['id'] }}" @selected($rate['shipping_method_id'] == $method['id'])>
                                                            {{ $method['name'] }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="md:col-span-2">
                                                <label class="text-sm">Shipping Class</label>
                                                <select name="shipping_class_id" class="select w-full border-base-300">
                                                    <option value="">*</option>
                                                    @foreach ($shipping_classes as $class)
                                                        <option value="{{ $class['id'] }}" @selected($rate['shipping_class_id'] == $class['id'])>
                                                            {{ $class['name'] }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="md:col-span-2">
                                                <label class="text-sm">Description</label>
                                                <textarea name="description" class="textarea w-full border-base-300" rows="2">{{ $rate['description'] }}</textarea>
                                            </div>

                                            <div>
                                                <label class="text-sm">Flat Cost</label>
                                                <input name="cost" type="number" step="0.01"
                                                    class="input w-full border-base-300" value="{{ $rate['cost'] }}"
                                                    required>
                                            </div>

                                            <div>
                                                <label class="text-sm">Type</label>
                                                <select name="type" class="select w-full border-base-300">
                                                    <option disabled>Select Type</option>
                                                    <option value="flat" @selected($rate['type'] == 'flat')>Flat Rate</option>
                                                    <option value="per_item" @selected($rate['type'] == 'per_item')>Per Item</option>
                                                    {{-- <option value="weight_based" @selected($rate['type'] == 'weight_based')>Weight Based
                                                    </option>
                                                    <option value="distance_based" @selected($rate['type'] == 'distance_based')>Distance
                                                        Based</option> --}}
                                                </select>
                                            </div>
                                            <div>
                                                <label class="w-full label mt-1.5 select-none">
                                                    <input type="hidden" name="is_percentage" value="0">
                                                    <input type="checkbox" name="is_percentage" value="1"
                                                        class="checkbox checkbox-sm" @checked($rate['is_percentage']) />
                                                    Percentage Rate (%)
                                                </label>
                                            </div>
                                        </div>

                                        <div class="modal-action mt-3">
                                            <button type="submit" class="btn btn-primary w-full">Update Shipping
                                                Rate</button>
                                        </div>
                                    </form>
                                </div>
                            </dialog>


                            <dialog id="delete_modal_{{ $rate['id'] }}" class="modal">
                                <div class="modal-box">
                                    <form method="dialog">
                                        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                    </form>
                                    <p class="text-lg font-semibold">Confirm Delete</p>
                                    <p class="text-sm mb-4">Are you sure you want to delete <span
                                            class="text-error">{{ $rate['name'] }}</span>?</p>
                                    <div class="modal-action">
                                        <form method="dialog"><button class="btn">Cancel</button></form>
                                        <form method="POST"
                                            action="{{ route('admin.dashboard.shipping.shipping-rate.id.delete', ['id' => $rate['id']]) }}">
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
                        <span class="font-semibold">{{ $shipping_rates->firstItem() }}</span> –
                        <span class="font-semibold">{{ $shipping_rates->lastItem() }}</span> of
                        <span class="font-semibold">{{ $shipping_rates->total() }}</span> results
                    </div>
                    <div class="join">
                        @if ($shipping_rates->onFirstPage())
                            <button class="join-item btn btn-sm btn-disabled">«</button>
                        @else
                            <a href="{{ $shipping_rates->previousPageUrl() }}" class="join-item btn btn-sm">«</a>
                        @endif
                        @for ($i = 1; $i <= $shipping_rates->lastPage(); $i++)
                            <a href="{{ $shipping_rates->url($i) }}"
                                class="join-item btn btn-sm {{ $shipping_rates->currentPage() === $i ? 'btn-active' : '' }}">{{ $i }}</a>
                        @endfor
                        @if ($shipping_rates->hasMorePages())
                            <a href="{{ $shipping_rates->nextPageUrl() }}" class="join-item btn btn-sm">»</a>
                        @else
                            <button class="join-item btn btn-sm btn-disabled">»</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <dialog id="create_shipping_rate_modal" class="modal">
            <div class="modal-box max-w-xl">
                <form method="dialog">
                    <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                </form>

                <h3 class="text-lg font-semibold text-center mb-3">Create Shipping Rate</h3>

                <form method="POST" action="{{ route('admin.dashboard.shipping.shipping-rate.post') }}">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div class="md:col-span-2">
                            <label class="text-sm">Name</label>
                            <input name="name" class="input w-full border-base-300" placeholder="Rate name">
                        </div>
                        <div>
                            <label class="text-sm">Shipping Zone</label>
                            <select name="shipping_zone_id" class="select w-full border-base-300" required>
                                <option disabled>Select Zone</option>
                                <option value="">*</option>
                                @foreach ($shipping_zones as $zone)
                                    <option value="{{ $zone['id'] }}">{{ $zone['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-sm">Shipping Method</label>
                            <select name="shipping_method_id" class="select w-full border-base-300" required>
                                <option disabled>Select Method</option>
                                <option value="">*</option>
                                @foreach ($shipping_methods as $method)
                                    <option value="{{ $method['id'] }}">{{ $method['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-sm">Shipping Class</label>
                            <select name="shipping_class_id" class="select w-full border-base-300" required>
                                <option disabled>Select Class</option>
                                <option value="">*</option>
                                @foreach ($shipping_classes as $class)
                                    <option value="{{ $class['id'] }}">{{ $class['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-sm">Description</label>
                            <textarea name="description" class="textarea w-full border-base-300" rows="2"></textarea>
                        </div>
                        <div>
                            <label class="text-sm">Flat Cost</label>
                            <input name="cost" type="number" step="0.01" class="input w-full border-base-300"
                                placeholder="Enter Cost" required>
                        </div>
                        <div>
                            <label class="text-sm">Type</label>
                            <select name="type" class="select w-full border-base-300" required>
                                <option disabled>Select Type</option>
                                {{-- flat,per_item,weight_based,distance_based --}}
                                <option value="flat">Flat Rate</option>
                                <option value="per_item">Per Item</option>
                                {{-- <option value="weight_based">Weight Based</option>
                                <option value="distance_based">Distance Based</option> --}}
                            </select>
                        </div>
                        <div>
                            <label class="w-full label mt-1.5 select-none">
                                <input type="hidden" name="is_percentage" value="0">
                                <input type="checkbox" name="is_percentage" value="1"
                                    class="checkbox checkbox-sm" />
                                Percentage Rate (%)
                            </label>
                        </div>
                    </div>

                    <div class="modal-action mt-3">
                        <button type="submit" class="btn btn-primary w-full">Create Shipping Rate</button>
                    </div>
                </form>
            </div>
        </dialog>
    </div>
@endsection
