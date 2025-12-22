@php
    $site_currency = getParsedTemplate('site_currency');
@endphp
@extends('layouts.admin.admin_dashboard')

@section('admin_dashboard_content')
    <div class="p-3 lg:p-5 min-h-screen">
        <p class="lg:text-lg font-semibold">Product Inventory Logs</p>

        <div class="mt-3 flex xl:flex-row flex-col justify-between gap-2">
            <div class="flex flex-row gap-2 flex-wrap" x-data>
                <div class="join join-horizontal">
                    <select class="select select-sm join-item" x-model="$store.bulk.current_action">
                        <option value="">Bulk Actions</option>
                        <option value="bulk_delete_selected">Delete Selected</option>
                        <option value="bulk_delete_all">Delete All</option>
                    </select>
                    <button class="join-item btn btn-sm" @click="$store.bulk.commit()">Commit</button>
                </div>
                <button class="btn btn-sm shadow-none" onclick="document.getElementById('create_log_modal').showModal()">Add
                    Transaction</button>
            </div>

            {{-- bulk delete selected modal --}}
            <dialog id="bulk_delete_selected" class="modal" x-data="{ loading: false }">
                <div class="modal-box relative">
                    <form method="dialog">
                        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                    </form>

                    <p class="text-lg font-semibold py-0">Confirm Delete</p>
                    <p class="py-2 mb-0 text-sm">
                        Are you sure you want to delete
                        <span class="italic text-error">Selected Logs</span>?
                    </p>

                    <template x-if="$store.bulk.hasCandidates">
                        <ul class="text-sm pl-10 list-decimal max-h-24 overflow-y-auto bg-base-200 p-3">
                            <template x-for="id in $store.bulk.candidates" :key="id">
                                <li x-text="'Log ID: ' + id"></li>
                            </template>
                        </ul>
                    </template>

                    <div class="mt-3 modal-action">
                        <form method="dialog"><button class="btn" :disabled="loading">Cancel</button></form>

                        <form method="POST" action="{{ route('admin.dashboard.product.inventory.bulk.delete-selected') }}"
                            @submit="loading = true">
                            @csrf
                            @method('DELETE')
                            <template x-for="id in $store.bulk.candidates" :key="id">
                                <input type="hidden" name="ids[]" :value="id">
                            </template>
                            <button type="submit" class="btn btn-error flex items-center gap-2" :disabled="loading">
                                <span x-show="!loading">Delete Candidates</span>
                                <span x-show="loading" class="loading loading-spinner loading-xs"></span>
                                <span x-show="loading">Deleting...</span>
                            </button>
                        </form>
                    </div>
                </div>
            </dialog>

            {{-- bulk delete all modal --}}
            <dialog id="bulk_delete_all" class="modal" x-data="{ loading: false }">
                <div class="modal-box relative">
                    <form method="dialog">
                        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                    </form>

                    <p class="text-lg font-semibold">Confirm Delete</p>
                    <p class="text-sm mb-4">
                        Are you sure you want to delete
                        <span class="text-error">All Logs</span>?
                    </p>

                    <div class="modal-action">
                        <form method="dialog"><button class="btn" :disabled="loading">Cancel</button></form>

                        <form method="POST" action="{{ route('admin.dashboard.product.inventory.bulk.delete-all') }}"
                            @submit="loading = true">
                            @csrf
                            @method('DELETE')
                            <template x-for="id in $store.bulk.candidates" :key="id">
                                <input type="hidden" name="ids[]" :value="id">
                            </template>
                            <button type="submit" class="btn btn-error flex items-center gap-2" :disabled="loading">
                                <span x-show="!loading">Delete</span>
                                <span x-show="loading" class="loading loading-spinner loading-xs"></span>
                                <span x-show="loading">Deleting...</span>
                            </button>
                        </form>
                    </div>
                </div>
            </dialog>


            {{-- Filtering Section --}}
            <div class="flex flex-row flex-wrap justify-start xl:justify-end gap-2">
                {{-- product searching --}}
                <form id="queryForm" method="GET" action="{{ request()->url() }}" class="join join-horizontal">
                    <template x-data x-if="$store.product_inventory_search_setting.sortBy">
                        <input type="hidden" name="sortBy" :value="$store.product_inventory_search_setting.sortBy">
                    </template>

                    <template x-data x-if="$store.product_inventory_search_setting.perPage">
                        <input type="hidden" x-data name="perPage"
                            :value="$store.product_inventory_search_setting.perPage">
                    </template>

                    <template x-data x-if="$store.product_inventory_search_setting.orderBy">
                        <input type="hidden" x-data name="orderBy"
                            :value="$store.product_inventory_search_setting.orderBy">
                    </template>

                    <input type="text" x-data x-cloak class="join-item input input-sm rounded-l-box" name="query"
                        :value="$store.product_inventory_search_setting.query"
                        @change="$store.product_inventory_search_setting.query = $event.target.value; $store.product_inventory_search_setting.save(); $el.form.submit()">

                    <button class="join-item btn btn-sm">Search</button>
                </form>

                {{-- page limiting --}}
                <form method="GET" action="{{ request()->url() }}">
                    <template x-data
                        x-if="$store.product_inventory_search_setting.query && $store.product_inventory_search_setting.query.length > 0">
                        <input type="hidden" name="query" :value="$store.product_inventory_search_setting.query">
                    </template>

                    <template x-data x-if="$store.product_inventory_search_setting.sortBy">
                        <input type="hidden" x-data name="sortBy"
                            :value="$store.product_inventory_search_setting.sortBy">
                    </template>

                    <template x-data x-if="$store.product_inventory_search_setting.orderBy">
                        <input type="hidden" x-data name="orderBy"
                            :value="$store.product_inventory_search_setting.orderBy">
                    </template>

                    <select name="perPage" x-data x-cloak class="select select-sm w-fit shrink-0" x-data
                        x-model="$store.product_inventory_search_setting.perPage"
                        @change="$store.product_inventory_search_setting.perPage = $event.target.value; $store.product_inventory_search_setting.save(); $el.form.submit()">
                        <option value="5">Show 5</option>
                        <option value="10">Show 10</option>
                        <option value="20">Show 20</option>
                        <option value="50">Show 50</option>
                    </select>
                </form>

                {{-- ascending & descending --}}
                <form method="GET" action="{{ request()->url() }}">
                    <template x-data
                        x-if="$store.product_inventory_search_setting.query && $store.product_inventory_search_setting.query.length > 0">
                        <input type="hidden" name="query" :value="$store.product_inventory_search_setting.query">
                    </template>

                    <template x-data x-if="$store.product_inventory_search_setting.perPage">
                        <input type="hidden" x-data name="perPage"
                            :value="$store.product_inventory_search_setting.perPage">
                    </template>

                    <template x-data x-if="$store.product_inventory_search_setting.sortBy">
                        <input type="hidden" x-data name="sortBy"
                            :value="$store.product_inventory_search_setting.sortBy">
                    </template>

                    <select name="orderBy" x-data x-cloak x-model="$store.product_inventory_search_setting.orderBy"
                        class="select select-sm w-fit shrink-0" x-data
                        :value="$store.product_inventory_search_setting.orderBy"
                        @change="$store.product_inventory_search_setting.orderBy = $event.target.value; $store.product_inventory_search_setting.save() ; $el.form.submit()">
                        <option value="desc">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 4.5h14.25M3 9h9.75M3 13.5h9.75m4.5-4.5v12m0 0-3.75-3.75M17.25 21 21 17.25" />
                            </svg>
                            Descending
                        </option>
                        <option value="asc">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 4.5h14.25M3 9h9.75M3 13.5h5.25m5.25-.75L17.25 9m0 0L21 12.75M17.25 9v12" />
                            </svg>
                            Ascending
                        </option>
                    </select>
                </form>

                {{-- sorting --}}
                <form method="GET" action="{{ request()->url() }}">
                    <template x-data
                        x-if="$store.product_inventory_search_setting.query && $store.product_inventory_search_setting.query.length > 0">
                        <input type="hidden" name="query" :value="$store.product_inventory_search_setting.query">
                    </template>

                    <template x-data x-if="$store.product_inventory_search_setting.perPage">
                        <input type="hidden" x-data name="perPage"
                            :value="$store.product_inventory_search_setting.perPage">
                    </template>

                    <template x-data x-if="$store.product_inventory_search_setting.orderBy">
                        <input type="hidden" x-data name="orderBy"
                            :value="$store.product_inventory_search_setting.orderBy">
                    </template>

                    <select name="sortBy" class="select select-sm w-fit shrink-0" x-data x-cloak
                        :value="$store.product_inventory_search_setting.sortBy"
                        @change="$store.product_inventory_search_setting.sortBy = $event.target.value; $store.product_inventory_search_setting.save() ; $el.form.submit()">
                        <option value="last_updated">Sort By Last Updated
                        </option>
                        <option value="last_created">Sort By Last Created
                        </option>
                    </select>
                </form>

                {{-- <button class="btn btn-sm bg-base-100 font-normal shadow-none border-slate-300" x-data x-transition x-cloak
                    @click="$store.product_inventory_search_setting.showFilterOption = !$store.product_inventory_search_setting.showFilterOption;$store.product_inventory_search_setting.save()">
                    <span x-text="$store.product_inventory_search_setting.showFilterOption ? 'Hide Filter' : 'Filter Option'"></span>
                </button> --}}
                <button class="btn btn-sm bg-base-100 font-normal shadow-none border-slate-300" x-data x-transition x-cloak
                    @click="$store.product_inventory_search_setting.showDisplayOption = !$store.product_inventory_search_setting.showDisplayOption;$store.product_inventory_search_setting.save()">
                    <span
                        x-text="$store.product_inventory_search_setting.showDisplayOption ? 'Hide Display' : 'Display Option'"></span>
                </button>
            </div>
        </div>

        {{-- filtering --}}
        <div class="mt-3 p-3 border border-base-300 rounded-md flex flex-col gap-2" x-cloak x-transition x-data
            x-show="$store.product_inventory_search_setting.showFilterOption">
            <p class="text-xs">Filter Options</p>
            <div class="grid grid-cols-2 md:grid-cols-4 xl:grid-cols-6 gap-3">
                {{-- <div class="flex flex-col gap-1">
                    <select x-data class="select select-sm text-xs" name="brand"
                        :value="$store.product_inventory_search_setting.pinnedFilter ? 'true' : 'false'"
                        @change="$store.product_inventory_search_setting.pinnedFilter = $event.target.value; $store.product_inventory_search_setting.save()">
                        <option value="false">Filter Pinned</option>
                        <option value="true">Pinned Product</option>
                    </select>
                </div> --}}
            </div>
            <div class="flex flex-row gap-2">
                {{-- <button class="btn btn-primary btn-sm">Save</button> --}}
                <button class="btn btn-sm" x-data
                    @click="$store.product_inventory_search_setting.resetFilter()">Reset</button>
            </div>
        </div>

        {{-- column displaying --}}
        <div class="mt-3 p-3 border border-base-300 rounded-md flex flex-col gap-2" x-cloak x-transition x-data
            x-show="$store.product_inventory_search_setting.showDisplayOption">
            <p class="text-xs">Display Options</p>
            <div class="flex sm:flex-row flex-col flex-wrap gap-3">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" class="checkbox checkbox-xs rounded-sm" x-data
                        :checked="$store.product_inventory_search_setting.showIDColumn"
                        @change="$store.product_inventory_search_setting.showIDColumn = !$store.product_inventory_search_setting.showIDColumn; $store.product_inventory_search_setting.save()">
                    <span class="text-xs">Show ID</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" class="checkbox checkbox-xs rounded-sm" x-data
                        :checked="$store.product_inventory_search_setting.showUpdatedTimeColumn || $store
                            .product_inventory_search_setting
                            .is_last_updated_filter"
                        @change="$store.product_inventory_search_setting.showUpdatedTimeColumn = !$store.product_inventory_search_setting.showUpdatedTimeColumn; $store.product_inventory_search_setting.save()">
                    <span class="text-xs">Show Updated Time</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" class="checkbox checkbox-xs rounded-sm" x-data
                        :checked="$store.product_inventory_search_setting.showCreatedTimeColumn || $store
                            .product_inventory_search_setting
                            .is_last_created_filter"
                        @change="$store.product_inventory_search_setting.showCreatedTimeColumn = !$store.product_inventory_search_setting.showCreatedTimeColumn; $store.product_inventory_search_setting.save()">
                    <span class="text-xs">Show Created Time</span>
                </label>
            </div>
        </div>

        <div class="card shadow-sm border border-base-300 mt-4">
            <div class="card-body p-0 m-0 overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="w-[10px]">
                                <input type="checkbox" class="checkbox checkbox-xs rounded-sm" x-data
                                    :checked="$store.bulk.isAllSelected()"
                                    @change="$store.bulk.toggleSelectAll($el.checked)">
                            </th>
                            <th class="w-[10px]">No.</th>
                            <th x-cloak x-data x-show="$store.product_inventory_search_setting.showIDColumn"
                                class="w-[50px]">ID
                            </th>
                            <th>Product Name</th>
                            <th>Reference</th>
                            <th>Action</th>
                            <th>Stock Before</th>
                            <th>Quantity</th>
                            <th>Stock After</th>
                            {{-- <th>Note</th> --}}
                            <th x-cloak x-data
                                x-show="$store.product_inventory_search_setting.showUpdatedTimeColumn || $store.product_inventory_search_setting.is_last_updated_filter">
                                Updated At</th>
                            <th x-cloak x-data
                                x-show="$store.product_inventory_search_setting.showCreatedTimeColumn || $store.product_inventory_search_setting.is_last_created_filter">
                                Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($logs as $log)
                            <tr>
                                <td>
                                    <input type="checkbox" class="checkbox checkbox-xs rounded-sm row-checkbox"
                                        data-id="{{ $log['id'] }}" x-data
                                        :checked="$store.bulk.candidates.includes({{ $log['id'] }})"
                                        @change="$store.bulk.toggleCandidate({{ $log['id'] }})">
                                </td>
                                <td>{{ $loop->iteration + ($logs->currentPage() - 1) * $logs->perPage() }}</td>
                                <td x-cloak x-data x-show="$store.product_inventory_search_setting.showIDColumn">
                                    {{ $log['id'] }}
                                </td>
                                <td class="max-w-[300px] truncate">

                                    <p onclick="document.getElementById('detail_modal_{{ $log['id'] }}').showModal()"
                                        class="cursor-pointer hover:underline">
                                        {{ $log['variant_id'] ? $log['product']['name'] . ' [Variant-' . $log['variant']['sku'] . ']' : $log['product']['name'] }}
                                    </p>
                                </td>
                                <td>
                                    <div class="badge badge-ghost border border-base-300 text-xs capitalize">
                                        {{ strtoupper($log['reference_type']) }}
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $color = match ($log['action_type']) {
                                            'out' => 'badge-error',
                                            'in' => 'badge-success',
                                            'reset' => 'badge-warning',
                                            default => 'badge-ghost',
                                        };
                                    @endphp
                                    <div class="badge {{ $color }} border border-base-300 text-xs capitalize">
                                        {{ strtoupper($log['action_type']) }}
                                    </div>
                                </td>
                                <td>{{ $log['stock_before'] ?? '-' }}</td>
                                <td>{{ $log['quantity'] ?? '-' }}</td>
                                <td>{{ $log['stock_after'] ?? '-' }}</td>
                                {{-- <td class="max-w-[200px] truncate">{{ $log['note'] ?? '-' }}</td> --}}
                                <td x-cloak x-data
                                    x-show="$store.product_inventory_search_setting.showUpdatedTimeColumn || $store.product_inventory_search_setting.is_last_updated_filter">
                                    {{ $log['updated_at'] ? \Carbon\Carbon::parse($log['updated_at'])->format('Y-m-d h:i A') : '-' }}
                                </td>
                                <td x-cloak x-data
                                    x-show="$store.product_inventory_search_setting.showCreatedTimeColumn || $store.product_inventory_search_setting.is_last_created_filter">
                                    {{ $log['created_at'] ? \Carbon\Carbon::parse($log['created_at'])->format('Y-m-d h:i A') : '-' }}
                                </td>
                                <td>
                                    <div tabindex="0" role="button" class="dropdown dropdown-left">
                                        <div class="btn btn-square btn-sm btn-ghost">
                                            <i data-lucide="ellipsis-vertical" class="size-5"></i>
                                        </div>
                                        <ul tabindex="0"
                                            class="menu dropdown-content bg-base-100 border border-base-300 w-36 rounded-box p-1 shadow-sm">
                                            <li><button
                                                    onclick="document.getElementById('edit_modal_{{ $log['id'] }}').showModal()">Edit</button>
                                            </li>
                                            <li><button class="text-error"
                                                    onclick="document.getElementById('delete_modal_{{ $log['id'] }}').showModal()">Delete</button>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>

                            <dialog id="detail_modal_{{ $log['id'] }}" class="modal">
                                <div class="modal-box max-w-3xl max-h-[85vh] overflow-y-auto">
                                    <form method="dialog">
                                        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                    </form>

                                    <h3 class="text-lg font-semibold text-center mb-3">Inventory Log Details</h3>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <div class="md:col-span-2">
                                            <label class="text-sm ">Log ID</label>
                                            <input type="text" class="input w-full border-base-300"
                                                value="{{ $log['id'] }}" readonly>
                                        </div>

                                        <div>
                                            <label class="text-sm">Product ID</label>
                                            <input type="text" class="input w-full border-base-300"
                                                value="{{ $log['product_id'] }}" readonly>
                                        </div>

                                        <div>
                                            <label class="text-sm">Variant ID</label>
                                            <input type="text" class="input w-full border-base-300"
                                                value="{{ $log['variant_id'] ?? 'N/A' }}" readonly>
                                        </div>

                                        <div class="md:col-span-2">
                                            <label class="text-sm">Product Name</label>
                                            <input type="text" class="input w-full border-base-300"
                                                value="{{ $log['variant_id'] ? $log['product']['name'] . ' [Variant-' . $log['variant']['sku'] . ']' : $log['product']['name'] }}"
                                                readonly>
                                        </div>

                                        <div>
                                            <label class="text-sm">Action Type</label>
                                            <input type="text" class="input w-full border-base-300"
                                                value="{{ strtoupper($log['action_type']) }}" readonly>
                                        </div>

                                        <div>
                                            <label class="text-sm">Quantity</label>
                                            <input type="text" class="input w-full border-base-300"
                                                value="{{ $log['quantity'] }}" readonly>
                                        </div>

                                        <div>
                                            <label class="text-sm">Stock Before</label>
                                            <input type="text" class="input w-full border-base-300"
                                                value="{{ $log['stock_before'] }}" readonly>
                                        </div>

                                        <div>
                                            <label class="text-sm">Stock After</label>
                                            <input type="text" class="input w-full border-base-300"
                                                value="{{ $log['stock_after'] }}" readonly>
                                        </div>

                                        <div>
                                            <label class="text-sm">Reference Type</label>
                                            <input type="text" class="input w-full border-base-300"
                                                value="{{ strtoupper($log['reference_type']) }}" readonly>
                                        </div>

                                        <div>
                                            <label class="text-sm">Reference ID</label>
                                            <input type="text" class="input w-full border-base-300"
                                                value="{{ $log['reference_id'] ?? 'N/A' }}" readonly>
                                        </div>

                                        <div>
                                            <label class="text-sm">Created By</label>
                                            <div class="flex flex-row gap-1 items-center">
                                                <img src="{{ $log['creator']['profile'] ?? asset('assets/images/blank_profile.png') }}"
                                                    alt="Profile" class="size-7 rounded-full">
                                                <p>
                                                    {{ $log['creator']['name'] }} -
                                                    {{ $log['creator']['role']['display_name'] }}
                                                    [#{{ $log['creator']['id'] }}]
                                                </p>
                                            </div>

                                        </div>

                                        <div class="md:col-span-2">
                                            <label class="text-sm">Note</label>
                                            <textarea class="textarea w-full border-base-300" readonly>{{ $log['note'] ?? '-' }}</textarea>
                                        </div>

                                        <div>
                                            <label class="text-sm">Updated At</label>
                                            <input type="text" class="input w-full border-base-300"
                                                value="{{ \Carbon\Carbon::parse($log['updated_at'])->format('Y-m-d h:i A') }}"
                                                readonly>
                                        </div>

                                        <div>
                                            <label class="text-sm">Created At</label>
                                            <input type="text" class="input w-full border-base-300"
                                                value="{{ \Carbon\Carbon::parse($log['created_at'])->format('Y-m-d h:i A') }}"
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
                            <dialog id="edit_modal_{{ $log['id'] }}" class="modal" x-data="{ submitting: false }"
                                @submit="submitting=true">
                                <div class="modal-box max-w-2xl max-h-[85vh] overflow-y-auto">
                                    <form method="dialog">
                                        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                    </form>

                                    <h3 class="text-lg font-semibold text-center mb-3">Edit Inventory Log</h3>

                                    <form method="POST"
                                        action="{{ route('admin.dashboard.product.inventory.id.post', $log['id']) }}">
                                        @csrf
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                            <div class="flex flex-col gap-1">
                                                <label>ID</label>
                                                <input type="text" class="input w-full" value="{{ $log['id'] }}"
                                                    readonly>
                                            </div>

                                            <div class="flex flex-col gap-1">
                                                <label>Product ID</label>
                                                <input type="text" class="input w-full"
                                                    value="{{ $log['product_id'] }}" readonly>
                                            </div>

                                            <div class="flex flex-col gap-1 md:col-span-2">
                                                <label>Note</label>
                                                <textarea name="note" class="textarea w-full" rows="4">{{ $log['note'] }}</textarea>
                                            </div>
                                        </div>


                                        <div class="modal-action mt-3">
                                            <button type="submit" class="btn btn-primary" :disabled="submitting">
                                                <span x-show="submitting"
                                                    class="loading loading-spinner loading-sm mr-2"></span>
                                                <span x-show="submitting">Saving Log</span>
                                                <span x-show="!submitting">
                                                    Update Log
                                                </span>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </dialog>


                            <!-- Delete Modal -->
                            <dialog id="delete_modal_{{ $log['id'] }}" class="modal">
                                <div class="modal-box">
                                    <form method="dialog">
                                        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                    </form>
                                    <p class="text-lg font-semibold">Confirm Delete</p>
                                    <p class="text-sm mb-4">Are you sure you want to delete this store branch <span
                                            class="text-error">{{ $log['product']['name'] }}</span>?</p>
                                    <div class="modal-action">
                                        <form method="dialog"><button class="btn">Cancel</button></form>
                                        <form method="POST"
                                            action="{{ route('admin.dashboard.product.inventory.id.delete', $log['id']) }}">
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
                        <span class="font-semibold">{{ $logs->firstItem() }}</span>
                        –
                        <span class="font-semibold">{{ $logs->lastItem() }}</span>
                        of
                        <span class="font-semibold">{{ $logs->total() }}</span>
                        results
                    </div>

                    <div class="join">
                        @if ($logs->onFirstPage())
                            <button class="join-item btn btn-sm btn-disabled">«</button>
                        @else
                            <a href="{{ $logs->previousPageUrl() }}" class="join-item btn btn-sm">«</a>
                        @endif

                        <a href="{{ $logs->url(1) }}"
                            class="join-item btn btn-sm {{ $logs->currentPage() === 1 ? 'btn-active' : '' }}">
                            1
                        </a>

                        @php
                            $start = max(2, $logs->currentPage() - 1);
                            $end = min($logs->lastPage() - 1, $logs->currentPage() + 1);
                        @endphp

                        @for ($i = $start; $i <= $end; $i++)
                            <a href="{{ $logs->url($i) }}"
                                class="join-item btn btn-sm {{ $logs->currentPage() === $i ? 'btn-active' : '' }}">
                                {{ $i }}
                            </a>
                        @endfor

                        @if ($logs->lastPage() > 1)
                            <a href="{{ $logs->url($logs->lastPage()) }}"
                                class="join-item btn btn-sm {{ $logs->currentPage() === $logs->lastPage() ? 'btn-active' : '' }}">
                                {{ $logs->lastPage() }}
                            </a>
                        @endif

                        @if ($logs->hasMorePages())
                            <a href="{{ $logs->nextPageUrl() }}" class="join-item btn btn-sm">»</a>
                        @else
                            <button class="join-item btn btn-sm btn-disabled">»</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Create Inventory Modal -->
        <dialog id="create_log_modal" class="modal">
            <div class="modal-box max-w-2xl max-h-[85vh] overflow-y-auto" x-data="{ submitting: false }">
                <form method="dialog">
                    <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                </form>

                <h3 class="text-lg font-semibold text-center mb-3">Create Inventory Transaction</h3>

                <form method="POST" action="{{ route('admin.dashboard.product.inventory.post') }}"
                    @submit="submitting=true">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">

                        <div class="w-full flex flex-col gap-2 md:col-span-2" x-data="searchProductForm()">
                            <label class="text-sm font-semibold mb-1 flex items-center gap-3">Search Product</label>
                            <input type="text" x-model.debounce.400ms="productQuery"
                                placeholder="Type product name..." class="input input-bordered w-full" />

                            <template x-if="productLoading">
                                <p class="text-gray-500 text-sm mt-3">Searching products...</p>
                            </template>

                            <template x-if="productResults.length > 0">
                                <ul
                                    class="border border-base-300 rounded-box mt-3 divide-y divide-base-300 max-h-48 overflow-y-auto">
                                    <template x-for="item,index in productResults" :key="index">
                                        <li @click="selectProduct(item)"
                                            class="px-3 py-2 cursor-pointer hover:bg-base-200 flex justify-between text-sm">
                                            <span x-text="item.name"></span>
                                            <span class="text-gray-500"><span x-text="item.regular_price"></span>
                                                {{ $site_currency }} [Stock <span x-text="item.stock"></span>]</span>

                                        </li>
                                    </template>
                                </ul>
                            </template>

                            <div class="pt-2">
                                <p class="font-medium mb-1 text-sm">Selected Product</p>
                                <template x-if="!selectedProduct">
                                    <p class="text-sm text-gray-500">No product selected</p>
                                </template>
                                <template x-if="selectedProduct">
                                    <div
                                        class="flex justify-between items-center mb-1 border border-base-300 rounded-box py-2 px-3">
                                        <span class=""
                                            x-text="`${selectedProduct.name} [Stock - ${selectedProduct.stock}]`"></span>
                                        <button type="button" @click="unselectProduct(index)"
                                            class="btn btn-xs btn-ghost btn-square">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-4 stroke-error">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                        <input type="hidden" name="product_id" :value="selectedProduct.id">
                                        <input type="hidden" name="variant_id" :value="selectedProduct.variant_id">
                                    </div>
                                </template>
                            </div>
                        </div>

                        {{-- <div class="flex flex-col gap-1 md:col-span-2">
                            <label>Product ID</label>
                            <input type="text" name="product_id" class="input w-full" required>
                        </div> --}}

                        <div class="flex flex-col gap-1">
                            <label>Action Type</label>
                            <select name="action_type" class="select w-full">
                                <option value="in" selected>Add (+)</option>
                                <option value="out">Remove (-)</option>
                                <option value="reset">Reset (#)</option>
                            </select>
                        </div>

                        <div class="flex flex-col gap-1">
                            <label>Quantity</label>
                            <input type="number" name="quantity" class="input w-full" required>
                        </div>

                        <div class="flex flex-col gap-1 md:col-span-2">
                            <label>Note</label>
                            <textarea name="note" class="textarea w-full border-base-300" rows="4"></textarea>
                        </div>
                    </div>

                    <div class="modal-action mt-3">
                        <button type="submit" class="btn btn-primary" :disabled="submitting">
                            <span x-show="submitting" class="loading loading-spinner loading-sm mr-2"></span>
                            <span x-show="submitting">Creating Log</span>
                            <span x-show="!submitting">
                                Create Transaction
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </dialog>

    </div>
@endsection


@push('script')
    <script>
        function searchProductForm() {
            return {
                productQuery: '',
                productResults: [],
                selectedProduct: null,
                productLoading: false,

                async init() {
                    this.$watch('productQuery', () => this.searchProducts());
                },

                async searchProducts() {
                    if (this.productQuery.length < 2) {
                        this.productResults = [];
                        return;
                    }
                    this.productLoading = true;
                    try {
                        const response = await axios.get(
                            `/admin/dashboard/product/inventory/search?q=${this.productQuery}`);
                        this.productResults = response.data.data ?? [];

                    } catch (e) {
                        console.error(e);
                    } finally {
                        this.productLoading = false;
                    }
                },

                selectProduct(product) {
                    this.productQuery = '';
                    this.selectedProduct = product;
                },

                unselectProduct(index) {
                    this.selectedProduct = null;
                },
            };
        }


        document.addEventListener('alpine:init', function() {

            Alpine.store('bulk', {
                candidates: [],
                current_action: '',
                loading: false,

                get hasCandidates() {
                    return this.candidates.length > 0;
                },

                isAllSelected() {
                    const visibleIds = Array.from(document.querySelectorAll('.row-checkbox')).map(cb =>
                        Number(cb.dataset.id));
                    return visibleIds.length > 0 && visibleIds.every(id => this.candidates.includes(
                        id));
                },

                toggleSelectAll(checked) {
                    const visibleIds = Array.from(document.querySelectorAll('.row-checkbox')).map(cb =>
                        Number(cb.dataset.id));
                    if (checked) {
                        this.candidates = [...visibleIds];
                    } else {
                        this.candidates = [];
                    }
                },

                toggleCandidate(id) {
                    if (this.candidates.includes(id)) {
                        this.candidates = this.candidates.filter(i => i !== id);
                    } else {
                        this.candidates.push(id);
                    }
                },

                commit() {
                    if (this.current_action === '') {
                        Toast.show('Please select a bulk action', {
                            type: 'error'
                        });
                        return;
                    }
                    if (!this.hasCandidates && !this.current_action.includes('_all')) {
                        Toast.show('Please select at least one log', {
                            type: 'error'
                        });
                        return;
                    }
                    const modal = document.getElementById(this.current_action);
                    if (modal) modal.showModal();
                },
            });

            Alpine.store('product_inventory_search_setting', {
                showDisplayOption: false,
                showFilterOption: false,
                query: "",
                perPage: "20",
                orderBy: "desc",
                sortBy: "last_updated",

                is_last_updated_filter: @json(request('sortBy') == 'last_updated'),
                is_last_created_filter: @json(request('sortBy') == 'last_created'),

                showIDColumn: false,
                showUpdatedTimeColumn: false,
                showCreatedTimeColumn: false,

                init() {
                    const savedSetting = JSON.parse(localStorage.getItem(
                        'product_inventory_search_setting') ?? "{}");

                    this.showDisplayOption = savedSetting.showDisplayOption ?? false;
                    this.showFilterOption = savedSetting.showFilterOption ?? false;
                    this.query = savedSetting.query ?? "";
                    this.perPage = savedSetting.perPage ?? "20";
                    this.orderBy = savedSetting.orderBy ?? "desc";
                    this.sortBy = savedSetting.sortBy ?? "last_updated";

                    this.showIDColumn = savedSetting.showIDColumn ?? false;
                    this.showUpdatedTimeColumn = savedSetting.showUpdatedTimeColumn ?? false;
                    this.showCreatedTimeColumn = savedSetting.showCreatedTimeColumn ?? false;
                },

                save() {
                    const data = {
                        showDisplayOption: this.showDisplayOption,
                        showFilterOption: this.showFilterOption,
                        query: this.query,
                        perPage: this.perPage,
                        orderBy: this.orderBy,
                        sortBy: this.sortBy,
                        showIDColumn: this.showIDColumn,
                        showUpdatedTimeColumn: this.showUpdatedTimeColumn,
                        showCreatedTimeColumn: this.showCreatedTimeColumn,
                    };
                    localStorage.setItem('product_inventory_search_setting', JSON.stringify(data));
                },

                resetFilter() {
                    // this.categoryFilter = "";
                    // this.brandFilter = "";
                    // this.saleFilter = false;
                    // this.promotionFilter = false;
                    // this.pinnedFilter = false;
                    this.save();
                }
            });

            Alpine.store('product_inventory_search_setting').init();
        });
    </script>
@endpush
