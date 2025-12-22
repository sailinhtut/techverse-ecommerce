@php
    $site_currency = getParsedTemplate('site_currency');
@endphp

@extends('layouts.admin.admin_dashboard')
@section('admin_dashboard_content')
    <div class="p-3 lg:p-5 min-h-screen">
        <p class="lg:text-lg font-semibold">Payments</p>


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
                        <span class="italic text-error">Selected Payments</span>?
                    </p>

                    <template x-if="$store.bulk.hasCandidates">
                        <ul class="text-sm pl-10 list-decimal max-h-24 overflow-y-auto bg-base-200 p-3">
                            <template x-for="id in $store.bulk.candidates" :key="id">
                                <li x-text="'Payment ID: ' + id"></li>
                            </template>
                        </ul>
                    </template>

                    <div class="mt-3 modal-action">
                        <form method="dialog"><button class="btn" :disabled="loading">Cancel</button></form>

                        <form method="POST" action="{{ route('admin.dashboard.order.payment.bulk.delete-selected') }}"
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
                        <span class="text-error">All Payments</span>?
                    </p>

                    <div class="modal-action">
                        <form method="dialog"><button class="btn" :disabled="loading">Cancel</button></form>

                        <form method="POST" action="{{ route('admin.dashboard.order.payment.bulk.delete-all') }}"
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
                    <template x-data x-if="$store.payment_search_setting.sortBy">
                        <input type="hidden" name="sortBy" :value="$store.payment_search_setting.sortBy">
                    </template>

                    <template x-data x-if="$store.payment_search_setting.perPage">
                        <input type="hidden" x-data name="perPage" :value="$store.payment_search_setting.perPage">
                    </template>

                    <template x-data x-if="$store.payment_search_setting.orderBy">
                        <input type="hidden" x-data name="orderBy" :value="$store.payment_search_setting.orderBy">
                    </template>

                    <input type="text" x-data x-cloak class="join-item input input-sm rounded-l-box" name="query"
                        :value="$store.payment_search_setting.query"
                        @change="$store.payment_search_setting.query = $event.target.value; $store.payment_search_setting.save(); $el.form.submit()">

                    <button class="join-item btn btn-sm">Search</button>
                </form>

                {{-- page limiting --}}
                <form method="GET" action="{{ request()->url() }}">
                    <template x-data
                        x-if="$store.payment_search_setting.query && $store.payment_search_setting.query.length > 0">
                        <input type="hidden" name="query" :value="$store.payment_search_setting.query">
                    </template>

                    <template x-data x-if="$store.payment_search_setting.sortBy">
                        <input type="hidden" x-data name="sortBy" :value="$store.payment_search_setting.sortBy">
                    </template>

                    <template x-data x-if="$store.payment_search_setting.orderBy">
                        <input type="hidden" x-data name="orderBy" :value="$store.payment_search_setting.orderBy">
                    </template>

                    <select name="perPage" x-data x-cloak class="select select-sm w-fit shrink-0" x-data
                        x-model="$store.payment_search_setting.perPage"
                        @change="$store.payment_search_setting.perPage = $event.target.value; $store.payment_search_setting.save(); $el.form.submit()">
                        <option value="5">Show 5</option>
                        <option value="10">Show 10</option>
                        <option value="20">Show 20</option>
                        <option value="50">Show 50</option>
                    </select>
                </form>

                {{-- ascending & descending --}}
                <form method="GET" action="{{ request()->url() }}">
                    <template x-data
                        x-if="$store.payment_search_setting.query && $store.payment_search_setting.query.length > 0">
                        <input type="hidden" name="query" :value="$store.payment_search_setting.query">
                    </template>

                    <template x-data x-if="$store.payment_search_setting.perPage">
                        <input type="hidden" x-data name="perPage" :value="$store.payment_search_setting.perPage">
                    </template>

                    <template x-data x-if="$store.payment_search_setting.sortBy">
                        <input type="hidden" x-data name="sortBy" :value="$store.payment_search_setting.sortBy">
                    </template>

                    <select name="orderBy" x-data x-cloak x-model="$store.payment_search_setting.orderBy"
                        class="select select-sm w-fit shrink-0" x-data :value="$store.payment_search_setting.orderBy"
                        @change="$store.payment_search_setting.orderBy = $event.target.value; $store.payment_search_setting.save() ; $el.form.submit()">
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
                        x-if="$store.payment_search_setting.query && $store.payment_search_setting.query.length > 0">
                        <input type="hidden" name="query" :value="$store.payment_search_setting.query">
                    </template>

                    <template x-data x-if="$store.payment_search_setting.perPage">
                        <input type="hidden" x-data name="perPage" :value="$store.payment_search_setting.perPage">
                    </template>

                    <template x-data x-if="$store.payment_search_setting.orderBy">
                        <input type="hidden" x-data name="orderBy" :value="$store.payment_search_setting.orderBy">
                    </template>

                    <select name="sortBy" class="select select-sm w-fit shrink-0" x-data x-cloak
                        :value="$store.payment_search_setting.sortBy"
                        @change="$store.payment_search_setting.sortBy = $event.target.value; $store.payment_search_setting.save() ; $el.form.submit()">
                        <option value="last_updated">Sort By Last Updated
                        </option>
                        <option value="last_created">Sort By Last Created
                        </option>
                    </select>
                </form>

                {{-- <button class="btn btn-sm bg-base-100 font-normal shadow-none border-slate-300" x-data x-transition x-cloak
                    @click="$store.payment_search_setting.showFilterOption = !$store.payment_search_setting.showFilterOption;$store.payment_search_setting.save()">
                    <span x-text="$store.payment_search_setting.showFilterOption ? 'Hide Filter' : 'Filter Option'"></span>
                </button> --}}
                <button class="btn btn-sm bg-base-100 font-normal shadow-none border-slate-300" x-data x-transition x-cloak
                    @click="$store.payment_search_setting.showDisplayOption = !$store.payment_search_setting.showDisplayOption;$store.payment_search_setting.save()">
                    <span
                        x-text="$store.payment_search_setting.showDisplayOption ? 'Hide Display' : 'Display Option'"></span>
                </button>
            </div>
        </div>

        {{-- filtering --}}
        <div class="mt-3 p-3 border border-base-300 rounded-md flex flex-col gap-2" x-cloak x-transition x-data
            x-show="$store.payment_search_setting.showFilterOption">
            <p class="text-xs">Filter Options</p>
            <div class="grid grid-cols-2 md:grid-cols-4 xl:grid-cols-6 gap-3">
                {{-- <div class="flex flex-col gap-1">
                    <select x-data class="select select-sm text-xs" name="brand"
                        :value="$store.payment_search_setting.pinnedFilter ? 'true' : 'false'"
                        @change="$store.payment_search_setting.pinnedFilter = $event.target.value; $store.payment_search_setting.save()">
                        <option value="false">Filter Pinned</option>
                        <option value="true">Pinned Product</option>
                    </select>
                </div> --}}
            </div>
            <div class="flex flex-row gap-2">
                {{-- <button class="btn btn-primary btn-sm">Save</button> --}}
                <button class="btn btn-sm" x-data @click="$store.payment_search_setting.resetFilter()">Reset</button>
            </div>
        </div>

        {{-- column displaying --}}
        <div class="mt-3 p-3 border border-base-300 rounded-md flex flex-col gap-2" x-cloak x-transition x-data
            x-show="$store.payment_search_setting.showDisplayOption">
            <p class="text-xs">Display Options</p>
            <div class="flex sm:flex-row flex-col flex-wrap gap-3">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" class="checkbox checkbox-xs rounded-sm" x-data
                        :checked="$store.payment_search_setting.showIDColumn"
                        @change="$store.payment_search_setting.showIDColumn = !$store.payment_search_setting.showIDColumn; $store.payment_search_setting.save()">
                    <span class="text-xs">Show ID</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" class="checkbox checkbox-xs rounded-sm" x-data
                        :checked="$store.payment_search_setting.showUpdatedTimeColumn || $store
                            .payment_search_setting
                            .is_last_updated_filter"
                        @change="$store.payment_search_setting.showUpdatedTimeColumn = !$store.payment_search_setting.showUpdatedTimeColumn; $store.payment_search_setting.save()">
                    <span class="text-xs">Show Updated Time</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" class="checkbox checkbox-xs rounded-sm" x-data
                        :checked="$store.payment_search_setting.showCreatedTimeColumn || $store
                            .payment_search_setting
                            .is_last_created_filter"
                        @change="$store.payment_search_setting.showCreatedTimeColumn = !$store.payment_search_setting.showCreatedTimeColumn; $store.payment_search_setting.save()">
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
                            <th x-cloak x-data x-show="$store.payment_search_setting.showIDColumn" class="w-[50px]">ID
                            </th>
                            <th>Payment ID</th>
                            <th>Order Number</th>
                            <th>Invoice Number</th>
                            <th>Amount</th>
                            <th x-cloak x-data
                                x-show="$store.payment_search_setting.showUpdatedTimeColumn || $store.payment_search_setting.is_last_updated_filter">
                                Updated At</th>
                            <th x-cloak x-data
                                x-show="$store.payment_search_setting.showCreatedTimeColumn || $store.payment_search_setting.is_last_created_filter">
                                Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($payments as $payment)
                            <tr>
                                <td>
                                    <input type="checkbox" class="checkbox checkbox-xs rounded-sm row-checkbox"
                                        data-id="{{ $payment['id'] }}" x-data
                                        :checked="$store.bulk.candidates.includes({{ $payment['id'] }})"
                                        @change="$store.bulk.toggleCandidate({{ $payment['id'] }})">
                                </td>
                                <td>{{ $loop->iteration + ($payments->currentPage() - 1) * $payments->perPage() }}.</td>
                                <td>
                                    <a href="{{ route('admin.dashboard.order.payment.id.get', ['id' => $payment['id']]) }}"
                                        class="cursor-pointer hover:underline">
                                        Payment #{{ $payment['id'] }}
                                    </a>
                                </td>
                                <td>
                                    @if ($payment['order_id'])
                                        <a href="{{ route('admin.dashboard.order.id.get', ['id' => $payment['order_id']]) }}"
                                            class="cursor-pointer hover:underline">
                                            {{ $payment['order']['order_number'] }}
                                        </a>
                                    @else
                                        No Order Found
                                    @endif
                                </td>
                                <td>
                                    @if ($payment['invoice_id'])
                                        <a href="{{ route('admin.dashboard.order.invoice.id.get', ['id' => $payment['invoice_id']]) }}"
                                            class="cursor-pointer hover:underline">
                                            {{ $payment['invoice']['invoice_number'] }}
                                        </a>
                                    @else
                                        No Invoice Found
                                    @endif
                                </td>
                                <td>{{ number_format($payment['amount'], 2) }} {{ $site_currency }}</td>
                                <td x-cloak x-data
                                    x-show="$store.payment_search_setting.showUpdatedTimeColumn || $store.payment_search_setting.is_last_updated_filter">
                                    {{ $payment['updated_at'] ? \Carbon\Carbon::parse($payment['updated_at'])->format('Y-m-d h:i A') : '-' }}
                                </td>
                                <td x-cloak x-data
                                    x-show="$store.payment_search_setting.showCreatedTimeColumn || $store.payment_search_setting.is_last_created_filter">
                                    {{ $payment['created_at'] ? \Carbon\Carbon::parse($payment['created_at'])->format('Y-m-d h:i A') : '-' }}
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
                                                    onclick="document.getElementById('detail_modal_{{ $payment['id'] }}').showModal()">
                                                    View Details
                                                </button>
                                            </li>
                                            <li>
                                                <button type="button" class="text-error"
                                                    onclick="document.getElementById('delete_modal_{{ $payment['id'] }}').showModal()">
                                                    Delete
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>

                            {{-- Payment Modal --}}
                            <dialog id="detail_modal_{{ $payment['id'] }}" class="modal">
                                <div class="modal-box max-h-[85vh] max-w-2xl">
                                    <form method="dialog">
                                        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                    </form>

                                    <h3 class="text-lg font-semibold text-center mb-3">
                                        Payment #{{ $payment['id'] }}
                                    </h3>

                                    <div class="text-sm space-y-2">
                                        <p><strong>Payment ID:</strong> {{ $payment['id'] }}</p>
                                        <p><strong>Invoice:</strong>{{ $payment['invoice']['invoice_number'] ?? '-' }}</p>
                                        <p><strong>Amount:</strong>{{ number_format($payment['amount'], 2) }}
                                            {{ $site_currency }}</p>
                                        <p><strong>Created At:</strong> {{ $payment['created_at'] }}</p>
                                        <p><strong>Updated At:</strong> {{ $payment['updated_at'] }}</p>
                                    </div>

                                    <div class="modal-action mt-6">
                                        <form method="dialog">
                                            <button class="btn">Close</button>
                                        </form>
                                    </div>
                                </div>
                            </dialog>

                            <dialog id="delete_modal_{{ $payment['id'] }}" class="modal">
                                <div class="modal-box">
                                    <form method="dialog">
                                        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                    </form>
                                    <p class="text-lg font-semibold py-0">Confirm Delete</p>

                                    <p class="py-2 mb-0 text-sm">
                                        Are you sure you want to delete
                                        <span class="italic text-error">Payment
                                            #{{ $payment['id'] }}</span>
                                        ?
                                    </p>
                                    <div class="modal-action mt-0">
                                        <form method="dialog">
                                            <button class="btn">Cancel</button>
                                        </form>
                                        <form method="POST"
                                            action="{{ route('admin.dashboard.order.payment.id.delete', ['id' => $payment['id']]) }}">
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
                        <span class="font-semibold">{{ $payments->firstItem() }}</span>
                        –
                        <span class="font-semibold">{{ $payments->lastItem() }}</span>
                        of
                        <span class="font-semibold">{{ $payments->total() }}</span>
                        results
                    </div>

                    <div class="join">
                        @if ($payments->onFirstPage())
                            <button class="join-item btn btn-sm btn-disabled">«</button>
                        @else
                            <a href="{{ $payments->previousPageUrl() }}" class="join-item btn btn-sm">«</a>
                        @endif

                        <a href="{{ $payments->url(1) }}"
                            class="join-item btn btn-sm {{ $payments->currentPage() === 1 ? 'btn-active' : '' }}">
                            1
                        </a>

                        @php
                            $start = max(2, $payments->currentPage() - 1);
                            $end = min($payments->lastPage() - 1, $payments->currentPage() + 1);
                        @endphp

                        @for ($i = $start; $i <= $end; $i++)
                            <a href="{{ $payments->url($i) }}"
                                class="join-item btn btn-sm {{ $payments->currentPage() === $i ? 'btn-active' : '' }}">
                                {{ $i }}
                            </a>
                        @endfor

                        @if ($payments->lastPage() > 1)
                            <a href="{{ $payments->url($payments->lastPage()) }}"
                                class="join-item btn btn-sm {{ $payments->currentPage() === $payments->lastPage() ? 'btn-active' : '' }}">
                                {{ $payments->lastPage() }}
                            </a>
                        @endif

                        @if ($payments->hasMorePages())
                            <a href="{{ $payments->nextPageUrl() }}" class="join-item btn btn-sm">»</a>
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
                        Toast.show('Please select at least one method', {
                            type: 'error'
                        });
                        return;
                    }
                    const modal = document.getElementById(this.current_action);
                    if (modal) modal.showModal();
                },
            });

            Alpine.store('payment_search_setting', {
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
                    const savedSetting = JSON.parse(localStorage.getItem('payment_search_setting') ??
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
                    localStorage.setItem('payment_search_setting', JSON.stringify(data));
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

            Alpine.store('payment_search_setting').init();
        });
    </script>
@endpush
