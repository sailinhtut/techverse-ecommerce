@php
    $site_name = getParsedTemplate('site_name');
    $site_logo = getSiteLogoURL();
@endphp


@extends('layouts.admin.admin_dashboard')
@section('admin_dashboard_content')
    <div class="p-3 lg:p-5 min-h-screen">
        <p class="text-lg font-semibold">Product Management</p>

        <div class="mt-3 flex xl:flex-row flex-col justify-between gap-2">

            <div class="flex flex-row gap-2 flex-wrap" x-data>
                <div class="join join-horizontal">
                    <select class="select select-sm join-item" x-model="$store.bulk.current_action">
                        <option value="">Bulk Actions</option>
                        <option value="bulk_delete_selected">Delete Selected</option>
                        <option value="bulk_delete_all">Delete All</option>
                        <option value="bulk_update_payment_method_selected">Update Payment Method Selected</option>
                        <option value="bulk_update_payment_method_all">Update Payment Method All</option>
                        <option value="bulk_update_shipping_class_selected">Update Shipping Class Selected</option>
                        <option value="bulk_update_shipping_class_all">Update Shipping Class All</option>
                        <option value="bulk_update_tax_class_selected">Update Tax Class Selected</option>
                        <option value="bulk_update_tax_class_all">Update Tax Class All</option>
                    </select>
                    <button class="join-item btn btn-sm" @click="$store.bulk.commit()">Commit</button>
                </div>
                <a href="{{ route('admin.dashboard.product.add.get') }}" class="btn btn-sm shadow-none">Add Product</a>
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
                        <span class="italic text-error">Selected Products</span>?
                    </p>

                    <template x-if="$store.bulk.hasCandidates">
                        <ul class="text-sm pl-10 list-decimal max-h-24 overflow-y-auto bg-base-200 p-3">
                            <template x-for="id in $store.bulk.candidates" :key="id">
                                <li x-text="'Product ID: ' + id"></li>
                            </template>
                        </ul>
                    </template>

                    <div class="mt-3 modal-action">
                        <form method="dialog"><button class="btn" :disabled="loading">Cancel</button></form>

                        <form method="POST" action="{{ route('admin.dashboard.product.bulk.delete-selected') }}"
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
                        <span class="text-error">All Products</span>?
                    </p>

                    <div class="modal-action">
                        <form method="dialog"><button class="btn" :disabled="loading">Cancel</button></form>

                        <form method="POST" action="{{ route('admin.dashboard.product.bulk.delete-all') }}"
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

            {{-- bulk payment method update selected modal --}}
            <dialog id="bulk_update_payment_method_selected" class="modal" x-data="{ loading: false, selectedPaymentIds: [] }">
                <div class="modal-box relative">
                    <form method="dialog">
                        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                    </form>

                    <p class="text-lg font-semibold py-0">Update Payment Method</p>

                    <p class="py-2 mb-0 text-sm">
                        Are you sure you want to update
                        <span class="italic text-error">Selected Products</span>?
                    </p>

                    <template x-if="$store.bulk.hasCandidates">
                        <ul class="text-sm pl-10 list-decimal max-h-24 overflow-y-auto bg-base-200 p-3">
                            <template x-for="id in $store.bulk.candidates" :key="id">
                                <li x-text="'Product ID: ' + id"></li>
                            </template>
                        </ul>
                    </template>

                    <div class="mt-3 w-full flex flex-col gap-2">
                        <label class="text-sm mb-3">Payment Methods</label>
                        <div class="flex flex-wrap gap-4">
                            @foreach ($payment_methods as $method)
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" class="checkbox checkbox-sm"
                                        @change="selectedPaymentIds = selectedPaymentIds.includes('{{ $method['id'] }}') ? selectedPaymentIds.filter((e)=> e != '{{ $method['id'] }}') : [...selectedPaymentIds,'{{ $method['id'] }}']"
                                        value="{{ $method['id'] }}">
                                    <span class="text-sm">{{ $method['name'] }}</span>
                                </label>
                            @endforeach
                        </div>

                    </div>

                    <div class="mt-3 modal-action">
                        <form method="dialog"><button class="btn" :disabled="loading">Cancel</button></form>

                        <form method="POST"
                            action="{{ route('admin.dashboard.product.bulk.update-payment-method-selected') }}"
                            @submit="loading = true">
                            @csrf
                            <template x-for="id in $store.bulk.candidates" :key="id">
                                <input type="hidden" name="ids[]" :value="id">
                            </template>

                            <template x-for="id in selectedPaymentIds" :key="id">
                                <input type="hidden" name="payment_methods[]" :value="id">
                            </template>

                            <button type="submit" class="btn btn-primary flex items-center gap-2"
                                :disabled="loading">
                                <span x-show="!loading">Update Payment Method</span>
                                <span x-show="loading" class="loading loading-spinner loading-xs"></span>
                                <span x-show="loading">Updating...</span>
                            </button>
                        </form>
                    </div>
                </div>
            </dialog>

            {{-- bulk payment method update all modal --}}
            <dialog id="bulk_update_payment_method_all" class="modal" x-data="{ loading: false, selectedPaymentIds: [] }">
                <div class="modal-box relative">
                    <form method="dialog">
                        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                    </form>

                    <p class="text-lg font-semibold py-0">Update Payment Method</p>
                    <p class="text-sm mb-4">
                        Are you sure you want to update
                        <span class="text-error">All Products</span> ? Updating may take some time depending on the number
                        of products.
                    </p>

                    <div class="mt-3 w-full flex flex-col gap-2">
                        <label class="text-sm mb-3">Payment Methods</label>
                        <div class="flex flex-wrap gap-4">
                            @foreach ($payment_methods as $method)
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" class="checkbox checkbox-sm"
                                        @change="selectedPaymentIds = selectedPaymentIds.includes('{{ $method['id'] }}') ?  selectedPaymentIds.filter((e)=> e != '{{ $method['id'] }}') : [...selectedPaymentIds,'{{ $method['id'] }}']"
                                        value="{{ $method['id'] }}">
                                    <span class="text-sm">{{ $method['name'] }}</span>
                                </label>
                            @endforeach
                        </div>

                    </div>

                    <div class="mt-3 modal-action">
                        <form method="dialog"><button class="btn" :disabled="loading">Cancel</button></form>

                        <form method="POST"
                            action="{{ route('admin.dashboard.product.bulk.update-payment-method-all') }}"
                            @submit="loading = true">
                            @csrf

                            <template x-for="id in selectedPaymentIds" :key="id">
                                <input type="hidden" name="payment_methods[]" :value="id">
                            </template>

                            <button type="submit" class="btn btn-primary flex items-center gap-2"
                                :disabled="loading">
                                <span x-show="!loading">Update Payment Method</span>
                                <span x-show="loading" class="loading loading-spinner loading-xs"></span>
                                <span x-show="loading">Updating...</span>
                            </button>
                        </form>
                    </div>
                </div>
            </dialog>

            {{-- bulk shipping class selected modal --}}
            <dialog id="bulk_update_shipping_class_selected" class="modal" x-data="{ loading: false, selectedShippingClass: '' }">
                <div class="modal-box relative">
                    <form method="dialog">
                        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                    </form>

                    <p class="text-lg font-semibold py-0">Update Shipping Class</p>

                    <p class="py-2 mb-0 text-sm">
                        Are you sure you want to updated
                        <span class="italic text-error">Selected Products</span>?
                    </p>

                    <template x-if="$store.bulk.hasCandidates">
                        <ul class="text-sm pl-10 list-decimal max-h-24 overflow-y-auto bg-base-200 p-3">
                            <template x-for="id in $store.bulk.candidates" :key="id">
                                <li x-text="'Product ID: ' + id"></li>
                            </template>
                        </ul>
                    </template>

                    <div class="mt-3 w-full flex flex-col gap-2">
                        <label class="text-sm mb-3">Shipping Classes</label>
                        <div class="w-full flex flex-col gap-2">
                            <label class="text-sm">Shipping Class</label>
                            <select name="shipping_class_id" class="select w-full" x-model="selectedShippingClass">
                                <option value="">No Shipping Class</option>
                                @foreach ($shipping_classes as $class)
                                    <option value="{{ $class['id'] }}">
                                        {{ $class['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mt-3 modal-action">
                        <form method="dialog"><button class="btn" :disabled="loading">Cancel</button></form>

                        <form method="POST"
                            action="{{ route('admin.dashboard.product.bulk.update-shipping-class-selected') }}"
                            @submit="loading = true">
                            @csrf
                            <template x-for="id in $store.bulk.candidates" :key="id">
                                <input type="hidden" name="ids[]" :value="id">
                            </template>

                            <template x-if="selectedShippingClass">
                                <input type="hidden" name="shipping_class_id" :value="selectedShippingClass">
                            </template>

                            <button type="submit" class="btn btn-primary flex items-center gap-2"
                                :disabled="loading">
                                <span x-show="!loading">Update Shipping Class</span>
                                <span x-show="loading" class="loading loading-spinner loading-xs"></span>
                                <span x-show="loading">Updating...</span>
                            </button>
                        </form>
                    </div>
                </div>
            </dialog>

            {{-- bulk shipping class selected modal --}}
            <dialog id="bulk_update_shipping_class_all" class="modal" x-data="{ loading: false, selectedShippingClass: '' }">
                <div class="modal-box relative">
                    <form method="dialog">
                        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                    </form>

                    <p class="text-lg font-semibold py-0">Update Shipping Class</p>
                    <p class="text-sm mb-4">
                        Are you sure you want to update
                        <span class="text-error">All Products</span> ? Updating may take some time depending on the number
                        of products.
                    </p>

                    <div class="mt-3 w-full flex flex-col gap-2">
                        <label class="text-sm">Shipping Class</label>
                        <select name="shipping_class_id" class="select w-full" x-model="selectedShippingClass">
                            <option value="">No Shipping Class</option>
                            @foreach ($shipping_classes as $class)
                                <option value="{{ $class['id'] }}">
                                    {{ $class['name'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mt-3 modal-action">
                        <form method="dialog"><button class="btn" :disabled="loading">Cancel</button></form>

                        <form method="POST"
                            action="{{ route('admin.dashboard.product.bulk.update-shipping-class-all') }}"
                            @submit="loading = true">
                            @csrf
                            <template x-for="id in $store.bulk.candidates" :key="id">
                                <input type="hidden" name="ids[]" :value="id">
                            </template>

                            <template x-if="selectedShippingClass">
                                <input type="hidden" name="shipping_class_id" :value="selectedShippingClass">
                            </template>

                            <button type="submit" class="btn btn-primary flex items-center gap-2"
                                :disabled="loading">
                                <span x-show="!loading">Update Shipping Class</span>
                                <span x-show="loading" class="loading loading-spinner loading-xs"></span>
                                <span x-show="loading">Updating...</span>
                            </button>
                        </form>
                    </div>
                </div>
            </dialog>

            {{-- bulk tax class selected modal --}}
            <dialog id="bulk_update_tax_class_selected" class="modal" x-data="{ loading: false, selectedTaxClass: '' }">
                <div class="modal-box relative">
                    <form method="dialog">
                        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                    </form>

                    <p class="text-lg font-semibold py-0">Update Tax Class</p>

                    <p class="py-2 mb-0 text-sm">
                        Are you sure you want to updated
                        <span class="italic text-error">Selected Products</span>?
                    </p>

                    <template x-if="$store.bulk.hasCandidates">
                        <ul class="text-sm pl-10 list-decimal max-h-24 overflow-y-auto bg-base-200 p-3">
                            <template x-for="id in $store.bulk.candidates" :key="id">
                                <li x-text="'Product ID: ' + id"></li>
                            </template>
                        </ul>
                    </template>

                    <div class="mt-3 w-full flex flex-col gap-2">
                        <label class="text-sm mb-3">Tax Classes</label>
                        <div class="w-full flex flex-col gap-2">
                            <label class="text-sm">Tax Class</label>
                            <select name="shipping_class_id" class="select w-full" x-model="selectedTaxClass">
                                <option value="">No Tax Class</option>
                                @foreach ($tax_classes as $class)
                                    <option value="{{ $class['id'] }}">
                                        {{ $class['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mt-3 modal-action">
                        <form method="dialog"><button class="btn" :disabled="loading">Cancel</button></form>

                        <form method="POST"
                            action="{{ route('admin.dashboard.product.bulk.update-tax-class-selected') }}"
                            @submit="loading = true">
                            @csrf
                            <template x-for="id in $store.bulk.candidates" :key="id">
                                <input type="hidden" name="ids[]" :value="id">
                            </template>

                            <template x-if="selectedTaxClass">
                                <input type="hidden" name="tax_class_id" :value="selectedTaxClass">
                            </template>

                            <button type="submit" class="btn btn-primary flex items-center gap-2"
                                :disabled="loading">
                                <span x-show="!loading">Update Tax Class</span>
                                <span x-show="loading" class="loading loading-spinner loading-xs"></span>
                                <span x-show="loading">Updating...</span>
                            </button>
                        </form>
                    </div>
                </div>
            </dialog>

            {{-- bulk tax class all modal --}}
            <dialog id="bulk_update_tax_class_all" class="modal" x-data="{ loading: false, selectedTaxClass: '' }">
                <div class="modal-box relative">
                    <form method="dialog">
                        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                    </form>

                    <p class="text-lg font-semibold py-0">Update Tax Class</p>
                    <p class="text-sm mb-4">
                        Are you sure you want to update
                        <span class="text-error">All Products</span> ? Updating may take some time depending on the number
                        of products.
                    </p>

                    <div class="mt-3 w-full flex flex-col gap-2">
                        <label class="text-sm">Tax Class</label>
                        <select name="shipping_class_id" class="select w-full" x-model="selectedTaxClass">
                            <option value="">No Tax Class</option>
                            @foreach ($tax_classes as $class)
                                <option value="{{ $class['id'] }}">
                                    {{ $class['name'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mt-3 modal-action">
                        <form method="dialog"><button class="btn" :disabled="loading">Cancel</button></form>

                        <form method="POST"
                            action="{{ route('admin.dashboard.product.bulk.update-tax-class-all') }}"
                            @submit="loading = true">
                            @csrf
                            <template x-for="id in $store.bulk.candidates" :key="id">
                                <input type="hidden" name="ids[]" :value="id">
                            </template>

                            <template x-if="selectedTaxClass">
                                <input type="hidden" name="tax_class_id" :value="selectedTaxClass">
                            </template>

                            <button type="submit" class="btn btn-primary flex items-center gap-2"
                                :disabled="loading">
                                <span x-show="!loading">Update Tax Class</span>
                                <span x-show="loading" class="loading loading-spinner loading-xs"></span>
                                <span x-show="loading">Updating...</span>
                            </button>
                        </form>
                    </div>
                </div>
            </dialog>


            {{-- Filtering Section --}}
            <div class="flex flex-row flex-wrap justify-start xl:justify-end gap-2">
                {{-- product searching --}}
                <form id="queryForm" method="GET" action="{{ request()->url() }}" class="join join-horizontal">
                    <template x-data x-if="$store.product_search_setting.sortBy">
                        <input type="hidden" name="sortBy" :value="$store.product_search_setting.sortBy">
                    </template>

                    <template x-data x-if="$store.product_search_setting.perPage">
                        <input type="hidden" x-data name="perPage" :value="$store.product_search_setting.perPage">
                    </template>

                    <template x-data x-if="$store.product_search_setting.orderBy">
                        <input type="hidden" x-data name="orderBy" :value="$store.product_search_setting.orderBy">
                    </template>

                    <template x-data
                        x-if="$store.product_search_setting.categoryFilter && $store.product_search_setting.categoryFilter.length > 0">
                        <input type="hidden" x-data name="category"
                            :value="$store.product_search_setting.categoryFilter">
                    </template>

                    <template x-data
                        x-if="$store.product_search_setting.brandFilter && $store.product_search_setting.brandFilter.length > 0">
                        <input type="hidden" x-data name="brand" :value="$store.product_search_setting.brandFilter">
                    </template>

                    <template x-data x-if="$store.product_search_setting.saleFilter">
                        <input type="hidden" x-data name="isSale" :value="$store.product_search_setting.saleFilter">
                    </template>

                    <template x-data x-if="$store.product_search_setting.promotionFilter">
                        <input type="hidden" x-data name="isPromotion"
                            :value="$store.product_search_setting.promotionFilter">
                    </template>

                    <template x-data x-if="$store.product_search_setting.pinnedFilter">
                        <input type="hidden" x-data name="isPinned" :value="$store.product_search_setting.pinnedFilter">
                    </template>

                    <input type="text" x-data x-cloak class="join-item input input-sm rounded-l-box" name="query"
                        :value="$store.product_search_setting.query"
                        @change="$store.product_search_setting.query = $event.target.value; $store.product_search_setting.save(); $el.form.submit()">

                    <button class="join-item btn btn-sm">Search</button>
                </form>

                {{-- page limiting --}}
                <form method="GET" action="{{ request()->url() }}">
                    <template x-data
                        x-if="$store.product_search_setting.query && $store.product_search_setting.query.length > 0">
                        <input type="hidden" name="query" :value="$store.product_search_setting.query">
                    </template>

                    <template x-data x-if="$store.product_search_setting.sortBy">
                        <input type="hidden" x-data name="sortBy" :value="$store.product_search_setting.sortBy">
                    </template>

                    <template x-data x-if="$store.product_search_setting.orderBy">
                        <input type="hidden" x-data name="orderBy" :value="$store.product_search_setting.orderBy">
                    </template>

                    <template x-data
                        x-if="$store.product_search_setting.categoryFilter && $store.product_search_setting.categoryFilter.length > 0">
                        <input type="hidden" x-data name="category"
                            :value="$store.product_search_setting.categoryFilter">
                    </template>

                    <template x-data
                        x-if="$store.product_search_setting.brandFilter && $store.product_search_setting.brandFilter.length > 0">
                        <input type="hidden" x-data name="brand" :value="$store.product_search_setting.brandFilter">
                    </template>

                    <template x-data x-if="$store.product_search_setting.saleFilter">
                        <input type="hidden" x-data name="isSale" :value="$store.product_search_setting.saleFilter">
                    </template>

                    <template x-data x-if="$store.product_search_setting.promotionFilter">
                        <input type="hidden" x-data name="isPromotion"
                            :value="$store.product_search_setting.promotionFilter">
                    </template>

                    <template x-data x-if="$store.product_search_setting.pinnedFilter">
                        <input type="hidden" x-data name="isPinned" :value="$store.product_search_setting.pinnedFilter">
                    </template>

                    <select name="perPage" x-data x-cloak class="select select-sm w-fit shrink-0" x-data
                        x-model="$store.product_search_setting.perPage"
                        @change="$store.product_search_setting.perPage = $event.target.value; $store.product_search_setting.save(); $el.form.submit()">
                        <option value="5">Show 5</option>
                        <option value="10">Show 10</option>
                        <option value="20">Show 20</option>
                        <option value="50">Show 50</option>
                    </select>
                </form>

                {{-- ascending & descending --}}
                <form method="GET" action="{{ request()->url() }}">
                    <template x-data
                        x-if="$store.product_search_setting.query && $store.product_search_setting.query.length > 0">
                        <input type="hidden" name="query" :value="$store.product_search_setting.query">
                    </template>

                    <template x-data x-if="$store.product_search_setting.perPage">
                        <input type="hidden" x-data name="perPage" :value="$store.product_search_setting.perPage">
                    </template>

                    <template x-data x-if="$store.product_search_setting.sortBy">
                        <input type="hidden" x-data name="sortBy" :value="$store.product_search_setting.sortBy">
                    </template>

                    <template x-data
                        x-if="$store.product_search_setting.categoryFilter && $store.product_search_setting.categoryFilter.length > 0">
                        <input type="hidden" x-data name="category"
                            :value="$store.product_search_setting.categoryFilter">
                    </template>

                    <template x-data
                        x-if="$store.product_search_setting.brandFilter && $store.product_search_setting.brandFilter.length > 0">
                        <input type="hidden" x-data name="brand" :value="$store.product_search_setting.brandFilter">
                    </template>

                    <template x-data x-if="$store.product_search_setting.saleFilter">
                        <input type="hidden" x-data name="isSale" :value="$store.product_search_setting.saleFilter">
                    </template>

                    <template x-data x-if="$store.product_search_setting.promotionFilter">
                        <input type="hidden" x-data name="isPromotion"
                            :value="$store.product_search_setting.promotionFilter">
                    </template>

                    <template x-data x-if="$store.product_search_setting.pinnedFilter">
                        <input type="hidden" x-data name="isPinned" :value="$store.product_search_setting.pinnedFilter">
                    </template>


                    <select name="orderBy" x-data x-cloak x-model="$store.product_search_setting.orderBy"
                        class="select select-sm w-fit shrink-0" x-data :value="$store.product_search_setting.orderBy"
                        @change="$store.product_search_setting.orderBy = $event.target.value; $store.product_search_setting.save() ; $el.form.submit()">
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
                        x-if="$store.product_search_setting.query && $store.product_search_setting.query.length > 0">
                        <input type="hidden" name="query" :value="$store.product_search_setting.query">
                    </template>

                    <template x-data x-if="$store.product_search_setting.perPage">
                        <input type="hidden" x-data name="perPage" :value="$store.product_search_setting.perPage">
                    </template>

                    <template x-data x-if="$store.product_search_setting.orderBy">
                        <input type="hidden" x-data name="orderBy" :value="$store.product_search_setting.orderBy">
                    </template>

                    <template x-data
                        x-if="$store.product_search_setting.categoryFilter && $store.product_search_setting.categoryFilter.length > 0">
                        <input type="hidden" x-data name="category"
                            :value="$store.product_search_setting.categoryFilter">
                    </template>

                    <template x-data
                        x-if="$store.product_search_setting.brandFilter && $store.product_search_setting.brandFilter.length > 0">
                        <input type="hidden" x-data name="brand" :value="$store.product_search_setting.brandFilter">
                    </template>

                    <template x-data x-if="$store.product_search_setting.saleFilter">
                        <input type="hidden" x-data name="isSale" :value="$store.product_search_setting.saleFilter">
                    </template>

                    <template x-data x-if="$store.product_search_setting.promotionFilter">
                        <input type="hidden" x-data name="isPromotion"
                            :value="$store.product_search_setting.promotionFilter">
                    </template>

                    <template x-data x-if="$store.product_search_setting.pinnedFilter">
                        <input type="hidden" x-data name="isPinned" :value="$store.product_search_setting.pinnedFilter">
                    </template>

                    <select name="sortBy" class="select select-sm w-fit shrink-0" x-data x-cloak
                        :value="$store.product_search_setting.sortBy"
                        @change="$store.product_search_setting.sortBy = $event.target.value; $store.product_search_setting.save() ; $el.form.submit()">
                        <option value="last_updated">Sort By Last Updated
                        </option>
                        <option value="last_created">Sort By Last Created
                        </option>
                        <option value="high_priority">Sort By High Priority
                        </option>
                        <option value="low_priority">Sort By Low Priority
                        </option>
                        <option value="high_popularity">Sort By High Popularity
                        </option>
                        <option value="low_popularity">Sort By Low Popularity
                        </option>
                    </select>
                </form>

                <div tabindex="0" role="button" class="dropdown dropdown-end">
                    <div class="btn btn-square btn-sm btn-ghost">
                        <button class="btn btn-square btn-sm bg-base-100 border-slate-300 shadow-none">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 6.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 12.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 18.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5Z" />
                            </svg>
                        </button>
                    </div>
                    <ul tabindex="0"
                        class="menu dropdown-content bg-base-100 border border-base-300 w-[180px] rounded-box p-1 mt-1 shadow-sm">
                        <li>
                            <button x-data x-transition x-cloak
                                @click="$store.product_search_setting.showFilterOption = !$store.product_search_setting.showFilterOption;$store.product_search_setting.save()">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
                                </svg>
                                <span
                                    x-text="$store.product_search_setting.showFilterOption ? 'Hide Filter' : 'Filter Option'"></span>
                            </button>
                        </li>
                        <li>
                            <button x-data x-transition x-cloak
                                @click="$store.product_search_setting.showDisplayOption = !$store.product_search_setting.showDisplayOption;$store.product_search_setting.save()">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 4.5v15m6-15v15m-10.875 0h15.75c.621 0 1.125-.504 1.125-1.125V5.625c0-.621-.504-1.125-1.125-1.125H4.125C3.504 4.5 3 5.004 3 5.625v12.75c0 .621.504 1.125 1.125 1.125Z" />
                                </svg>
                                <span
                                    x-text="$store.product_search_setting.showDisplayOption ? 'Hide Display' : 'Display Option'"></span>
                            </button>
                        </li>
                        <li>
                            <a href="#">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-5 text-success">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 0 1-1.125-1.125M3.375 19.5h7.5c.621 0 1.125-.504 1.125-1.125m-9.75 0V5.625m0 12.75v-1.5c0-.621.504-1.125 1.125-1.125m18.375 2.625V5.625m0 12.75c0 .621-.504 1.125-1.125 1.125m1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125m0 3.75h-7.5A1.125 1.125 0 0 1 12 18.375m9.75-12.75c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125m19.5 0v1.5c0 .621-.504 1.125-1.125 1.125M2.25 5.625v1.5c0 .621.504 1.125 1.125 1.125m0 0h17.25m-17.25 0h7.5c.621 0 1.125.504 1.125 1.125M3.375 8.25c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125m17.25-3.75h-7.5c-.621 0-1.125.504-1.125 1.125m8.625-1.125c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h7.5m-7.5 0c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125M12 10.875v-1.5m0 1.5c0 .621-.504 1.125-1.125 1.125M12 10.875c0 .621.504 1.125 1.125 1.125m-2.25 0c.621 0 1.125.504 1.125 1.125M13.125 12h7.5m-7.5 0c-.621 0-1.125.504-1.125 1.125M20.625 12c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h7.5M12 14.625v-1.5m0 1.5c0 .621-.504 1.125-1.125 1.125M12 14.625c0 .621.504 1.125 1.125 1.125m-2.25 0c.621 0 1.125.504 1.125 1.125m0 1.5v-1.5m0 0c0-.621.504-1.125 1.125-1.125m0 0h7.5" />
                                </svg>
                                Export Products
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- filtering --}}
        <div class="mt-3 p-3 border border-base-300 rounded-md flex flex-col gap-2" x-cloak x-transition x-data
            x-show="$store.product_search_setting.showFilterOption">
            <p class="text-xs">Filter Options</p>
            <div class="grid grid-cols-2 md:grid-cols-4 xl:grid-cols-6 gap-3">
                <div class="flex flex-col gap-1 ">
                    <select x-data class="select select-sm text-xs" name="category"
                        :value="$store.product_search_setting.categoryFilter"
                        @change="$store.product_search_setting.categoryFilter = $event.target.value; $store.product_search_setting.save()">
                        <div class="max-h-[200px] overflow-y-auto">
                            <option value="" :selected="!$store.product_search_setting.categoryFilter">Filter
                                Category</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category['slug'] }}">{{ $category['name'] }}</option>
                            @endforeach
                        </div>
                    </select>
                </div>

                <div class="flex flex-col gap-1">
                    <select x-data class="select select-sm text-xs" name="brand"
                        :value="$store.product_search_setting.brandFilter"
                        @change="$store.product_search_setting.brandFilter = $event.target.value; $store.product_search_setting.save()">
                        <div class="max-h-[200px] overflow-y-auto">
                            <option value="" :selected="!$store.product_search_setting.brandFilter">Filter Brand
                            </option>
                            @foreach ($brands as $brand)
                                <option value="{{ $brand['slug'] }}">
                                    {{ $brand['name'] }}
                                </option>
                            @endforeach
                        </div>
                    </select>
                </div>

                <div class="flex flex-col gap-1">
                    <select x-data class="select select-sm text-xs" name="brand"
                        :value="$store.product_search_setting.saleFilter ? 'true' : 'false'"
                        @change="$store.product_search_setting.saleFilter = $event.target.value; $store.product_search_setting.save()">
                        <option value="false">Filter Sale</option>
                        <option value="true">Sale Product</option>
                    </select>
                </div>

                <div class="flex flex-col gap-1">
                    <select x-data class="select select-sm text-xs" name="brand"
                        :value="$store.product_search_setting.promotionFilter ? 'true' : 'false'"
                        @change="$store.product_search_setting.promotionFilter = $event.target.value; $store.product_search_setting.save()">
                        <option value="false">Filter Promotion</option>
                        <option value="true">Promotion Product</option>
                    </select>
                </div>

                <div class="flex flex-col gap-1">
                    <select x-data class="select select-sm text-xs" name="brand"
                        :value="$store.product_search_setting.pinnedFilter ? 'true' : 'false'"
                        @change="$store.product_search_setting.pinnedFilter = $event.target.value; $store.product_search_setting.save()">
                        <option value="false">Filter Pinned</option>
                        <option value="true">Pinned Product</option>
                    </select>
                </div>
            </div>
            <div class="flex flex-row gap-2">
                <button class="btn btn-sm" x-data @click="document.getElementById('queryForm').requestSubmit();">Search</button>
                <button class="btn btn-sm" x-data @click="$store.product_search_setting.resetFilter()">Reset</button>
            </div>
        </div>

        {{-- column displaying --}}
        <div class="mt-3 p-3 border border-base-300 rounded-md flex flex-col gap-2" x-cloak x-transition x-data
            x-show="$store.product_search_setting.showDisplayOption">
            <p class="text-xs">Display Options</p>
            <div class="flex sm:flex-row flex-col flex-wrap gap-3">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" class="checkbox checkbox-xs rounded-sm" x-data
                        :checked="$store.product_search_setting.showIDColumn"
                        @change="$store.product_search_setting.showIDColumn = !$store.product_search_setting.showIDColumn; $store.product_search_setting.save()">
                    <span class="text-xs">Show ID</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" class="checkbox checkbox-xs rounded-sm" x-data
                        :checked="$store.product_search_setting.showPaymentMethodColumn"
                        @change="$store.product_search_setting.showPaymentMethodColumn = !$store.product_search_setting.showPaymentMethodColumn; $store.product_search_setting.save()">
                    <span class="text-xs">Show Payment Method</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" class="checkbox checkbox-xs rounded-sm" x-data
                        :checked="$store.product_search_setting.showShippingClassColumn"
                        @change="$store.product_search_setting.showShippingClassColumn = !$store.product_search_setting.showShippingClassColumn; $store.product_search_setting.save()">
                    <span class="text-xs">Show Shipping Class</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" class="checkbox checkbox-xs rounded-sm" x-data
                        :checked="$store.product_search_setting.showPriorityColumn || $store.product_search_setting
                            .is_priority_filter"
                        @change="$store.product_search_setting.showPriorityColumn = !$store.product_search_setting.showPriorityColumn; $store.product_search_setting.save()">
                    <span class="text-xs">Show Priority</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" class="checkbox checkbox-xs rounded-sm" x-data
                        :checked="$store.product_search_setting.showViewColumn || $store.product_search_setting
                            .is_popularity_filter"
                        @change="$store.product_search_setting.showViewColumn = !$store.product_search_setting.showViewColumn; $store.product_search_setting.save()">
                    <span class="text-xs">Show Views</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" class="checkbox checkbox-xs rounded-sm" x-data
                        :checked="$store.product_search_setting.showUpdatedTimeColumn || $store.product_search_setting
                            .is_last_updated_filter"
                        @change="$store.product_search_setting.showUpdatedTimeColumn = !$store.product_search_setting.showUpdatedTimeColumn; $store.product_search_setting.save()">
                    <span class="text-xs">Show Updated Time</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" class="checkbox checkbox-xs rounded-sm" x-data
                        :checked="$store.product_search_setting.showCreatedTimeColumn || $store.product_search_setting
                            .is_last_created_filter"
                        @change="$store.product_search_setting.showCreatedTimeColumn = !$store.product_search_setting.showCreatedTimeColumn; $store.product_search_setting.save()">
                    <span class="text-xs">Show Created Time</span>
                </label>
            </div>
        </div>

        <div class="mt-3 card border border-base-300 rounded-t-box ">
            <div class="card-body p-0 m-0 overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="w-[10px]">
                                <input type="checkbox" class="checkbox checkbox-xs rounded-sm" x-data
                                    :checked="$store.bulk.isAllSelected()"
                                    @change="$store.bulk.toggleSelectAll($el.checked)">
                            </th>
                            <th class="w-[50px]">No.</th>
                            <th x-cloak x-data x-show="$store.product_search_setting.showIDColumn" class="w-[50px]">ID
                            </th>
                            <th class="w-[50px]">Image</th>
                            <th class="w-[200px]">Name</th>
                            <th>Stock</th>
                            <th>Price</th>
                            <th>Sale</th>
                            <th x-cloak x-data x-show="$store.product_search_setting.showPaymentMethodColumn">Payment
                                Method</th>
                            <th x-cloak x-data x-show="$store.product_search_setting.showShippingClassColumn">Shipping
                                Class</th>
                            <th x-cloak x-data
                                x-show="$store.product_search_setting.showPriorityColumn || $store.product_search_setting.is_priority_filter">
                                Priority</th>
                            <th x-cloak x-data
                                x-show="$store.product_search_setting.showViewColumn || $store.product_search_setting.is_popularity_filter">
                                View</th>
                            <th x-cloak x-data
                                x-show="$store.product_search_setting.showUpdatedTimeColumn || $store.product_search_setting.is_last_updated_filter">
                                Updated At</th>
                            <th x-cloak x-data
                                x-show="$store.product_search_setting.showCreatedTimeColumn || $store.product_search_setting.is_last_created_filter">
                                Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                            <tr>
                                <td>
                                    <input type="checkbox" class="checkbox checkbox-xs rounded-sm row-checkbox"
                                        data-id="{{ $product['id'] }}" x-data
                                        :checked="$store.bulk.candidates.includes({{ $product['id'] }})"
                                        @change="$store.bulk.toggleCandidate({{ $product['id'] }})">
                                </td>
                                <td>
                                    {{ $loop->iteration + ($products->currentPage() - 1) * $products->perPage() }}.
                                </td>
                                <td x-cloak x-data x-show="$store.product_search_setting.showIDColumn">
                                    {{ $product['id'] }}
                                </td>
                                <td class="w-[50px]">
                                    @if ($product['image'])
                                        <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}"
                                            class="w-[20px] h-auto object-contain">
                                    @else
                                        <img src="{{ $site_logo }}" alt="{{ $product['name'] }}"
                                            class="w-[30px] h-auto">
                                    @endif
                                </td>
                                <td class="w-[200px] h-[30px] line-clamp-1">
                                    <a href="{{ route('admin.dashboard.product.edit.id.get', ['id' => $product['id']]) }}"
                                        class="cursor-default hover:underline">{{ $product['name'] }}</a>
                                </td>
                                <td>{{ $product['stock'] ?? 'Non Stock' }}</td>
                                <td>{{ $product['regular_price'] ?? '-' }}</td>
                                <td>{{ $product['sale_price'] ?? '-' }}</td>
                                <td x-cloak x-data x-show="$store.product_search_setting.showPaymentMethodColumn">
                                    {{ isset($product['payment_methods']) ? collect($product['payment_methods'])->map(fn($e) => $e['name'])->implode(',') : '-' }}
                                </td>
                                <td x-cloak x-data x-show="$store.product_search_setting.showShippingClassColumn">
                                    {{ $product['shipping_class']['name'] ?? '-' }}</td>
                                <td x-cloak x-data
                                    x-show="$store.product_search_setting.showPriorityColumn || $store.product_search_setting.is_priority_filter">
                                    {{ $product['priority'] ?? '-' }}
                                </td>
                                <td x-cloak x-data
                                    x-show="$store.product_search_setting.showViewColumn || $store.product_search_setting.is_popularity_filter">
                                    {{ $product['interest'] ?? 0 }}</td>
                                <td x-cloak x-data
                                    x-show="$store.product_search_setting.showUpdatedTimeColumn || $store.product_search_setting.is_last_updated_filter">
                                    {{ $product['updated_at'] ? \Carbon\Carbon::parse($product['updated_at'])->format('Y-m-d h:i A') : '-' }}
                                </td>
                                <td x-cloak x-data
                                    x-show="$store.product_search_setting.showCreatedTimeColumn || $store.product_search_setting.is_last_created_filter">
                                    {{ $product['created_at'] ? \Carbon\Carbon::parse($product['created_at'])->format('Y-m-d h:i A') : '-' }}
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
                                                    onclick="document.getElementById('detail_modal_{{ $product['id'] }}').showModal()">
                                                    View
                                                </button>
                                            </li>
                                            <li>
                                                <a
                                                    href="{{ route('admin.dashboard.product.edit.id.get', ['id' => $product['id']]) }}">Edit</a>
                                            </li>
                                            <li>
                                                <button type="button" class="text-error"
                                                    onclick="document.getElementById('deleteModal{{ $product['id'] }}').showModal()">
                                                    Delete
                                                </button>
                                            </li>
                                        </ul>
                                    </div>


                                    <dialog id="detail_modal_{{ $product['id'] }}" class="modal">
                                        <div class="modal-box max-w-3xl max-h-[85vh] overflow-y-auto">
                                            <form method="dialog">
                                                <button
                                                    class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                            </form>

                                            <h3 class="text-lg font-semibold text-center mb-4">
                                                {{ $product['name'] }}
                                            </h3>

                                            {{-- Basic Product Info --}}
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm mb-4">
                                                <div>
                                                    <label class="text-sm">Product ID</label>
                                                    <input type="text" value="{{ $product['id'] }}" readonly
                                                        class="input w-full cursor-default select-none focus:outline-none focus:ring-0 focus:border-base-300">
                                                </div>
                                                <div>
                                                    <label class="text-sm">Slug</label>
                                                    <input type="text" value="{{ $product['slug'] ?? '-' }}" readonly
                                                        class="input w-full cursor-default select-none focus:outline-none focus:ring-0 focus:border-base-300">
                                                </div>
                                                <div>
                                                    <label class="text-sm">SKU</label>
                                                    <input type="text" value="{{ $product['sku'] ?? '-' }}" readonly
                                                        class="input w-full cursor-default select-none focus:outline-none focus:ring-0 focus:border-base-300">
                                                </div>
                                                <div>
                                                    <label class="text-sm">Status</label>
                                                    <input type="text"
                                                        value="{{ $product['is_active'] ? 'Live' : 'Disabled' }}" readonly
                                                        class="input w-full cursor-default select-none focus:outline-none focus:ring-0 focus:border-base-300">
                                                </div>
                                                <div>
                                                    <label class="text-sm">Product Type</label>
                                                    <input type="text"
                                                        value="{{ ucfirst($product['product_type'] ?? '-') }}" readonly
                                                        class="input w-full cursor-default select-none focus:outline-none focus:ring-0 focus:border-base-300">
                                                </div>
                                                <div>
                                                    <label class="text-sm">Stock</label>
                                                    <input type="text" value="{{ $product['stock'] ?? 'N/A' }}"
                                                        readonly
                                                        class="input w-full cursor-default select-none focus:outline-none focus:ring-0 focus:border-base-300">
                                                </div>
                                                <div>
                                                    <label class="text-sm">Stock Status</label>
                                                    <input type="text"
                                                        value="{{ $product['enable_stock'] ? 'Enabled' : 'Disabled' }}"
                                                        readonly
                                                        class="input w-full cursor-default select-none focus:outline-none focus:ring-0 focus:border-base-300">
                                                </div>
                                                <div>
                                                    <label class="text-sm">Regular Price</label>
                                                    <input type="text"
                                                        value="${{ number_format($product['regular_price'], 2) }}"
                                                        readonly
                                                        class="input w-full cursor-default select-none focus:outline-none focus:ring-0 focus-border-base-300">
                                                </div>
                                                <div>
                                                    <label class="text-sm">Sale Price</label>
                                                    <input type="text"
                                                        value="${{ number_format($product['sale_price'], 2) }}" readonly
                                                        class="input w-full cursor-default select-none focus:outline-none focus:ring-0 focus-border-base-300">
                                                </div>
                                            </div>


                                            {{-- Category & Brand --}}
                                            <p class="font-semibold mb-2">Category & Brand</p>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                                                <div>
                                                    <label class="text-sm">Category</label>
                                                    <input type="text"
                                                        value="{{ $product['category']['name'] ?? 'No Category Set' }}"
                                                        readonly
                                                        class="input w-full cursor-default select-none focus:outline-none focus:ring-0 focus:border-base-300">
                                                </div>
                                                <div>
                                                    <label class="text-sm">Brand</label>
                                                    <input type="text"
                                                        value="{{ $product['brand']['name'] ?? 'No Brand Set' }}" readonly
                                                        class="input w-full cursor-default select-none focus:outline-none focus:ring-0 focus:border-base-300">
                                                </div>
                                            </div>


                                            {{-- Shipping & Tax Classes --}}
                                            <p class="font-semibold mb-2">Shipping & Tax Classes</p>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                                                <div>
                                                    <label class="text-sm">Shipping Class</label>
                                                    <input type="text"
                                                        value="{{ $product['shipping_class']['name'] ?? 'No Shipping Class' }}"
                                                        readonly
                                                        class="input w-full cursor-default select-none focus:outline-none focus:ring-0 focus:border-base-300">
                                                </div>
                                                <div>
                                                    <label class="text-sm">Tax Class</label>
                                                    <input type="text"
                                                        value="{{ $product['tax_class']['name'] ?? 'No Tax Class' }}"
                                                        readonly
                                                        class="input w-full cursor-default select-none focus:outline-none focus:ring-0 focus:border-base-300">
                                                </div>
                                            </div>


                                            {{-- Descriptions --}}
                                            <p class="font-semibold mb-2">Descriptions</p>
                                            <div class="space-y-2 mb-3">
                                                <div>
                                                    <label class="text-sm">Short Description</label>
                                                    <textarea readonly
                                                        class="textarea textarea-bordered w-full min-h-[60px] focus:outline-none focus:ring-0 focus:border-base-300">{{ $product['short_description'] ?? '-' }}</textarea>
                                                </div>
                                                <div>
                                                    <label class="text-sm">Long Description</label>
                                                    <textarea readonly
                                                        class="textarea textarea-bordered w-full min-h-[100px] focus:outline-none focus:ring-0 focus:border-base-300">{{ $product['long_description'] ?? '-' }}</textarea>
                                                </div>
                                            </div>


                                            {{-- Images --}}
                                            <p class="font-semibold mb-2">Images</p>
                                            <div class="flex flex-wrap gap-3 mb-3">
                                                @if ($product['image'])
                                                    <div class="tooltip" data-tip="Main Image">
                                                        <img src="{{ $product['image'] }}" alt="Main Image"
                                                            class="rounded-lg w-[100px] h-[100px] object-cover">
                                                    </div>
                                                @endif

                                                @if (!empty($product['image_gallery']))
                                                    @foreach ($product['image_gallery'] as $img)
                                                        <div class="tooltip"
                                                            data-tip="{{ $img['label'] ?? 'Gallery Image' }}">
                                                            <img src="{{ $img['image'] }}"
                                                                alt="{{ $img['label'] ?? 'Gallery Image' }}"
                                                                class="rounded-lg w-[100px] h-[100px] object-cover">
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <p class="text-gray-500 italic">No gallery images available.</p>
                                                @endif
                                            </div>


                                            {{-- Payment Methods --}}
                                            <p class="font-semibold mb-2">Payment Methods</p>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                                                @forelse ($product['payment_methods'] ?? [] as $method)
                                                    <div class="bg-base-200 rounded-box p-3">
                                                        <p class="font-medium">{{ $method['name'] }}</p>
                                                        <p class="text-xs text-gray-600">
                                                            {{ $method['description'] ?? '-' }}</p>
                                                    </div>
                                                @empty
                                                    <div class="md:col-span-2 text-gray-500 italic">No payment methods
                                                        linked.</div>
                                                @endforelse
                                            </div>

                                            <div class="modal-action mt-6">
                                                <form method="dialog">
                                                    <button class="btn btn-primary w-full">Close</button>
                                                </form>
                                            </div>
                                        </div>
                                    </dialog>




                                    <dialog id="deleteModal{{ $product['id'] }}" class="modal">
                                        <div class="modal-box">
                                            <form method="dialog">
                                                <button
                                                    class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                            </form>
                                            <p class="text-lg font-semibold py-0">Confirm Delete</p>

                                            <p class="py-2 mb-0 text-sm">
                                                Are you sure you want to delete
                                                <span class="italic text-error">{{ $product['name'] }}</span> ?
                                            </p>
                                            <div class="modal-action mt-0">
                                                <form method="dialog">
                                                    <button class="btn lg:btn-md">Close</button>
                                                </form>
                                                <form method="POST"
                                                    action="{{ route('admin.dashboard.product.id.delete', ['id' => $product['id']]) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn lg:btn-md btn-error">Delete</button>
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
                        <span class="font-semibold">{{ $products->firstItem() }}</span>
                        –
                        <span class="font-semibold">{{ $products->lastItem() }}</span>
                        of
                        <span class="font-semibold">{{ $products->total() }}</span>
                        results
                    </div>

                    <div class="join">
                        @if ($products->onFirstPage())
                            <button class="join-item btn btn-sm btn-disabled">«</button>
                        @else
                            <a href="{{ $products->previousPageUrl() }}" class="join-item btn btn-sm">«</a>
                        @endif

                        <a href="{{ $products->url(1) }}"
                            class="join-item btn btn-sm {{ $products->currentPage() === 1 ? 'btn-active' : '' }}">
                            1
                        </a>

                        @php
                            $start = max(2, $products->currentPage() - 1);
                            $end = min($products->lastPage() - 1, $products->currentPage() + 1);
                        @endphp

                        @for ($i = $start; $i <= $end; $i++)
                            <a href="{{ $products->url($i) }}"
                                class="join-item btn btn-sm {{ $products->currentPage() === $i ? 'btn-active' : '' }}">
                                {{ $i }}
                            </a>
                        @endfor

                        @if ($products->lastPage() > 1)
                            <a href="{{ $products->url($products->lastPage()) }}"
                                class="join-item btn btn-sm {{ $products->currentPage() === $products->lastPage() ? 'btn-active' : '' }}">
                                {{ $products->lastPage() }}
                            </a>
                        @endif

                        @if ($products->hasMorePages())
                            <a href="{{ $products->nextPageUrl() }}" class="join-item btn btn-sm">»</a>
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
                        Toast.show('Please select at least one product', {
                            type: 'error'
                        });
                        return;
                    }
                    const modal = document.getElementById(this.current_action);
                    if (modal) modal.showModal();
                },
            });

            Alpine.store('product_search_setting', {
                showDisplayOption: false,
                showFilterOption: false,
                query: "",
                perPage: "20",
                orderBy: "desc",
                sortBy: "last_updated",

                categoryFilter: "",
                brandFilter: "",
                saleFilter: false,
                promotionFilter: false,
                pinnedFilter: false,

                is_priority_filter: @json(request('sortBy') == 'high_priority' || request('sortBy') == 'low_priority'),
                is_popularity_filter: @json(request('sortBy') == 'high_popularity' || request('sortBy') == 'low_popularity'),
                is_last_updated_filter: @json(request('sortBy') == 'last_updated'),
                is_last_created_filter: @json(request('sortBy') == 'last_created'),

                showIDColumn: false,
                showPriorityColumn: false,
                showViewColumn: false,
                showUpdatedTimeColumn: false,
                showCreatedTimeColumn: false,
                showPaymentMethodColumn: false,
                showShippingClassColumn: false,

                init() {
                    const savedSetting = JSON.parse(localStorage.getItem('product_search_setting') ?? "{}");

                    this.showDisplayOption = savedSetting.showDisplayOption ?? false;
                    this.showFilterOption = savedSetting.showFilterOption ?? false;
                    this.query = savedSetting.query ?? "";
                    this.perPage = savedSetting.perPage ?? "20";
                    this.orderBy = savedSetting.orderBy ?? "desc";
                    this.sortBy = savedSetting.sortBy ?? "last_updated";

                    this.categoryFilter = savedSetting.categoryFilter ?? "";
                    this.brandFilter = savedSetting.brandFilter ?? "";
                    this.saleFilter = savedSetting.saleFilter ?? false;
                    this.promotionFilter = savedSetting.promotionFilter ?? false;
                    this.pinnedFilter = savedSetting.pinnedFilter ?? false;

                    this.showIDColumn = savedSetting.showIDColumn ?? false;
                    this.showPriorityColumn = savedSetting.showPriorityColumn ?? false;
                    this.showViewColumn = savedSetting.showViewColumn ?? false;
                    this.showUpdatedTimeColumn = savedSetting.showUpdatedTimeColumn ?? false;
                    this.showCreatedTimeColumn = savedSetting.showCreatedTimeColumn ?? false;
                    this.showPaymentMethodColumn = savedSetting.showPaymentMethodColumn ?? false;
                    this.showShippingClassColumn = savedSetting.showShippingClassColumn ?? false;
                },

                save() {
                    const data = {
                        showDisplayOption: this.showDisplayOption,
                        showFilterOption: this.showFilterOption,
                        query: this.query,
                        perPage: this.perPage,
                        orderBy: this.orderBy,
                        sortBy: this.sortBy,
                        categoryFilter: this.categoryFilter,
                        brandFilter: this.brandFilter,
                        saleFilter: this.saleFilter,
                        promotionFilter: this.promotionFilter,
                        pinnedFilter: this.pinnedFilter,
                        showIDColumn: this.showIDColumn,
                        showPriorityColumn: this.showPriorityColumn,
                        showViewColumn: this.showViewColumn,
                        showUpdatedTimeColumn: this.showUpdatedTimeColumn,
                        showCreatedTimeColumn: this.showCreatedTimeColumn,
                        showPaymentMethodColumn: this.showPaymentMethodColumn,
                        showShippingClassColumn: this.showShippingClassColumn,

                    };
                    localStorage.setItem('product_search_setting', JSON.stringify(data));
                },

                resetFilter() {
                    this.categoryFilter = "";
                    this.brandFilter = "";
                    this.saleFilter = false;
                    this.promotionFilter = false;
                    this.pinnedFilter = false;
                    this.save();
                }
            });

            Alpine.store('product_search_setting').init();
        });
    </script>
@endpush
