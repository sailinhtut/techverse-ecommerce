@extends('layouts.admin.admin_dashboard')

@section('admin_dashboard_content')
    <div class="p-3 lg:p-5 min-h-screen">
        <p class="lg:text-lg font-semibold">Coupons</p>

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
                <button class="btn btn-sm shadow-none" onclick="create_coupon_modal.showModal()">Add Coupon</button>
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
                        <span class="italic text-error">Selected Coupons</span>?
                    </p>

                    <template x-if="$store.bulk.hasCandidates">
                        <ul class="text-sm pl-10 list-decimal max-h-24 overflow-y-auto bg-base-200 p-3">
                            <template x-for="id in $store.bulk.candidates" :key="id">
                                <li x-text="'Coupon ID: ' + id"></li>
                            </template>
                        </ul>
                    </template>

                    <div class="mt-3 modal-action">
                        <form method="dialog"><button class="btn" :disabled="loading">Cancel</button></form>

                        <form method="POST" action="{{ route('admin.dashboard.product.coupon.bulk.delete-selected') }}"
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
                        <span class="text-error">All Coupons</span>?
                    </p>

                    <div class="modal-action">
                        <form method="dialog"><button class="btn" :disabled="loading">Cancel</button></form>

                        <form method="POST" action="{{ route('admin.dashboard.product.coupon.bulk.delete-all') }}"
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
                    <template x-data x-if="$store.coupon_search_setting.sortBy">
                        <input type="hidden" name="sortBy" :value="$store.coupon_search_setting.sortBy">
                    </template>

                    <template x-data x-if="$store.coupon_search_setting.perPage">
                        <input type="hidden" x-data name="perPage" :value="$store.coupon_search_setting.perPage">
                    </template>

                    <template x-data x-if="$store.coupon_search_setting.orderBy">
                        <input type="hidden" x-data name="orderBy" :value="$store.coupon_search_setting.orderBy">
                    </template>

                    <input type="text" x-data x-cloak class="join-item input input-sm rounded-l-box" name="query"
                        :value="$store.coupon_search_setting.query"
                        @change="$store.coupon_search_setting.query = $event.target.value; $store.coupon_search_setting.save(); $el.form.submit()">

                    <button class="join-item btn btn-sm">Search</button>
                </form>

                {{-- page limiting --}}
                <form method="GET" action="{{ request()->url() }}">
                    <template x-data
                        x-if="$store.coupon_search_setting.query && $store.coupon_search_setting.query.length > 0">
                        <input type="hidden" name="query" :value="$store.coupon_search_setting.query">
                    </template>

                    <template x-data x-if="$store.coupon_search_setting.sortBy">
                        <input type="hidden" x-data name="sortBy" :value="$store.coupon_search_setting.sortBy">
                    </template>

                    <template x-data x-if="$store.coupon_search_setting.orderBy">
                        <input type="hidden" x-data name="orderBy" :value="$store.coupon_search_setting.orderBy">
                    </template>

                    <select name="perPage" x-data x-cloak class="select select-sm w-fit shrink-0" x-data
                        x-model="$store.coupon_search_setting.perPage"
                        @change="$store.coupon_search_setting.perPage = $event.target.value; $store.coupon_search_setting.save(); $el.form.submit()">
                        <option value="5">Show 5</option>
                        <option value="10">Show 10</option>
                        <option value="20">Show 20</option>
                        <option value="50">Show 50</option>
                    </select>
                </form>

                {{-- ascending & descending --}}
                <form method="GET" action="{{ request()->url() }}">
                    <template x-data
                        x-if="$store.coupon_search_setting.query && $store.coupon_search_setting.query.length > 0">
                        <input type="hidden" name="query" :value="$store.coupon_search_setting.query">
                    </template>

                    <template x-data x-if="$store.coupon_search_setting.perPage">
                        <input type="hidden" x-data name="perPage" :value="$store.coupon_search_setting.perPage">
                    </template>

                    <template x-data x-if="$store.coupon_search_setting.sortBy">
                        <input type="hidden" x-data name="sortBy" :value="$store.coupon_search_setting.sortBy">
                    </template>

                    <select name="orderBy" x-data x-cloak x-model="$store.coupon_search_setting.orderBy"
                        class="select select-sm w-fit shrink-0" x-data :value="$store.coupon_search_setting.orderBy"
                        @change="$store.coupon_search_setting.orderBy = $event.target.value; $store.coupon_search_setting.save() ; $el.form.submit()">
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
                        x-if="$store.coupon_search_setting.query && $store.coupon_search_setting.query.length > 0">
                        <input type="hidden" name="query" :value="$store.coupon_search_setting.query">
                    </template>

                    <template x-data x-if="$store.coupon_search_setting.perPage">
                        <input type="hidden" x-data name="perPage" :value="$store.coupon_search_setting.perPage">
                    </template>

                    <template x-data x-if="$store.coupon_search_setting.orderBy">
                        <input type="hidden" x-data name="orderBy" :value="$store.coupon_search_setting.orderBy">
                    </template>

                    <select name="sortBy" class="select select-sm w-fit shrink-0" x-data x-cloak
                        :value="$store.coupon_search_setting.sortBy"
                        @change="$store.coupon_search_setting.sortBy = $event.target.value; $store.coupon_search_setting.save() ; $el.form.submit()">
                        <option value="last_updated">Sort By Last Updated
                        </option>
                        <option value="last_created">Sort By Last Created
                        </option>
                    </select>
                </form>

                {{-- <button class="btn btn-sm bg-base-100 font-normal shadow-none border-slate-300" x-data x-transition x-cloak
                    @click="$store.coupon_search_setting.showFilterOption = !$store.coupon_search_setting.showFilterOption;$store.coupon_search_setting.save()">
                    <span x-text="$store.coupon_search_setting.showFilterOption ? 'Hide Filter' : 'Filter Option'"></span>
                </button> --}}
                <button class="btn btn-sm bg-base-100 font-normal shadow-none border-slate-300" x-data x-transition x-cloak
                    @click="$store.coupon_search_setting.showDisplayOption = !$store.coupon_search_setting.showDisplayOption;$store.coupon_search_setting.save()">
                    <span
                        x-text="$store.coupon_search_setting.showDisplayOption ? 'Hide Display' : 'Display Option'"></span>
                </button>
            </div>
        </div>

        <div class="mt-3 p-3 border border-base-300 rounded-md flex flex-col gap-2" x-cloak x-transition x-data
            x-show="$store.coupon_search_setting.showFilterOption">
            <p class="text-xs">Filter Options</p>
            <div class="grid grid-cols-2 md:grid-cols-4 xl:grid-cols-6 gap-3">
                {{-- <div class="flex flex-col gap-1">
                    <select x-data class="select select-sm text-xs" name="brand"
                        :value="$store.coupon_search_setting.pinnedFilter ? 'true' : 'false'"
                        @change="$store.coupon_search_setting.pinnedFilter = $event.target.value; $store.coupon_search_setting.save()">
                        <option value="false">Filter Pinned</option>
                        <option value="true">Pinned Product</option>
                    </select>
                </div> --}}
            </div>
            <div class="flex flex-row gap-2">
                {{-- <button class="btn btn-primary btn-sm">Save</button> --}}
                <button class="btn btn-sm" x-data @click="$store.coupon_search_setting.resetFilter()">Reset</button>
            </div>
        </div>

        {{-- column displaying --}}
        <div class="mt-3 p-3 border border-base-300 rounded-md flex flex-col gap-2" x-cloak x-transition x-data
            x-show="$store.coupon_search_setting.showDisplayOption">
            <p class="text-xs">Display Options</p>
            <div class="flex sm:flex-row flex-col flex-wrap gap-3">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" class="checkbox checkbox-xs rounded-sm" x-data
                        :checked="$store.coupon_search_setting.showIDColumn"
                        @change="$store.coupon_search_setting.showIDColumn = !$store.coupon_search_setting.showIDColumn; $store.coupon_search_setting.save()">
                    <span class="text-xs">Show ID</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" class="checkbox checkbox-xs rounded-sm" x-data
                        :checked="$store.coupon_search_setting.showUpdatedTimeColumn || $store.coupon_search_setting
                            .is_last_updated_filter"
                        @change="$store.coupon_search_setting.showUpdatedTimeColumn = !$store.coupon_search_setting.showUpdatedTimeColumn; $store.coupon_search_setting.save()">
                    <span class="text-xs">Show Updated Time</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" class="checkbox checkbox-xs rounded-sm" x-data
                        :checked="$store.coupon_search_setting.showCreatedTimeColumn || $store.coupon_search_setting
                            .is_last_created_filter"
                        @change="$store.coupon_search_setting.showCreatedTimeColumn = !$store.coupon_search_setting.showCreatedTimeColumn; $store.coupon_search_setting.save()">
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
                            <th x-cloak x-data x-show="$store.coupon_search_setting.showIDColumn" class="w-[50px]">ID
                            </th>
                            <th>Code</th>
                            <th>Type</th>
                            <th>Value</th>
                            <th>Apply To</th>
                            <th>Min Cart Value</th>
                            <th>Valid From</th>
                            <th>Valid To</th>
                            <th>Usage Limit</th>
                            <th>Used Limit</th>
                            <th x-cloak x-data
                                x-show="$store.coupon_search_setting.showUpdatedTimeColumn || $store.coupon_search_setting.is_last_updated_filter">
                                Updated At</th>
                            <th x-cloak x-data
                                x-show="$store.coupon_search_setting.showCreatedTimeColumn || $store.coupon_search_setting.is_last_created_filter">
                                Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($coupons as $coupon)
                            <tr>
                                <td>
                                    <input type="checkbox" class="checkbox checkbox-xs rounded-sm row-checkbox"
                                        data-id="{{ $coupon['id'] }}" x-data
                                        :checked="$store.bulk.candidates.includes({{ $coupon['id'] }})"
                                        @change="$store.bulk.toggleCandidate({{ $coupon['id'] }})">
                                </td>
                                <td>{{ $loop->iteration + ($coupons->currentPage() - 1) * $coupons->perPage() }}</td>
                                <td x-cloak x-data x-show="$store.coupon_search_setting.showIDColumn">
                                    {{ $coupon['id'] }}
                                </td>
                                <td>
                                    <p onclick="document.getElementById('detail_modal_{{ $coupon['id'] }}').showModal()"
                                        class="cursor-pointer hover:underline">
                                        {{ $coupon['code'] }}
                                    </p>
                                </td>
                                <td>{{ ucfirst($coupon['type']) }}</td>
                                <td>{{ number_format($coupon['value'], 0) }}{{ $coupon['type'] == 'percentage' ? '%' : '' }}
                                </td>
                                <td>{{ ucfirst($coupon['apply_to']) }}</td>
                                <td>{{ $coupon['min_cart_value'] ? number_format($coupon['min_cart_value'], 0) : '-' }}
                                </td>
                                <td>{{ $coupon['valid_from'] ? \Carbon\Carbon::parse($coupon['valid_from'])->format('Y-m-d h:i A') : '-' }}
                                </td>
                                <td>{{ $coupon['valid_to'] ? \Carbon\Carbon::parse($coupon['valid_to'])->format('Y-m-d h:i A') : '-' }}
                                </td>
                                <td>{{ $coupon['usage_limit'] ?? '-' }}</td>
                                <td>{{ $coupon['used'] ?? '-' }}</td>
                                <td x-cloak x-data
                                    x-show="$store.coupon_search_setting.showUpdatedTimeColumn || $store.coupon_search_setting.is_last_updated_filter">
                                    {{ $coupon['updated_at'] ? \Carbon\Carbon::parse($coupon['updated_at'])->format('Y-m-d h:i A') : '-' }}
                                </td>
                                <td x-cloak x-data
                                    x-show="$store.coupon_search_setting.showCreatedTimeColumn || $store.coupon_search_setting.is_last_created_filter">
                                    {{ $coupon['created_at'] ? \Carbon\Carbon::parse($coupon['created_at'])->format('Y-m-d h:i A') : '-' }}
                                </td>
                                <td>
                                    <div tabindex="0" role="button" class="dropdown dropdown-left">
                                        <div class="btn btn-square btn-sm btn-ghost">
                                            <i data-lucide="ellipsis-vertical" class="size-5"></i>
                                        </div>
                                        <ul tabindex="0"
                                            class="menu dropdown-content bg-base-100 border border-base-300 w-30 rounded-box p-1 shadow-sm">
                                            <li><button
                                                    onclick="document.getElementById('detail_modal_{{ $coupon['id'] }}').showModal()">View</button>
                                            </li>
                                            <li><button
                                                    onclick="document.getElementById('edit_modal_{{ $coupon['id'] }}').showModal()">Edit</button>
                                            </li>
                                            <li><button class="text-error"
                                                    onclick="document.getElementById('delete_modal_{{ $coupon['id'] }}').showModal()">Delete</button>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>

                            {{-- View Details Modal --}}
                            <dialog id="detail_modal_{{ $coupon['id'] }}" class="modal" x-data="couponDetail({{ json_encode($coupon['category_ids'] ?? []) }}, {{ json_encode($coupon['product_ids'] ?? []) }})"
                                x-init="fetchProducts()">
                                <div class="modal-box max-w-3xl max-h-[85vh] overflow-y-auto">
                                    <form method="dialog">
                                        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                    </form>

                                    <h3 class="text-lg font-semibold text-center mb-3">Coupon Details</h3>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <div>
                                            <label class="text-sm">Code</label>
                                            <input type="text" class="input w-full border-base-300"
                                                value="{{ $coupon['code'] }}" readonly>
                                        </div>

                                        <div>
                                            <label class="text-sm">Type</label>
                                            <input type="text" class="input w-full border-base-300"
                                                value="{{ ucfirst($coupon['type']) }}" readonly>
                                        </div>

                                        <div>
                                            <label class="text-sm">Value</label>
                                            <input type="text" class="input w-full border-base-300"
                                                value="{{ number_format($coupon['value'], 2) }}" readonly>
                                        </div>

                                        <div>
                                            <label class="text-sm">Apply To</label>
                                            <input type="text" class="input w-full border-base-300"
                                                value="{{ ucfirst($coupon['apply_to']) }}" readonly>
                                        </div>

                                        <div>
                                            <label class="text-sm">Min Cart Value</label>
                                            <input type="text" class="input w-full border-base-300"
                                                value="{{ $coupon['min_cart_value'] ?? '-' }}" readonly>
                                        </div>

                                        <div>
                                            <label class="text-sm">Usage Limit</label>
                                            <input type="text" class="input w-full border-base-300"
                                                value="{{ $coupon['usage_limit'] ?? '-' }}" readonly>
                                        </div>

                                        <div class="md:col-span-2">
                                            <label class="text-sm">Used</label>
                                            <input type="text" class="input w-full border-base-300"
                                                value="{{ $coupon['used'] ?? '0' }}" readonly>
                                        </div>

                                        <div>
                                            <label class="text-sm">Valid From</label>
                                            <input type="datetime-local" name="valid_from"
                                                value="{{ old('valid_from', isset($coupon['valid_from']) ? \Carbon\Carbon::parse($coupon['valid_from'])->format('Y-m-d\TH:i') : '') }}"
                                                class="input input-bordered w-full" readonly />
                                        </div>

                                        <div>
                                            <label class="text-sm">Valid To</label>
                                            <input type="datetime-local" name="valid_to"
                                                value="{{ old('valid_to', isset($coupon['valid_to']) ? \Carbon\Carbon::parse($coupon['valid_to'])->format('Y-m-d\TH:i') : '') }}"
                                                class="input input-bordered w-full" readonly />
                                        </div>

                                        <div class="pt-2">
                                            <p class="font-medium mb-1 text-sm">Selected Products</p>
                                            <template x-if="productLoading">
                                                <p class="text-gray-500 text-sm mt-3" x-cloak>Searching products...</p>
                                            </template>
                                            <template x-if="!productLoading && selectedProducts.length === 0" x-cloak>
                                                <p class="text-sm text-gray-500">No product</p>
                                            </template>
                                            <template x-for="(product, index) in selectedProducts" :key="product.id"
                                                x-cloak>
                                                <div
                                                    class="flex justify-between items-center mb-1 border border-base-300 rounded-box py-1 px-3">
                                                    <span class="text-xs"
                                                        x-text="`ID ${product.id} - ${product.name}`"></span>
                                                </div>
                                            </template>
                                        </div>

                                        <!-- CATEGORY SEARCH -->
                                        <div class="pt-2">
                                            <p class="font-medium mb-1 text-sm">Selected Categories</p>
                                            <template x-if="categoryLoading" x-cloak>
                                                <p class="text-gray-500 text-sm mt-3">Searching categories...</p>
                                            </template>
                                            <template x-if="!categoryLoading && selectedCategories.length === 0" x-cloak>
                                                <p class="text-sm text-gray-500">No category</p>
                                            </template>
                                            <template x-for="(category, index) in selectedCategories"
                                                :key="category.id" x-cloak>
                                                <div
                                                    class="flex justify-between items-center mb-1 border border-base-300 rounded-box py-1 px-3">
                                                    <span class="text-xs"
                                                        x-text="`ID ${category.id} - ${category.name}`"></span>

                                                </div>
                                            </template>
                                        </div>
                                    </div>

                                    <div class="modal-action mt-3">
                                        <form method="dialog">
                                            <button class="btn">Close</button>
                                        </form>
                                    </div>
                                </div>
                            </dialog>


                            {{-- Edit Modal --}}
                            <dialog id="edit_modal_{{ $coupon['id'] }}" class="modal" x-data="couponForm({{ json_encode($coupon['category_ids'] ?? []) }}, {{ json_encode($coupon['product_ids'] ?? []) }})">
                                <div class="modal-box max-w-3xl max-h-[85vh] overflow-y-auto">
                                    <form method="dialog">
                                        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                    </form>

                                    <h3 class="text-lg font-semibold text-center mb-3">Edit Coupon</h3>

                                    <form method="POST" x-ref="couponForm" @submit.prevent="beforeSubmit"
                                        action="{{ route('admin.dashboard.product.coupon.id.post', ['id' => $coupon['id']]) }}">
                                        @csrf
                                        @method('POST')

                                        <template x-for="(id, index) in selectedProducts.map(e=>e.id)"
                                            :key="id">
                                            <input type="hidden" name="product_ids[]" :value="id">
                                        </template>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                            <div>
                                                <label class="text-sm">Code</label>
                                                <input name="code" class="input w-full border-base-300"
                                                    value="{{ $coupon['code'] }}" required>
                                            </div>

                                            <div>
                                                <label class="text-sm">Type</label>
                                                <select name="type" class="select w-full border-base-300" required>
                                                    <option value="fixed" @selected($coupon['type'] == 'fixed')>Fixed</option>
                                                    <option value="percentage" @selected($coupon['type'] == 'percentage')>Percentage
                                                    </option>
                                                </select>
                                            </div>

                                            <div>
                                                <label class="text-sm">Value</label>
                                                <input name="value" type="number" step="0.01"
                                                    class="input w-full border-base-300" value="{{ $coupon['value'] }}"
                                                    required>
                                            </div>

                                            <div>
                                                <label class="text-sm">Apply To</label>
                                                <select name="apply_to" class="select w-full border-base-300" required>
                                                    <option value="cart" @selected($coupon['apply_to'] == 'cart')>Cart Subtotal
                                                    </option>
                                                    <option value="product" @selected($coupon['apply_to'] == 'product')>Selected Products
                                                    </option>
                                                    <option value="category" @selected($coupon['apply_to'] == 'category')>Selected
                                                        Categories</option>
                                                </select>
                                            </div>

                                            <div>
                                                <label class="text-sm">Min Cart Value</label>
                                                <input name="min_cart_value" type="number" step="0.01"
                                                    class="input w-full border-base-300"
                                                    value="{{ $coupon['min_cart_value'] }}">
                                            </div>

                                            <div>
                                                <label class="text-sm">Usage Limit</label>
                                                <input name="usage_limit" type="number"
                                                    class="input w-full border-base-300"
                                                    value="{{ $coupon['usage_limit'] }}">
                                            </div>

                                            <div class="md:col-span-2">
                                                <label class="text-sm">Used</label>
                                                <input name="used" type="number" class="input w-full border-base-300"
                                                    value="{{ $coupon['used'] ?? 0 }}">
                                            </div>


                                            <div>
                                                <label class="text-sm">Valid From</label>
                                                <input type="datetime-local" name="valid_from"
                                                    value="{{ old('valid_from', isset($coupon['valid_from']) ? \Carbon\Carbon::parse($coupon['valid_from'])->format('Y-m-d\TH:i') : '') }}"
                                                    class="input input-bordered w-full" />
                                            </div>

                                            <div>
                                                <label class="text-sm">Valid To</label>
                                                <input type="datetime-local" name="valid_to"
                                                    value="{{ old('valid_to', isset($coupon['valid_to']) ? \Carbon\Carbon::parse($coupon['valid_to'])->format('Y-m-d\TH:i') : '') }}"
                                                    class="input input-bordered w-full" />
                                            </div>

                                            <div class="mt-5">
                                                <label class="block text-sm font-semibold mb-1">Search Product</label>
                                                <input type="text" x-model.debounce.400ms="productQuery"
                                                    placeholder="Type product name..."
                                                    class="input input-bordered w-full" />

                                                <template x-if="productLoading">
                                                    <p class="text-gray-500 text-sm mt-3">Searching products...</p>
                                                </template>

                                                <template x-if="productResults.length > 0">
                                                    <ul
                                                        class="border border-base-300 rounded-box mt-3 divide-y divide-base-300 max-h-48 overflow-y-auto">
                                                        <template x-for="item in productResults" :key="item.id">
                                                            <li @click="addProduct(item)"
                                                                class="px-3 py-2 cursor-pointer hover:bg-base-200 flex justify-between text-sm">
                                                                <span x-text="`ID ${item.id} - ${item.name}`"></span>
                                                                <span class="text-gray-500">$<span
                                                                        x-text="item.regular_price"></span></span>
                                                            </li>
                                                        </template>
                                                    </ul>
                                                </template>

                                                <div class="pt-2">
                                                    <p class="font-medium mb-1 text-sm">Selected Products</p>
                                                    <template x-if="selectedProducts.length === 0">
                                                        <p class="text-sm text-gray-500">No product selected</p>
                                                    </template>
                                                    <template x-for="(product, index) in selectedProducts"
                                                        :key="product.id">
                                                        <div
                                                            class="flex justify-between items-center mb-1 border border-base-300 rounded-box py-1 px-3">
                                                            <span class="text-xs"
                                                                x-text="`ID ${product.id} - ${product.name}`"></span>
                                                            <button type="button" @click="removeProduct(index)"
                                                                class="btn btn-xs btn-ghost btn-square">
                                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                    viewBox="0 0 24 24" stroke-width="1.5"
                                                                    stroke="currentColor" class="size-4 stroke-error">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        d="M6 18L18 6M6 6l12 12" />
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>

                                            <!-- CATEGORY SEARCH -->
                                            <div class="mt-5">
                                                <label class="block text-sm font-semibold mb-1">Search Category</label>
                                                <input type="text" x-model.debounce.400ms="categoryQuery"
                                                    placeholder="Type category name..."
                                                    class="input input-bordered w-full" />

                                                <template x-if="categoryLoading">
                                                    <p class="text-gray-500 text-sm mt-3">Searching categories...</p>
                                                </template>

                                                <template x-if="categoryResults.length > 0">
                                                    <ul
                                                        class="border border-base-300 rounded-box mt-3 divide-y divide-base-300 max-h-48 overflow-y-auto">
                                                        <template x-for="item in categoryResults" :key="item.id">
                                                            <li @click="addCategory(item)"
                                                                class="px-3 py-2 cursor-pointer hover:bg-base-200 flex justify-between text-sm">
                                                                <span x-text="`ID ${item.id} - ${item.name}`"></span>
                                                            </li>
                                                        </template>
                                                    </ul>
                                                </template>

                                                <div class="pt-2">
                                                    <p class="font-medium mb-1 text-sm">Selected Categories</p>
                                                    <template x-if="selectedCategories.length === 0">
                                                        <p class="text-sm text-gray-500">No category selected</p>
                                                    </template>
                                                    <template x-for="(category, index) in selectedCategories"
                                                        :key="category.id">
                                                        <div
                                                            class="flex justify-between items-center mb-1 border border-base-300 rounded-box py-1 px-3">
                                                            <span class="text-xs"
                                                                x-text="`ID ${category.id} - ${category.name}`"></span>
                                                            <button type="button" @click="removeCategory(index)"
                                                                class="btn btn-xs btn-ghost btn-square">
                                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                    viewBox="0 0 24 24" stroke-width="1.5"
                                                                    stroke="currentColor" class="size-4 stroke-error">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        d="M6 18L18 6M6 6l12 12" />
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="modal-action mt-3">
                                            <button type="submit" class="btn btn-primary">Update Coupon</button>
                                        </div>
                                    </form>
                                </div>
                            </dialog>

                            {{-- Delete Modal --}}
                            <dialog id="delete_modal_{{ $coupon['id'] }}" class="modal">
                                <div class="modal-box">
                                    <form method="dialog">
                                        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                    </form>
                                    <p class="text-lg font-semibold">Confirm Delete</p>
                                    <p class="text-sm mb-4">Are you sure you want to delete <span
                                            class="text-error">{{ $coupon['code'] }}</span>?</p>
                                    <div class="modal-action">
                                        <form method="dialog"><button class="btn">Cancel</button></form>
                                        <form method="POST"
                                            action="{{ route('admin.dashboard.product.coupon.id.delete', ['id' => $coupon['id']]) }}">
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
                        <span class="font-semibold">{{ $coupons->firstItem() }}</span>
                        –
                        <span class="font-semibold">{{ $coupons->lastItem() }}</span>
                        of
                        <span class="font-semibold">{{ $coupons->total() }}</span>
                        results
                    </div>

                    <div class="join">
                        @if ($coupons->onFirstPage())
                            <button class="join-item btn btn-sm btn-disabled">«</button>
                        @else
                            <a href="{{ $coupons->previousPageUrl() }}" class="join-item btn btn-sm">«</a>
                        @endif

                        <a href="{{ $coupons->url(1) }}"
                            class="join-item btn btn-sm {{ $coupons->currentPage() === 1 ? 'btn-active' : '' }}">
                            1
                        </a>

                        @php
                            $start = max(2, $coupons->currentPage() - 1);
                            $end = min($coupons->lastPage() - 1, $coupons->currentPage() + 1);
                        @endphp

                        @for ($i = $start; $i <= $end; $i++)
                            <a href="{{ $coupons->url($i) }}"
                                class="join-item btn btn-sm {{ $coupons->currentPage() === $i ? 'btn-active' : '' }}">
                                {{ $i }}
                            </a>
                        @endfor
                            
                        @if ($coupons->lastPage() > 1)
                            <a href="{{ $coupons->url($coupons->lastPage()) }}"
                                class="join-item btn btn-sm {{ $coupons->currentPage() === $coupons->lastPage() ? 'btn-active' : '' }}">
                                {{ $coupons->lastPage() }}
                            </a>
                        @endif

                        @if ($coupons->hasMorePages())
                            <a href="{{ $coupons->nextPageUrl() }}" class="join-item btn btn-sm">»</a>
                        @else
                            <button class="join-item btn btn-sm btn-disabled">»</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Create Coupon Modal --}}
        <dialog id="create_coupon_modal" class="modal" x-data="couponForm()">
            <div class="modal-box max-w-3xl max-h-[85vh] overflow-y-auto">
                <form method="dialog">
                    <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                </form>

                <h3 class="text-lg font-semibold text-center mb-3">Create Coupon</h3>

                <form method="POST" x-ref="couponForm" @submit.prevent="beforeSubmit"
                    action="{{ route('admin.dashboard.product.coupon.post') }}">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="text-sm">Code</label>
                            <input name="code" class="input w-full border-base-300" placeholder="Coupon Code"
                                required>
                        </div>

                        <div>
                            <label class="text-sm">Type</label>
                            <select name="type" class="select w-full border-base-300" required>
                                <option value="fixed">Fixed</option>
                                <option value="percentage">Percentage</option>
                            </select>
                        </div>

                        <div>
                            <label class="text-sm">Value</label>
                            <input name="value" type="number" step="0.01" class="input w-full border-base-300"
                                placeholder="Discount Value" required>
                        </div>

                        <div>
                            <label class="text-sm">Apply To</label>
                            <select name="apply_to" class="select w-full border-base-300" required>
                                <option value="cart">Cart Subtotal</option>
                                <option value="product">Selected Products</option>
                                <option value="category">Selected Categories</option>
                            </select>
                        </div>

                        <div>
                            <label class="text-sm">Min Cart Value</label>
                            <input name="min_cart_value" type="number" step="0.01"
                                class="input w-full border-base-300" placeholder="Optional">
                        </div>

                        <div>
                            <label class="text-sm">Usage Limit</label>
                            <input name="usage_limit" type="number" class="input w-full border-base-300"
                                placeholder="Optional">
                        </div>

                        <div class="md:col-span-2">
                            <label class="text-sm">Used</label>
                            <input name="used" type="number" class="input w-full border-base-300"
                                placeholder="Optional">
                        </div>

                        <div>
                            <label class="text-sm">Valid From</label>
                            <input type="datetime-local" name="valid_from" class="input input-bordered w-full" />
                        </div>

                        <div>
                            <label class="text-sm">Valid To</label>
                            <input type="datetime-local" name="valid_to" class="input input-bordered w-full" />
                        </div>

                        <div class="mt-5">
                            <label class="block text-sm font-semibold mb-1">Search Product</label>
                            <input type="text" x-model.debounce.400ms="productQuery"
                                placeholder="Type product name..." class="input input-bordered w-full" />

                            <template x-if="productLoading">
                                <p class="mt-3 text-gray-500 text-sm ">Searching products...</p>
                            </template>

                            <template x-if="productResults.length > 0">
                                <ul
                                    class="mt-3 border border-base-300 rounded-box divide-y divide-base-300 max-h-48 overflow-y-auto">
                                    <template x-for="item in productResults" :key="item.id">
                                        <li @click="addProduct(item)"
                                            class="px-3 py-2 cursor-pointer hover:bg-base-200 flex justify-between text-sm">
                                            <span x-text="item.name"></span>
                                            <span class="text-gray-500">$<span x-text="item.regular_price"></span></span>
                                        </li>
                                    </template>
                                </ul>
                            </template>

                            <div class="pt-2">
                                <p class="font-medium mb-1 text-sm">Selected Products</p>
                                <template x-if="selectedProducts.length === 0">
                                    <p class="text-sm text-gray-500">No product selected</p>
                                </template>
                                <template x-for="(product, index) in selectedProducts" :key="product.id">
                                    <div
                                        class="flex justify-between items-center mb-1 border border-base-300 rounded-box py-1 px-3">
                                        <span class="text-xs" x-text="product.name"></span>
                                        <button type="button" @click="removeProduct(index)"
                                            class="btn btn-xs btn-ghost btn-square">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-4 stroke-error">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- CATEGORY SEARCH -->
                        <div class="mt-5">
                            <label class="block text-sm font-semibold mb-1">Search Category</label>
                            <input type="text" x-model.debounce.400ms="categoryQuery"
                                placeholder="Type category name..." class="input input-bordered w-full" />

                            <template x-if="categoryLoading">
                                <p class="text-gray-500 text-sm mt-3">Searching categories...</p>
                            </template>

                            <template x-if="categoryResults.length > 0">
                                <ul
                                    class="border border-base-300 rounded-box mt-3 divide-y divide-base-300 max-h-48 overflow-y-auto">
                                    <template x-for="item in categoryResults" :key="item.id">
                                        <li @click="addCategory(item)"
                                            class="px-3 py-2 cursor-pointer hover:bg-base-200 flex justify-between text-sm">
                                            <span x-text="item.name"></span>
                                        </li>
                                    </template>
                                </ul>
                            </template>

                            <div class="pt-2">
                                <p class="font-medium mb-1 text-sm">Selected Categories</p>
                                <template x-if="selectedCategories.length === 0">
                                    <p class="text-sm text-gray-500">No category selected</p>
                                </template>
                                <template x-for="(category, index) in selectedCategories" :key="category.id">
                                    <div
                                        class="flex justify-between items-center mb-1 border border-base-300 rounded-box py-1 px-3">
                                        <span class="text-xs" x-text="category.name"></span>
                                        <button type="button" @click="removeCategory(index)"
                                            class="btn btn-xs btn-ghost btn-square">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-4 stroke-error">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div class="modal-action mt-3">
                        <button type="submit" class="btn btn-primary">Create Coupon</button>
                    </div>
                </form>
            </div>
        </dialog>
    </div>
