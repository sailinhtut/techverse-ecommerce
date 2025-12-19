@extends('layouts.admin.admin_dashboard')
@section('admin_dashboard_content')
    <div class="p-5 min-h-screen">
        <p class="lg:text-lg font-semibold">Invoices</p>

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
                {{-- <button class="btn btn-sm shadow-none" onclick="create_tax_class_modal.showModal()">Add Tax
                    Class</button> --}}
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
                        <span class="italic text-error">Selected Invoices</span>?
                    </p>

                    <template x-if="$store.bulk.hasCandidates">
                        <ul class="text-sm pl-10 list-decimal max-h-24 overflow-y-auto bg-base-200 p-3">
                            <template x-for="id in $store.bulk.candidates" :key="id">
                                <li x-text="'Invoice ID: ' + id"></li>
                            </template>
                        </ul>
                    </template>

                    <div class="mt-3 modal-action">
                        <form method="dialog"><button class="btn" :disabled="loading">Cancel</button></form>

                        <form method="POST" action="{{ route('admin.dashboard.payment.invoice.bulk.delete-selected') }}"
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
                        <span class="text-error">All Invoices</span>?
                    </p>

                    <div class="modal-action">
                        <form method="dialog"><button class="btn" :disabled="loading">Cancel</button></form>

                        <form method="POST" action="{{ route('admin.dashboard.payment.invoice.bulk.delete-all') }}"
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
                    <template x-data x-if="$store.invoice_search_setting.sortBy">
                        <input type="hidden" name="sortBy" :value="$store.invoice_search_setting.sortBy">
                    </template>

                    <template x-data x-if="$store.invoice_search_setting.perPage">
                        <input type="hidden" x-data name="perPage" :value="$store.invoice_search_setting.perPage">
                    </template>

                    <template x-data x-if="$store.invoice_search_setting.orderBy">
                        <input type="hidden" x-data name="orderBy" :value="$store.invoice_search_setting.orderBy">
                    </template>

                    <input type="text" x-data x-cloak class="join-item input input-sm rounded-l-box" name="query"
                        :value="$store.invoice_search_setting.query"
                        @change="$store.invoice_search_setting.query = $event.target.value; $store.invoice_search_setting.save(); $el.form.submit()">

                    <button class="join-item btn btn-sm">Search</button>
                </form>

                {{-- page limiting --}}
                <form method="GET" action="{{ request()->url() }}">
                    <template x-data
                        x-if="$store.invoice_search_setting.query && $store.invoice_search_setting.query.length > 0">
                        <input type="hidden" name="query" :value="$store.invoice_search_setting.query">
                    </template>

                    <template x-data x-if="$store.invoice_search_setting.sortBy">
                        <input type="hidden" x-data name="sortBy" :value="$store.invoice_search_setting.sortBy">
                    </template>

                    <template x-data x-if="$store.invoice_search_setting.orderBy">
                        <input type="hidden" x-data name="orderBy" :value="$store.invoice_search_setting.orderBy">
                    </template>

                    <select name="perPage" x-data x-cloak class="select select-sm w-fit shrink-0" x-data
                        x-model="$store.invoice_search_setting.perPage"
                        @change="$store.invoice_search_setting.perPage = $event.target.value; $store.invoice_search_setting.save(); $el.form.submit()">
                        <option value="5">Show 5</option>
                        <option value="10">Show 10</option>
                        <option value="20">Show 20</option>
                        <option value="50">Show 50</option>
                    </select>
                </form>

                {{-- ascending & descending --}}
                <form method="GET" action="{{ request()->url() }}">
                    <template x-data
                        x-if="$store.invoice_search_setting.query && $store.invoice_search_setting.query.length > 0">
                        <input type="hidden" name="query" :value="$store.invoice_search_setting.query">
                    </template>

                    <template x-data x-if="$store.invoice_search_setting.perPage">
                        <input type="hidden" x-data name="perPage" :value="$store.invoice_search_setting.perPage">
                    </template>

                    <template x-data x-if="$store.invoice_search_setting.sortBy">
                        <input type="hidden" x-data name="sortBy" :value="$store.invoice_search_setting.sortBy">
                    </template>

                    <select name="orderBy" x-data x-cloak x-model="$store.invoice_search_setting.orderBy"
                        class="select select-sm w-fit shrink-0" x-data :value="$store.invoice_search_setting.orderBy"
                        @change="$store.invoice_search_setting.orderBy = $event.target.value; $store.invoice_search_setting.save() ; $el.form.submit()">
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
                        x-if="$store.invoice_search_setting.query && $store.invoice_search_setting.query.length > 0">
                        <input type="hidden" name="query" :value="$store.invoice_search_setting.query">
                    </template>

                    <template x-data x-if="$store.invoice_search_setting.perPage">
                        <input type="hidden" x-data name="perPage" :value="$store.invoice_search_setting.perPage">
                    </template>

                    <template x-data x-if="$store.invoice_search_setting.orderBy">
                        <input type="hidden" x-data name="orderBy" :value="$store.invoice_search_setting.orderBy">
                    </template>

                    <select name="sortBy" class="select select-sm w-fit shrink-0" x-data x-cloak
                        :value="$store.invoice_search_setting.sortBy"
                        @change="$store.invoice_search_setting.sortBy = $event.target.value; $store.invoice_search_setting.save() ; $el.form.submit()">
                        <option value="last_updated">Sort By Last Updated
                        </option>
                        <option value="last_created">Sort By Last Created
                        </option>
                    </select>
                </form>

                {{-- <button class="btn btn-sm bg-base-100 font-normal shadow-none border-slate-300" x-data x-transition x-cloak
                    @click="$store.invoice_search_setting.showFilterOption = !$store.invoice_search_setting.showFilterOption;$store.invoice_search_setting.save()">
                    <span x-text="$store.invoice_search_setting.showFilterOption ? 'Hide Filter' : 'Filter Option'"></span>
                </button> --}}
                <button class="btn btn-sm bg-base-100 font-normal shadow-none border-slate-300" x-data x-transition x-cloak
                    @click="$store.invoice_search_setting.showDisplayOption = !$store.invoice_search_setting.showDisplayOption;$store.invoice_search_setting.save()">
                    <span
                        x-text="$store.invoice_search_setting.showDisplayOption ? 'Hide Display' : 'Display Option'"></span>
                </button>
            </div>
        </div>

        {{-- filtering --}}
        <div class="mt-3 p-3 border border-base-300 rounded-md flex flex-col gap-2" x-cloak x-transition x-data
            x-show="$store.invoice_search_setting.showFilterOption">
            <p class="text-xs">Filter Options</p>
            <div class="grid grid-cols-2 md:grid-cols-4 xl:grid-cols-6 gap-3">
                {{-- <div class="flex flex-col gap-1">
                    <select x-data class="select select-sm text-xs" name="brand"
                        :value="$store.invoice_search_setting.pinnedFilter ? 'true' : 'false'"
                        @change="$store.invoice_search_setting.pinnedFilter = $event.target.value; $store.invoice_search_setting.save()">
                        <option value="false">Filter Pinned</option>
                        <option value="true">Pinned Product</option>
                    </select>
                </div> --}}
            </div>
            <div class="flex flex-row gap-2">
                {{-- <button class="btn btn-primary btn-sm">Save</button> --}}
                <button class="btn btn-sm" x-data @click="$store.invoice_search_setting.resetFilter()">Reset</button>
            </div>
        </div>

        {{-- column displaying --}}
        <div class="mt-3 p-3 border border-base-300 rounded-md flex flex-col gap-2" x-cloak x-transition x-data
            x-show="$store.invoice_search_setting.showDisplayOption">
            <p class="text-xs">Display Options</p>
            <div class="flex sm:flex-row flex-col flex-wrap gap-3">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" class="checkbox checkbox-xs rounded-sm" x-data
                        :checked="$store.invoice_search_setting.showIDColumn"
                        @change="$store.invoice_search_setting.showIDColumn = !$store.invoice_search_setting.showIDColumn; $store.invoice_search_setting.save()">
                    <span class="text-xs">Show ID</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" class="checkbox checkbox-xs rounded-sm" x-data
                        :checked="$store.invoice_search_setting.showUpdatedTimeColumn || $store
                            .invoice_search_setting
                            .is_last_updated_filter"
                        @change="$store.invoice_search_setting.showUpdatedTimeColumn = !$store.invoice_search_setting.showUpdatedTimeColumn; $store.invoice_search_setting.save()">
                    <span class="text-xs">Show Updated Time</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" class="checkbox checkbox-xs rounded-sm" x-data
                        :checked="$store.invoice_search_setting.showCreatedTimeColumn || $store
                            .invoice_search_setting
                            .is_last_created_filter"
                        @change="$store.invoice_search_setting.showCreatedTimeColumn = !$store.invoice_search_setting.showCreatedTimeColumn; $store.invoice_search_setting.save()">
                    <span class="text-xs">Show Created Time</span>
                </label>
            </div>
        </div>

        <div class="mt-3 card shadow-sm border border-base-300">
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
                            <th x-cloak x-data x-show="$store.invoice_search_setting.showIDColumn" class="w-[50px]">ID
                            </th>
                            <th class="w-[150px]">Invoice Number</th>
                            <th class="w-[150px]">Order Number</th>
                            <th class="w-[150px]">Status</th>
                            <th class="w-[150px]">Total</th>
                            <th x-cloak x-data
                                x-show="$store.invoice_search_setting.showUpdatedTimeColumn || $store.invoice_search_setting.is_last_updated_filter">
                                Updated At</th>
                            <th x-cloak x-data
                                x-show="$store.invoice_search_setting.showCreatedTimeColumn || $store.invoice_search_setting.is_last_created_filter">
                                Created At</th>
                            <th class="w-[150px]">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invoices as $invoice)
                            <tr>
                                <td>
                                    <input type="checkbox" class="checkbox checkbox-xs rounded-sm row-checkbox"
                                        data-id="{{ $invoice['id'] }}" x-data
                                        :checked="$store.bulk.candidates.includes({{ $invoice['id'] }})"
                                        @change="$store.bulk.toggleCandidate({{ $invoice['id'] }})">
                                </td>
                                <td>{{ $loop->iteration + ($invoices->currentPage() - 1) * $invoices->perPage() }}.</td>
                                <td x-cloak x-data x-show="$store.invoice_search_setting.showIDColumn">
                                    {{ $invoice['id'] }}
                                </td>
                                <td>
                                    <p onclick="document.getElementById('detail_modal_{{ $invoice['id'] }}').showModal()"
                                        class="cursor-pointer hover:underline">
                                        {{ $invoice['invoice_number'] }}</p>
                                </td>
                                <td>
                                    <a href="{{ route('admin.dashboard.order.id.get', ['id' => $invoice['order']['id']]) }}"
                                        class="cursor-pointer hover:underline">
                                        {{ $invoice['order']['order_number'] ?? '-' }}
                                    </a>
                                </td>
                                <td>
                                    @php
                                        $color = match ($invoice['status']) {
                                            'unpaid' => 'badge-error',
                                            'paid' => 'badge-success',
                                            'refunded' => 'badge-warning',
                                            default => 'badge-ghost',
                                        };
                                    @endphp
                                    <div class="badge {{ $color }} badge-outline capitalize">
                                        {{ $invoice['status'] }}
                                    </div>
                                </td>
                                <td>${{ number_format($invoice['grand_total'], 2) }}</td>
                                <td x-cloak x-data
                                    x-show="$store.invoice_search_setting.showUpdatedTimeColumn || $store.invoice_search_setting.is_last_updated_filter">
                                    {{ $invoice['updated_at'] ? \Carbon\Carbon::parse($invoice['updated_at'])->format('Y-m-d h:i A') : '-' }}
                                </td>
                                <td x-cloak x-data
                                    x-show="$store.invoice_search_setting.showCreatedTimeColumn || $store.invoice_search_setting.is_last_created_filter">
                                    {{ $invoice['created_at'] ? \Carbon\Carbon::parse($invoice['created_at'])->format('Y-m-d h:i A') : '-' }}
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
                                                    onclick="document.getElementById('detail_modal_{{ $invoice['id'] }}').showModal()">
                                                    View Details
                                                </button>
                                            </li>
                                            <li>
                                                <button type="button" class="text-error"
                                                    onclick="document.getElementById('delete_modal_{{ $invoice['id'] }}').showModal()">
                                                    Delete
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>

                            {{-- Invoice Detail Modal --}}
                            <dialog id="detail_modal_{{ $invoice['id'] }}" class="modal">
                                <div class="modal-box max-h-[85vh] max-w-2xl">
                                    <form method="dialog">
                                        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                    </form>

                                    <h3 class="text-lg font-semibold text-center mb-3">
                                        Invoice #{{ $invoice['invoice_number'] }}
                                    </h3>

                                    <div class="text-sm space-y-2">
                                        <p><strong>ID:</strong> {{ $invoice['id'] }}</p>
                                        <p><strong>Order:</strong> {{ $invoice['order']['order_number'] ?? '-' }}</p>
                                        <p><strong>Status:</strong>
                                            <span
                                                class="badge {{ $color }} badge-outline">{{ ucfirst($invoice['status']) }}</span>
                                        </p>
                                        <p><strong>Subtotal:</strong> ${{ number_format($invoice['subtotal'], 2) }}</p>
                                        <p><strong>Discount:</strong> -${{ number_format($invoice['discount_total'], 2) }}
                                        </p>
                                        <p><strong>Tax:</strong> +${{ number_format($invoice['tax_total'], 2) }}</p>
                                        <p><strong>Shipping:</strong> +${{ number_format($invoice['shipping_total'], 2) }}
                                        </p>
                                        <p><strong>Grand Total:</strong> <span
                                                class="font-semibold">${{ number_format($invoice['grand_total'], 2) }}</span>
                                        </p>
                                        <p><strong>Issued At:</strong> {{ $invoice['issued_at'] }}</p>
                                        <p><strong>Due At:</strong> {{ $invoice['due_at'] ?? '-' }}</p>
                                    </div>

                                    <div class="modal-action mt-6">
                                        <form method="dialog">
                                            <button class="btn">Close</button>
                                        </form>
                                    </div>
                                </div>
                            </dialog>

                            <dialog id="delete_modal_{{ $invoice['id'] }}" class="modal">
                                <div class="modal-box">
                                    <form method="dialog">
                                        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                    </form>
                                    <p class="text-lg font-semibold py-0">Confirm Delete</p>

                                    <p class="py-2 mb-0 text-sm">
                                        Are you sure you want to delete
                                        <span class="italic text-error">Invoice
                                            #{{ $invoice['id'] }}</span>
                                        ?
                                    </p>
                                    <div class="modal-action mt-0">
                                        <form method="dialog">
                                            <button class="btn">Cancel</button>
                                        </form>
                                        <form method="POST"
                                            action="{{ route('admin.dashboard.payment.invoice.id.delete', ['id' => $invoice['id']]) }}">
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
                        <span class="font-semibold">{{ $invoices->firstItem() }}</span> –
                        <span class="font-semibold">{{ $invoices->lastItem() }}</span> of
                        <span class="font-semibold">{{ $invoices->total() }}</span> results
                    </div>
                    <div class="join">
                        @if ($invoices->onFirstPage())
                            <button class="join-item btn btn-sm btn-disabled">«</button>
                        @else
                            <a href="{{ $invoices->previousPageUrl() }}" class="join-item btn btn-sm">«</a>
                        @endif
                        @for ($i = 1; $i <= $invoices->lastPage(); $i++)
                            <a href="{{ $invoices->url($i) }}"
                                class="join-item btn btn-sm {{ $invoices->currentPage() === $i ? 'btn-active' : '' }}">{{ $i }}</a>
                        @endfor
                        @if ($invoices->hasMorePages())
                            <a href="{{ $invoices->nextPageUrl() }}" class="join-item btn btn-sm">»</a>
                        @else
                            <button class="join-item btn btn-sm btn-disabled">»</button>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection


@push('script')
    <script>
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
                        Toast.show('Please select at least one invoice', {
                            type: 'error'
                        });
                        return;
                    }
                    const modal = document.getElementById(this.current_action);
                    if (modal) modal.showModal();
                },
            });

            Alpine.store('invoice_search_setting', {
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
                    const savedSetting = JSON.parse(localStorage.getItem('invoice_search_setting') ??
                        "{}");

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
                    localStorage.setItem('invoice_search_setting', JSON.stringify(data));
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

            Alpine.store('invoice_search_setting').init();
        });
    </script>
@endpush
