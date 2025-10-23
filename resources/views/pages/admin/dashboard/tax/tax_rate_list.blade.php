@extends('layouts.admin.admin_dashboard')

@section('admin_dashboard_content')
    <div class="p-5 min-h-screen">
        <p class="lg:text-lg font-semibold mb-3">Tax Rates</p>

        <button class="btn btn-primary" onclick="create_tax_rate_modal.showModal()">Create Tax Rate</button>

        <div class="card shadow-sm border border-base-300 mt-4">
            <div class="card-body p-0 m-0 overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Name</th>
                            <th>Zone</th>
                            <th>Class</th>
                            <th>Type</th>
                            <th>Rate</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tax_rates as $rate)
                            <tr>
                                <td>{{ $loop->iteration + ($tax_rates->currentPage() - 1) * $tax_rates->perPage() }}</td>
                                <td>
                                    <p onclick="document.getElementById('detail_modal_{{ $rate['id'] }}').showModal()"
                                        class="cursor-pointer hover:underline">{{ $rate['name'] }}</p>
                                </td>
                                <td>{{ $rate['zone']['name'] ?? '*' }}</td>
                                <td>{{ $rate['class']['name'] ?? '*' }}</td>
                                <td>{{ $rate['is_percentage'] ? 'Percentage Rate' : 'Flat Rate' }}</td>
                                <td>{{ $rate['rate'] }}</td>
                                <td>{{ $rate['description'] ?? '-' }}</td>
                                <td>
                                    <div tabindex="0" role="button" class="dropdown dropdown-left">
                                        <div class="btn btn-square btn-sm btn-ghost">
                                            <i data-lucide="ellipsis-vertical" class="size-5"></i>
                                        </div>
                                        <ul tabindex="0"
                                            class="menu dropdown-content bg-base-100 border border-base-300 w-30 rounded-box p-1 shadow-sm">
                                            <li><button
                                                    onclick="document.getElementById('detail_modal_{{ $rate['id'] }}').showModal()">View</button>
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

                            {{-- Detail Modal --}}
                            <dialog id="detail_modal_{{ $rate['id'] }}" class="modal">
                                <div class="modal-box max-w-xl">
                                    <form method="dialog">
                                        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                    </form>
                                    <h3 class="text-lg font-semibold text-center mb-3">Tax Rate Details</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <div>
                                            <label class="text-sm">ID</label>
                                            <input type="text" class="input w-full" value="{{ $rate['id'] }}"
                                                readonly>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="text-sm">Name</label>
                                            <input type="text" class="input w-full" value="{{ $rate['name'] }}"
                                                readonly>
                                        </div>
                                        <div>
                                            <label class="text-sm">Zone</label>
                                            <input type="text" class="input w-full"
                                                value="{{ $rate['zone']['name'] ?? '*' }}" readonly>
                                        </div>
                                        <div>
                                            <label class="text-sm">Class</label>
                                            <input type="text" class="input w-full"
                                                value="{{ $rate['class']['name'] ?? '*' }}" readonly>
                                        </div>
                                        <div>
                                            <label class="text-sm">Type</label>
                                            <input type="text" class="input w-full"
                                                value="{{ $rate['is_percentage'] ? 'Percentage Rate' : 'Flat Rate' }}"
                                                readonly>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="text-sm">Rate</label>
                                            <input type="text" class="input w-full" value="{{ $rate['rate'] }}"
                                                readonly>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="text-sm">Description</label>
                                            <textarea class="textarea w-full" readonly>{{ $rate['description'] ?? '-' }}</textarea>
                                        </div>
                                    </div>
                                    <div class="modal-action">
                                        <form method="dialog" class="w-full">
                                            <button class="btn ">Close</button>
                                        </form>
                                    </div>
                                </div>
                            </dialog>

                            {{-- Edit Modal --}}
                            <dialog id="edit_modal_{{ $rate['id'] }}" class="modal">
                                <div class="modal-box max-w-xl">
                                    <form method="dialog">
                                        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                    </form>
                                    <h3 class="text-lg font-semibold text-center mb-3">Edit Tax Rate</h3>
                                    <form method="POST"
                                        action="{{ route('admin.dashboard.tax.tax-rate.id.post', ['id' => $rate['id']]) }}">
                                        @csrf
                                        @method('POST')
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                            <div class="md:col-span-2">
                                                <label class="text-sm">Name</label>
                                                <input name="name" class="input w-full" value="{{ $rate['name'] }}"
                                                    required>
                                            </div>
                                            <div>
                                                <label class="text-sm">Zone</label>
                                                <select name="tax_zone_id" class="select w-full">
                                                    <option value="">*</option>
                                                    @foreach ($tax_zones as $zone)
                                                        <option value="{{ $zone['id'] }}"
                                                            @if ($zone['id'] == $rate['tax_zone_id']) selected @endif>
                                                            {{ $zone['name'] }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <label class="text-sm">Class</label>
                                                <select name="tax_class_id" class="select w-full">
                                                    <option value="">*</option>
                                                    @foreach ($tax_classes as $class)
                                                        <option value="{{ $class['id'] }}"
                                                            @if ($class['id'] == $rate['tax_class_id']) selected @endif>
                                                            {{ $class['name'] }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <label class="text-sm">Type</label>
                                                <select name="is_percentage" class="select w-full">
                                                    <option value="0" @selected(!$rate['is_percentage'])>Flat</option>
                                                    <option value="1" @selected($rate['is_percentage'])>Percentage
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="text-sm">Rate</label>
                                                <input name="rate" type="number" step="0.01" class="input w-full"
                                                    value="{{ $rate['rate'] }}">
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="text-sm">Description</label>
                                                <textarea name="description" class="textarea w-full">{{ $rate['description'] }}</textarea>
                                            </div>
                                        </div>
                                        <div class="modal-action mt-3">
                                            <button type="submit" class="btn btn-primary ">Update Tax Rate</button>
                                        </div>
                                    </form>
                                </div>
                            </dialog>

                            {{-- Delete Modal --}}
                            <dialog id="delete_modal_{{ $rate['id'] }}" class="modal">
                                <div class="modal-box">
                                    <form method="dialog">
                                        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                    </form>
                                    <p class="text-lg font-semibold">Confirm Delete</p>
                                    <p class="text-sm mb-4">Are you sure you want to delete <span
                                            class="text-error">{{ $rate['name'] }}</span>?</p>
                                    <div class="modal-action">
                                        <form method="dialog">
                                            <button class="btn">Cancel</button>
                                        </form>
                                        <form method="POST"
                                            action="{{ route('admin.dashboard.tax.tax-rate.id.delete', ['id' => $rate['id']]) }}">
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
                        <span class="font-semibold">{{ $tax_rates->firstItem() }}</span> –
                        <span class="font-semibold">{{ $tax_rates->lastItem() }}</span> of
                        <span class="font-semibold">{{ $tax_rates->total() }}</span> results
                    </div>
                    <div class="join">
                        @if ($tax_rates->onFirstPage())
                            <button class="join-item btn btn-sm btn-disabled">«</button>
                        @else
                            <a href="{{ $tax_rates->previousPageUrl() }}" class="join-item btn btn-sm">«</a>
                        @endif
                        @for ($i = 1; $i <= $tax_rates->lastPage(); $i++)
                            <a href="{{ $tax_rates->url($i) }}"
                                class="join-item btn btn-sm {{ $tax_rates->currentPage() === $i ? 'btn-active' : '' }}">{{ $i }}</a>
                        @endfor
                        @if ($tax_rates->hasMorePages())
                            <a href="{{ $tax_rates->nextPageUrl() }}" class="join-item btn btn-sm">»</a>
                        @else
                            <button class="join-item btn btn-sm btn-disabled">»</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Create Modal --}}
        <dialog id="create_tax_rate_modal" class="modal">
            <div class="modal-box max-w-xl">
                <form method="dialog"><button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                </form>
                <h3 class="text-lg font-semibold text-center mb-3">Create Tax Rate</h3>
                <form method="POST" action="{{ route('admin.dashboard.tax.tax-rate.post') }}">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div class="md:col-span-2">
                            <label class="text-sm">Name</label>
                            <input name="name" class="input w-full" placeholder="Tax Rate Name" required>
                        </div>
                        <div>
                            <label class="text-sm">Zone</label>
                            <select name="tax_zone_id" class="select w-full">
                                <option value="">*</option>
                                @foreach ($tax_zones as $zone)
                                    <option value="{{ $zone['id'] }}">{{ $zone['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-sm">Class</label>
                            <select name="tax_class_id" class="select w-full">
                                <option value="">*</option>
                                @foreach ($tax_classes as $class)
                                    <option value="{{ $class['id'] }}">{{ $class['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-sm">Type</label>
                            <select name="is_percentage" class="select w-full">
                                <option value="0">Flat</option>
                                <option value="1">Percentage</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-sm">Rate</label>
                            <input name="rate" type="number" step="0.01" class="input w-full"
                                placeholder="Enter Rate">
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-sm">Description</label>
                            <textarea name="description" class="textarea w-full"></textarea>
                        </div>
                    </div>
                    <div class="modal-action mt-3">
                        <button type="submit" class="btn btn-primary ">Create Tax Rate</button>
                    </div>
                </form>
            </div>
        </dialog>
    </div>
@endsection