@endsection

@push('script')
    <script>
        function couponDetail(initialCategoryIds = [], initialProductIds = []) {
            return {
                initialCategoryIds,
                initialProductIds,
                selectedCategories: [],
                selectedProducts: [],
                categoryLoading: false,
                productLoading: false,

                async fetchProducts() {
                    if (this.productLoading || this.categoryLoading) return;

                    this.productLoading = true;
                    this.categoryLoading = true;
                    try {

                        if (this.initialCategoryIds.length > 0) {
                            let categoryResponse = await axios.post('/admin/dashboard/category/search-ids', {
                                ids: this.initialCategoryIds
                            });
                            this.selectedCategories = categoryResponse.data.data ?? [];
                            this.categoryLoading = false;
                        }

                        if (this.initialProductIds.length > 0) {
                            let productResponse = await axios.post('/admin/dashboard/product/search-ids', {
                                ids: this.initialProductIds
                            });
                            this.selectedProducts = productResponse.data.data ?? [];
                            this.productLoading = false;
                        }



                    } catch (e) {
                        console.error('Failed to load existing products', e);
                    } finally {
                        this.productLoading = false;
                        this.categoryLoading = false;
                    }
                },
            };
        }

        function couponForm(initialCategoryIds = [], initialProductIds = []) {
            return {
                // category section
                categoryQuery: '',
                categoryResults: [],
                selectedCategories: [],
                categoryLoading: false,
                initialCategoryIds: initialCategoryIds,

                // product section
                productQuery: '',
                productResults: [],
                selectedProducts: [],
                productLoading: false,
                initialProductIds: initialProductIds,

                async beforeSubmit() {
                    const form = this.$refs.couponForm;

                    // append selected categories
                    this.selectedCategories.forEach(c => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'category_ids[]';
                        input.value = c.id;
                        form.appendChild(input);
                    });

                    // append selected products
                    this.selectedProducts.forEach(p => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'product_ids[]';
                        input.value = p.id;
                        form.appendChild(input);
                    });

                    form.submit();
                },

                async searchCategories() {
                    if (this.categoryQuery.length < 2) {
                        this.categoryResults = [];
                        return;
                    }
                    this.categoryLoading = true;
                    try {
                        const response = await axios.get(`/admin/dashboard/category/search?q=${this.categoryQuery}`);
                        this.categoryResults = response.data.data ?? [];
                    } catch (e) {
                        console.error(e);
                    } finally {
                        this.categoryLoading = false;
                    }
                },

                addCategory(category) {
                    if (!this.selectedCategories.find(c => c.id === category.id)) {
                        this.selectedCategories.push(category);
                    }
                    this.categoryQuery = '';
                    this.categoryResults = [];
                },

                removeCategory(index) {
                    this.selectedCategories.splice(index, 1);
                },

                // ============ PRODUCT ============
                async searchProducts() {
                    if (this.productQuery.length < 2) {
                        this.productResults = [];
                        return;
                    }
                    this.productLoading = true;
                    try {
                        const response = await axios.get(`/admin/dashboard/product/search?q=${this.productQuery}`);
                        this.productResults = response.data.data ?? [];
                    } catch (e) {
                        console.error(e);
                    } finally {
                        this.productLoading = false;
                    }
                },

                addProduct(product) {
                    if (!this.selectedProducts.find(p => p.id === product.id)) {
                        this.selectedProducts.push(product);
                    }
                    this.productQuery = '';
                    this.productResults = [];
                },

                removeProduct(index) {
                    this.selectedProducts.splice(index, 1);
                },

                async init() {
                    this.$watch('categoryQuery', () => this.searchCategories());
                    this.$watch('productQuery', () => this.searchProducts());

                    if (this.initialCategoryIds.length > 0) {
                        try {
                            const res = await axios.post('/admin/dashboard/category/search-ids', {
                                ids: this.initialCategoryIds
                            });
                            this.selectedCategories = res.data.data ?? [];
                        } catch (e) {
                            console.error('Failed to load existing categories', e);
                        }
                    }

                    if (this.initialProductIds.length > 0) {
                        try {
                            const res = await axios.post('/admin/dashboard/product/search-ids', {
                                ids: this.initialProductIds
                            });
                            this.selectedProducts = res.data.data ?? [];
                        } catch (e) {
                            console.error('Failed to load existing products', e);
                        }
                    }
                }
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
                        Toast.show('Please select at least one product', {
                            type: 'error'
                        });
                        return;
                    }
                    const modal = document.getElementById(this.current_action);
                    if (modal) modal.showModal();
                },
            });

            Alpine.store('coupon_search_setting', {
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
                    const savedSetting = JSON.parse(localStorage.getItem('coupon_search_setting') ?? "{}");

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
                    localStorage.setItem('coupon_search_setting', JSON.stringify(data));
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

            Alpine.store('coupon_search_setting').init();
        });
    </script>
@endpush
