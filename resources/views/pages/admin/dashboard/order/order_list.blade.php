@php
    $site_currency = getParsedTemplate('site_currency');
@endphp

@extends('layouts.admin.admin_dashboard')
@section('admin_dashboard_content')
    <div class="p-3 lg:p-5 min-h-screen">
        <p class="lg:text-lg font-semibold">Orders</p>

        <div class="mt-3 flex xl:flex-row flex-col justify-between gap-2">
            <div class="flex flex-row gap-2 flex-wrap" x-data>
                <div class="join join-horizontal">
                    <select class="select select-sm join-item" x-model="$store.bulk.current_action">
                        <option value="">Bulk Actions</option>
                        <option value="bulk_archive_selected">Archive Selected</option>
                        <option value="bulk_archive_all">Archive All</option>
                        <option value="bulk_unarchive_selected">Unarchive Selected</option>
                        <option value="bulk_unarchive_all">Unarchive All</option>
                        <option value="bulk_delete_selected">Delete Selected</option>
                        <option value="bulk_delete_all">Delete All</option>
                    </select>
                    <button class="join-item btn btn-sm" @click="$store.bulk.commit()">Commit</button>
                </div>
                {{-- <button class="btn btn-sm shadow-none" onclick="create_brand_modal.showModal()">Add Brand</button> --}}
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
                        <span class="italic text-error">Selected Orders</span>?
                    </p>

                    <template x-if="$store.bulk.hasCandidates">
                        <ul class="text-sm pl-10 list-decimal max-h-24 overflow-y-auto bg-base-200 p-3">
                            <template x-for="id in $store.bulk.candidates" :key="id">
                                <li x-text="'Order ID: ' + id"></li>
                            </template>
                        </ul>
                    </template>

                    <div class="mt-3 modal-action">
                        <form method="dialog"><button class="btn" :disabled="loading">Cancel</button></form>

                        <form method="POST" action="{{ route('admin.dashboard.order.bulk.delete-selected') }}"
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
                        <span class="text-error">All Orders</span>?
                    </p>

                    <div class="modal-action">
                        <form method="dialog"><button class="btn" :disabled="loading">Cancel</button></form>

                        <form method="POST" action="{{ route('admin.dashboard.order.bulk.delete-all') }}"
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

            {{-- bulk archive selected modal --}}
            <dialog id="bulk_archive_selected" class="modal" x-data="{ loading: false }">
                <div class="modal-box relative">
                    <form method="dialog">
                        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                    </form>

                    <p class="text-lg font-semibold py-0">Confirm Archive</p>
                    <p class="py-2 mb-0 text-sm">
                        Are you sure you want to archive
                        <span class="italic">Selected Orders</span>?
                    </p>

                    <template x-if="$store.bulk.hasCandidates">
                        <ul class="text-sm pl-10 list-decimal max-h-24 overflow-y-auto bg-base-200 p-3">
                            <template x-for="id in $store.bulk.candidates" :key="id">
                                <li x-text="'Order ID: ' + id"></li>
                            </template>
                        </ul>
                    </template>

                    <div class="mt-3 modal-action">
                        <form method="dialog"><button class="btn" :disabled="loading">Cancel</button></form>

                        <form method="POST" action="{{ route('admin.dashboard.order.bulk.archive-selected') }}"
                            @submit="loading = true">
                            @csrf
                            <template x-for="id in $store.bulk.candidates" :key="id">
                                <input type="hidden" name="ids[]" :value="id">
                            </template>
                            <button type="submit" class="btn btn-primary flex items-center gap-2" :disabled="loading">
                                <span x-show="!loading">Archive Candidates</span>
                                <span x-show="loading" class="loading loading-spinner loading-xs"></span>
                                <span x-show="loading">Archiving...</span>
                            </button>
                        </form>
                    </div>
                </div>
            </dialog>

            {{-- bulk archive all modal --}}
            <dialog id="bulk_archive_all" class="modal" x-data="{ loading: false }">
                <div class="modal-box relative">
                    <form method="dialog">
                        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                    </form>

                    <p class="text-lg font-semibold">Confirm Archive</p>
                    <p class="text-sm mb-4">
                        Are you sure you want to archive
                        <span>All Orders</span>?
                    </p>

                    <div class="modal-action">
                        <form method="dialog"><button class="btn" :disabled="loading">Cancel</button></form>

                        <form method="POST" action="{{ route('admin.dashboard.order.bulk.archive-all') }}"
                            @submit="loading = true">
                            @csrf
                            <template x-for="id in $store.bulk.candidates" :key="id">
                                <input type="hidden" name="ids[]" :value="id">
                            </template>
                            <button type="submit" class="btn btn-primary flex items-center gap-2"
                                :disabled="loading">
                                <span x-show="!loading">Archive</span>
                                <span x-show="loading" class="loading loading-spinner loading-xs"></span>
                                <span x-show="loading">Archiving...</span>
                            </button>
                        </form>
                    </div>
                </div>
            </dialog>

            {{-- bulk unarchive selected modal --}}
            <dialog id="bulk_unarchive_selected" class="modal" x-data="{ loading: false }">
                <div class="modal-box relative">
                    <form method="dialog">
                        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                    </form>

                    <p class="text-lg font-semibold py-0">Confirm Unarchive</p>
                    <p class="py-2 mb-0 text-sm">
                        Are you sure you want to unarchive
                        <span class="italic">Selected Orders</span>?
                    </p>

                    <template x-if="$store.bulk.hasCandidates">
                        <ul class="text-sm pl-10 list-decimal max-h-24 overflow-y-auto bg-base-200 p-3">
                            <template x-for="id in $store.bulk.candidates" :key="id">
                                <li x-text="'Order ID: ' + id"></li>
                            </template>
                        </ul>
                    </template>

                    <div class="mt-3 modal-action">
                        <form method="dialog"><button class="btn" :disabled="loading">Cancel</button></form>

                        <form method="POST" action="{{ route('admin.dashboard.order.bulk.unarchive-selected') }}"
                            @submit="loading = true">
                            @csrf
                            <template x-for="id in $store.bulk.candidates" :key="id">
                                <input type="hidden" name="ids[]" :value="id">
                            </template>
                            <button type="submit" class="btn btn-primary flex items-center gap-2"
                                :disabled="loading">
                                <span x-show="!loading">Unarchive Candidates</span>
                                <span x-show="loading" class="loading loading-spinner loading-xs"></span>
                                <span x-show="loading">Unarchiving...</span>
                            </button>
                        </form>
                    </div>
                </div>
            </dialog>

            {{-- bulk unarchive all modal --}}
            <dialog id="bulk_unarchive_all" class="modal" x-data="{ loading: false }">
                <div class="modal-box relative">
                    <form method="dialog">
                        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                    </form>

                    <p class="text-lg font-semibold">Confirm Unarchive</p>
                    <p class="text-sm mb-4">
                        Are you sure you want to unarchive
                        <span>All Orders</span>?
                    </p>

                    <div class="modal-action">
                        <form method="dialog"><button class="btn" :disabled="loading">Cancel</button></form>

                        <form method="POST" action="{{ route('admin.dashboard.order.bulk.unarchive-all') }}"
                            @submit="loading = true">
                            @csrf
                            <template x-for="id in $store.bulk.candidates" :key="id">
                                <input type="hidden" name="ids[]" :value="id">
                            </template>
                            <button type="submit" class="btn btn-primary flex items-center gap-2"
                                :disabled="loading">
                                <span x-show="!loading">Unarchive</span>
                                <span x-show="loading" class="loading loading-spinner loading-xs"></span>
                                <span x-show="loading">Unarchiving...</span>
                            </button>
                        </form>
                    </div>
                </div>
            </dialog>

            {{-- Filtering Section --}}
            <div class="flex flex-row flex-wrap justify-start xl:justify-end gap-2">
                {{-- product searching --}}
                <form id="queryForm" method="GET" action="{{ request()->url() }}" class="join join-horizontal">
                    <template x-data x-if="$store.order_search_setting.sortBy">
                        <input type="hidden" name="sortBy" :value="$store.order_search_setting.sortBy">
                    </template>

                    <template x-data x-if="$store.order_search_setting.perPage">
                        <input type="hidden" x-data name="perPage" :value="$store.order_search_setting.perPage">
                    </template>

                    <template x-data x-if="$store.order_search_setting.orderBy">
                        <input type="hidden" x-data name="orderBy" :value="$store.order_search_setting.orderBy">
                    </template>

                    <template x-data
                        x-if="$store.order_search_setting.date_limit && $store.order_search_setting.date_limit.length > 0">
                        <input type="hidden" x-data name="date_limit" :value="$store.order_search_setting.date_limit">
                    </template>

                    <template x-data
                        x-if="$store.order_search_setting.orderStatusFilter && $store.order_search_setting.orderStatusFilter.length > 0">
                        <input type="hidden" x-data name="order_status"
                            :value="$store.order_search_setting.orderStatusFilter">
                    </template>

                    <template x-data x-if="$store.order_search_setting.archivedFilter">
                        <input type="hidden" x-data name="is_archived"
                            :value="$store.order_search_setting.archivedFilter">
                    </template>

                    <input type="text" x-data x-cloak class="join-item input input-sm rounded-l-box" name="query"
                        :value="$store.order_search_setting.query"
                        @change="$store.order_search_setting.query = $event.target.value; $store.order_search_setting.save(); $el.form.submit()">

                    <button class="join-item btn btn-sm">Search</button>
                </form>

                {{-- day limiting --}}
                <form method="GET" action="{{ request()->url() }}">
                    <template x-data
                        x-if="$store.order_search_setting.query && $store.order_search_setting.query.length > 0">
                        <input type="hidden" name="query" :value="$store.order_search_setting.query">
                    </template>

                    <template x-data x-if="$store.order_search_setting.sortBy">
                        <input type="hidden" x-data name="sortBy" :value="$store.order_search_setting.sortBy">
                    </template>

                    <template x-data x-if="$store.order_search_setting.orderBy">
                        <input type="hidden" x-data name="orderBy" :value="$store.order_search_setting.orderBy">
                    </template>

                    <template x-data x-if="$store.order_search_setting.perPage">
                        <input type="hidden" x-data name="perPage" :value="$store.order_search_setting.perPage">
                    </template>

                    <template x-data
                        x-if="$store.order_search_setting.orderStatusFilter && $store.order_search_setting.orderStatusFilter.length > 0">
                        <input type="hidden" x-data name="order_status"
                            :value="$store.order_search_setting.orderStatusFilter">
                    </template>

                    <template x-data x-if="$store.order_search_setting.archivedFilter">
                        <input type="hidden" x-data name="is_archived"
                            :value="$store.order_search_setting.archivedFilter">
                    </template>

                    <select name="date_limit" x-data x-cloak class="select select-sm w-fit shrink-0" x-data
                        x-model="$store.order_search_setting.date_limit"
                        @change="$store.order_search_setting.date_limit = $event.target.value; $store.order_search_setting.save(); $el.form.submit()">
                        <option value="">Everyday</option>
                        <option value="last_3_day">Last 3 Days</option>
                        <option value="last_5_day">Last 5 Days</option>
                        <option value="last_7_day">Last 7 Days</option>
                        <option value="last_10_day">Last 10 Days</option>
                        <option value="last_20_day">Last 20 Days</option>
                        <option value="last_30_day">Last 30 Days</option>
                        <option value="last_3_month">Last 3 Months</option>
                        <option value="last_5_month">Last 5 Months</option>
                        <option value="last_10_month">Last 10 Months</option>
                        <option value="last_1_year">Last 1 Year</option>
                        <option value="last_2_year">Last 2 Year</option>
                        <option value="last_5_year">Last 5 Year</option>
                        <option value="last_10_year">Last 10 Year</option>
                    </select>
                </form>

                {{-- page limiting --}}
                <form method="GET" action="{{ request()->url() }}">
                    <template x-data
                        x-if="$store.order_search_setting.query && $store.order_search_setting.query.length > 0">
                        <input type="hidden" name="query" :value="$store.order_search_setting.query">
                    </template>

                    <template x-data x-if="$store.order_search_setting.sortBy">
                        <input type="hidden" x-data name="sortBy" :value="$store.order_search_setting.sortBy">
                    </template>

                    <template x-data x-if="$store.order_search_setting.orderBy">
                        <input type="hidden" x-data name="orderBy" :value="$store.order_search_setting.orderBy">
                    </template>

                    <template x-data
                        x-if="$store.order_search_setting.date_limit && $store.order_search_setting.date_limit.length > 0">
                        <input type="hidden" x-data name="date_limit" :value="$store.order_search_setting.date_limit">
                    </template>

                    <template x-data
                        x-if="$store.order_search_setting.orderStatusFilter && $store.order_search_setting.orderStatusFilter.length > 0">
                        <input type="hidden" x-data name="order_status"
                            :value="$store.order_search_setting.orderStatusFilter">
                    </template>

                    <template x-data x-if="$store.order_search_setting.archivedFilter">
                        <input type="hidden" x-data name="is_archived"
                            :value="$store.order_search_setting.archivedFilter">
                    </template>

                    <select name="perPage" x-data x-cloak class="select select-sm w-fit shrink-0" x-data
                        x-model="$store.order_search_setting.perPage"
                        @change="$store.order_search_setting.perPage = $event.target.value; $store.order_search_setting.save(); $el.form.submit()">
                        <option value="5">Show 5</option>
                        <option value="10">Show 10</option>
                        <option value="20">Show 20</option>
                        <option value="50">Show 50</option>
                    </select>
                </form>

                {{-- ascending & descending --}}
                <form method="GET" action="{{ request()->url() }}">
                    <template x-data
                        x-if="$store.order_search_setting.query && $store.order_search_setting.query.length > 0">
                        <input type="hidden" name="query" :value="$store.order_search_setting.query">
                    </template>

                    <template x-data x-if="$store.order_search_setting.perPage">
                        <input type="hidden" x-data name="perPage" :value="$store.order_search_setting.perPage">
                    </template>

                    <template x-data x-if="$store.order_search_setting.sortBy">
                        <input type="hidden" x-data name="sortBy" :value="$store.order_search_setting.sortBy">
                    </template>

                    <template x-data
                        x-if="$store.order_search_setting.date_limit && $store.order_search_setting.date_limit.length > 0">
                        <input type="hidden" x-data name="date_limit" :value="$store.order_search_setting.date_limit">
                    </template>

                    <template x-data
                        x-if="$store.order_search_setting.orderStatusFilter && $store.order_search_setting.orderStatusFilter.length > 0">
                        <input type="hidden" x-data name="order_status"
                            :value="$store.order_search_setting.orderStatusFilter">
                    </template>

                    <template x-data x-if="$store.order_search_setting.archivedFilter">
                        <input type="hidden" x-data name="is_archived"
                            :value="$store.order_search_setting.archivedFilter">
                    </template>

                    <select name="orderBy" x-data x-cloak x-model="$store.order_search_setting.orderBy"
                        class="select select-sm w-fit shrink-0" x-data :value="$store.order_search_setting.orderBy"
                        @change="$store.order_search_setting.orderBy = $event.target.value; $store.order_search_setting.save() ; $el.form.submit()">
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
                        x-if="$store.order_search_setting.query && $store.order_search_setting.query.length > 0">
                        <input type="hidden" name="query" :value="$store.order_search_setting.query">
                    </template>

                    <template x-data x-if="$store.order_search_setting.perPage">
                        <input type="hidden" x-data name="perPage" :value="$store.order_search_setting.perPage">
                    </template>

                    <template x-data x-if="$store.order_search_setting.orderBy">
                        <input type="hidden" x-data name="orderBy" :value="$store.order_search_setting.orderBy">
                    </template>

                    <template x-data
                        x-if="$store.order_search_setting.date_limit && $store.order_search_setting.date_limit.length > 0">
                        <input type="hidden" x-data name="date_limit" :value="$store.order_search_setting.date_limit">
                    </template>

                    <template x-data
                        x-if="$store.order_search_setting.orderStatusFilter && $store.order_search_setting.orderStatusFilter.length > 0">
                        <input type="hidden" x-data name="order_status"
                            :value="$store.order_search_setting.orderStatusFilter">
                    </template>

                    <template x-data x-if="$store.order_search_setting.archivedFilter">
                        <input type="hidden" x-data name="is_archived"
                            :value="$store.order_search_setting.archivedFilter">
                    </template>

                    <select name="sortBy" class="select select-sm w-fit shrink-0" x-data x-cloak
                        :value="$store.order_search_setting.sortBy"
                        @change="$store.order_search_setting.sortBy = $event.target.value; $store.order_search_setting.save(); $el.form.submit()">
                        <option value="last_updated">Sort By Last Updated
                        </option>
                        <option value="last_created">Sort By Last Created
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
                                @click="$store.order_search_setting.showFilterOption = !$store.order_search_setting.showFilterOption;$store.order_search_setting.save()">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
                                </svg>
                                <span
                                    x-text="$store.order_search_setting.showFilterOption ? 'Hide Filter' : 'Filter Option'"></span>
                            </button>
                        </li>
                        <li>
                            <button x-data x-transition x-cloak
                                @click="$store.order_search_setting.showDisplayOption = !$store.order_search_setting.showDisplayOption;$store.order_search_setting.save()">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 4.5v15m6-15v15m-10.875 0h15.75c.621 0 1.125-.504 1.125-1.125V5.625c0-.621-.504-1.125-1.125-1.125H4.125C3.504 4.5 3 5.004 3 5.625v12.75c0 .621.504 1.125 1.125 1.125Z" />
                                </svg>
                                <span
                                    x-text="$store.order_search_setting.showDisplayOption ? 'Hide Display' : 'Display Option'"></span>
                            </button>
                        </li>
                        {{-- <li>
                            <button x-data x-transition x-cloak
                                @click="$store.order_search_setting.archivedFilter = !$store.order_search_setting.archivedFilter; $store.order_search_setting.save(); document.getElementById('queryForm').requestSubmit();">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-5 text-amber-500">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                                </svg>
                                <span
                                    x-text="$store.order_search_setting.archivedFilter ? 'Show Active' : 'Show Archived'"></span>
                            </button>
                        </li> --}}
                        <li>
                            <a href="{{ route('admin.dashboard.order.export-order.get') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-5 text-success">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 0 1-1.125-1.125M3.375 19.5h7.5c.621 0 1.125-.504 1.125-1.125m-9.75 0V5.625m0 12.75v-1.5c0-.621.504-1.125 1.125-1.125m18.375 2.625V5.625m0 12.75c0 .621-.504 1.125-1.125 1.125m1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125m0 3.75h-7.5A1.125 1.125 0 0 1 12 18.375m9.75-12.75c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125m19.5 0v1.5c0 .621-.504 1.125-1.125 1.125M2.25 5.625v1.5c0 .621.504 1.125 1.125 1.125m0 0h17.25m-17.25 0h7.5c.621 0 1.125.504 1.125 1.125M3.375 8.25c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125m17.25-3.75h-7.5c-.621 0-1.125.504-1.125 1.125m8.625-1.125c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h7.5m-7.5 0c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125M12 10.875v-1.5m0 1.5c0 .621-.504 1.125-1.125 1.125M12 10.875c0 .621.504 1.125 1.125 1.125m-2.25 0c.621 0 1.125.504 1.125 1.125M13.125 12h7.5m-7.5 0c-.621 0-1.125.504-1.125 1.125M20.625 12c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h7.5M12 14.625v-1.5m0 1.5c0 .621-.504 1.125-1.125 1.125M12 14.625c0 .621.504 1.125 1.125 1.125m-2.25 0c.621 0 1.125.504 1.125 1.125m0 1.5v-1.5m0 0c0-.621.504-1.125 1.125-1.125m0 0h7.5" />
                                </svg>
                                Export Orders
                            </a>
                        </li>
                    </ul>
                </div>

            </div>
        </div>

        {{-- filtering --}}
        <div class="mt-3 p-3 border border-base-300 rounded-md flex flex-col gap-2" x-cloak x-transition x-data
            x-show="$store.order_search_setting.showFilterOption">
            <p class="text-xs">Filter Options</p>
            <div class="grid grid-cols-2 md:grid-cols-4 xl:grid-cols-6 gap-3">
                <div class="flex flex-col gap-1">
                    <select x-data class="select select-sm text-xs" name="order_status"
                        :value="$store.order_search_setting.orderStatusFilter"
                        @change="$store.order_search_setting.orderStatusFilter = $event.target.value; $store.order_search_setting.save();">
                        <div class="max-h-[200px] overflow-y-auto">
                            <option value="" :selected="!$store.order_search_setting.orderStatusFilter">Filter
                                Order Status</option>
                            <option value="pending">Pending</option>
                            <option value="processing">Processing</option>
                            <option value="shipped">Shipped</option>
                            <option value="delivered">Delivered</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                            <option value="refunded">Refunded</option>
                        </div>
                    </select>
                </div>
                <div class="flex flex-col gap-1">
                    <select x-data class="select select-sm text-xs" name="is_archived"
                        :value="$store.order_search_setting.archivedFilter ? 'true' : 'false'"
                        @change="$store.order_search_setting.archivedFilter = $event.target.value === 'true' ? true : false; $store.order_search_setting.save()">
                        <option value="false">Active Order</option>
                        <option value="true">Archived Order</option>
                    </select>
                </div>
            </div>
            <div class="flex flex-row gap-2">
                <button class="btn btn-sm" x-data
                    @click="document.getElementById('queryForm').requestSubmit();">Search</button>
                <button class="btn btn-sm" x-data @click="$store.order_search_setting.resetFilter()">Reset</button>
            </div>
        </div>

        {{-- column displaying --}}
        <div class="mt-3 p-3 border border-base-300 rounded-md flex flex-col gap-2" x-cloak x-transition x-data
            x-show="$store.order_search_setting.showDisplayOption">
            <p class="text-xs">Display Options</p>
            <div class="flex sm:flex-row flex-col flex-wrap gap-3">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" class="checkbox checkbox-xs rounded-sm" x-data
                        :checked="$store.order_search_setting.showIDColumn"
                        @change="$store.order_search_setting.showIDColumn = !$store.order_search_setting.showIDColumn; $store.order_search_setting.save()">
                    <span class="text-xs">Show ID</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" class="checkbox checkbox-xs rounded-sm" x-data
                        :checked="$store.order_search_setting.showUpdatedTimeColumn || $store.order_search_setting
                            .is_last_updated_filter"
                        @change="$store.order_search_setting.showUpdatedTimeColumn = !$store.order_search_setting.showUpdatedTimeColumn; $store.order_search_setting.save()">
                    <span class="text-xs">Show Updated Time</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" class="checkbox checkbox-xs rounded-sm" x-data
                        :checked="$store.order_search_setting.showCreatedTimeColumn || $store.order_search_setting
                            .is_last_created_filter"
                        @change="$store.order_search_setting.showCreatedTimeColumn = !$store.order_search_setting.showCreatedTimeColumn; $store.order_search_setting.save()">
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
                            <th class="w-[50px]">No.</th>
                            <th x-cloak x-data x-show="$store.order_search_setting.showIDColumn" class="w-[50px]">ID
                            </th>
                            <th>Order Number</th>
                            <th>Order Status</th>
                            <th>Stock Inventory</th>
                            <th>Grand Total</th>
                            <th x-cloak x-data
                                x-show="$store.order_search_setting.showUpdatedTimeColumn || $store.order_search_setting.is_last_updated_filter">
                                Updated At</th>
                            <th x-cloak x-data
                                x-show="$store.order_search_setting.showCreatedTimeColumn || $store.order_search_setting.is_last_created_filter">
                                Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            <tr>
                                <td>
                                    <input type="checkbox" class="checkbox checkbox-xs rounded-sm row-checkbox"
                                        data-id="{{ $order['id'] }}" x-data
                                        :checked="$store.bulk.candidates.includes({{ $order['id'] }})"
                                        @change="$store.bulk.toggleCandidate({{ $order['id'] }})">
                                </td>
                                <td>
                                    {{ $loop->iteration + ($orders->currentPage() - 1) * $orders->perPage() }}.
                                </td>
                                <td x-cloak x-data x-show="$store.order_search_setting.showIDColumn">
                                    {{ $order['id'] }}
                                </td>
                                <td>
                                    <a href="{{ route('admin.dashboard.order.id.get', ['id' => $order['id']]) }}"
                                        class="cursor-pointer hover:underline">
                                        {{ $order['order_number'] }}
                                    </a>
                                </td>
                                <td>
                                    @php
                                        $color = match ($order['status']) {
                                            'pending' => 'badge-warning',
                                            'processing' => 'badge-warning',
                                            'shipped' => 'badge-info',
                                            'delivered' => 'badge-info',
                                            'completed' => 'badge-success',
                                            'refunded' => 'badge-error',
                                            'cancelled' => 'badge-error',
                                            default => 'badge-ghost',
                                        };
                                    @endphp
                                    <div
                                        class="badge badge-sm {{ $color }} border border-base-300 capitalize text-xs">
                                        {{ $order['status'] }}
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $color = match ($order['stock_consumed']) {
                                            false => 'badge-info',
                                            true => 'badge-ghost',
                                            default => 'badge-ghost',
                                        };
                                    @endphp
                                    <div
                                        class="badge badge-sm {{ $color }} border border-base-300 capitalize text-xs">
                                        {{ $order['stock_consumed'] ? 'Consumed' : 'Not Consumed' }}
                                    </div>
                                </td>

                                <td>{{ number_format($order['grand_total'], 2) }} {{ $site_currency }}</td>

                                <td x-cloak x-data
                                    x-show="$store.order_search_setting.showUpdatedTimeColumn || $store.order_search_setting.is_last_updated_filter">
                                    {{ $order['updated_at'] ? \Carbon\Carbon::parse($order['updated_at'])->format('Y-m-d h:i A') : '-' }}
                                </td>
                                <td x-cloak x-data
                                    x-show="$store.order_search_setting.showCreatedTimeColumn || $store.order_search_setting.is_last_created_filter">
                                    {{ $order['created_at'] ? \Carbon\Carbon::parse($order['created_at'])->format('Y-m-d h:i A') : '-' }}
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
                                                    onclick="document.getElementById('detail_modal_{{ $order['id'] }}').showModal()">
                                                    View Details
                                                </button>
                                            </li>
                                            @if ($order['archived'] == false)
                                                <li>
                                                    <button type="button"
                                                        onclick="document.getElementById('archive_modal_{{ $order['id'] }}').showModal()">
                                                        Archive
                                                    </button>
                                                </li>
                                            @else
                                                <li>
                                                    <button type="button"
                                                        onclick="document.getElementById('unarchive_modal_{{ $order['id'] }}').showModal()">
                                                        Unarchive
                                                    </button>
                                                </li>
                                            @endif
                                            <li>
                                                <button type="button" class="text-error"
                                                    onclick="document.getElementById('delete_modal_{{ $order['id'] }}').showModal()">
                                                    Delete
                                                </button>
                                            </li>
                                        </ul>
                                    </div>

                                    <dialog id="detail_modal_{{ $order['id'] }}" class="modal">
                                        <div class="modal-box max-w-2xl max-h-[85vh] overflow-y-auto">
                                            <form method="dialog">
                                                <button
                                                    class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                            </form>

                                            <h3 class="text-lg font-semibold text-center mb-3">
                                                Order #{{ $order['order_number'] }}
                                            </h3>

                                            {{-- Order Basic Info --}}
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm mb-4">
                                                <div>
                                                    <label class="text-sm">Order ID</label>
                                                    <input type="text" value="{{ $order['id'] }}" readonly
                                                        class="input w-full cursor-default select-none focus:outline-none focus:ring-0 focus:border-base-300">
                                                </div>
                                                <div>
                                                    <label class="text-sm">Date</label>
                                                    <input type="text"
                                                        value="{{ $order['created_at'] ? \Carbon\Carbon::parse($order['created_at'])->format('Y-m-d h:i A') : '' }}"
                                                        readonly
                                                        class="input w-full cursor-default select-none focus:outline-none focus:ring-0 focus:border-base-300">
                                                </div>
                                                <div>
                                                    <label class="text-sm">Status</label>
                                                    <input type="text" value="{{ ucfirst($order['status']) }}"
                                                        readonly
                                                        class="input w-full cursor-default select-none focus:outline-none focus:ring-0 focus:border-base-300">
                                                </div>
                                                <div>
                                                    <label class="text-sm">Subtotal</label>
                                                    <input type="text"
                                                        value="{{ number_format($order['subtotal'], 2) }} {{ $site_currency }}"
                                                        readonly
                                                        class="input w-full cursor-default select-none focus:outline-none focus:ring-0 focus:border-base-300">
                                                </div>
                                                <div>
                                                    <label class="text-sm">Discount</label>
                                                    <input type="text"
                                                        value="- {{ number_format($order['discount_total'], 2) }} {{ $site_currency }}"
                                                        readonly
                                                        class="input w-full cursor-default select-none focus:outline-none focus:ring-0 focus:border-base-300">
                                                </div>
                                                <div>
                                                    <label class="text-sm">Coupon Code</label>
                                                    <input type="text" value="{{ $order['coupon_code'] ?? 'None' }}"
                                                        readonly
                                                        class="input w-full cursor-default select-none focus:outline-none focus:ring-0 focus:border-base-300">
                                                </div>
                                                <div>
                                                    <label class="text-sm">Tax</label>
                                                    <input type="text"
                                                        value="+ {{ number_format($order['tax_total'], 2) }} {{ $site_currency }}"
                                                        readonly
                                                        class="input w-full cursor-default select-none focus:outline-none focus:ring-0 focus:border-base-300">
                                                </div>
                                                <div>
                                                    <label class="text-sm">Shipping</label>
                                                    <input type="text"
                                                        value="+ {{ number_format($order['shipping_total'], 2) }} {{ $site_currency }}"
                                                        readonly
                                                        class="input w-full cursor-default select-none focus:outline-none focus:ring-0 focus:border-base-300">
                                                </div>
                                                <div>
                                                    <label class="text-sm">Grand Total</label>
                                                    <input type="text"
                                                        value="{{ number_format($order['grand_total'], 2) }} {{ $site_currency }}"
                                                        readonly
                                                        class="input w-full font-semibold cursor-default select-none focus:outline-none focus:ring-0 focus:border-base-300">
                                                </div>
                                            </div>


                                            {{-- Ordered Products Table --}}
                                            <p class="font-semibold mb-2">Ordered Products</p>
                                            <div class="overflow-x-auto mb-3">
                                                <table class="table table-sm border border-base-300">
                                                    <thead>
                                                        <tr>
                                                            <th>Name</th>
                                                            <th>Qty</th>
                                                            <th>SKU</th>
                                                            <th>Type</th>
                                                            <th>Unit Price</th>
                                                            <th>Subtotal</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($order['products'] ?? [] as $item)
                                                            <tr>
                                                                <td>{{ $item['name'] }}</td>
                                                                <td>{{ $item['quantity'] }}</td>
                                                                <td>{{ $item['sku'] }}</td>
                                                                <td>{{ $item['variant_id'] ? 'Variant Product' : 'Simple Product' }}
                                                                </td>
                                                                <td>{{ number_format($item['unit_price'], 2) }}
                                                                    {{ $site_currency }}</td>
                                                                <td>{{ number_format($item['subtotal'], 2) }}
                                                                    {{ $site_currency }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>

                                            <div class="flex flex-col gap-4 mb-3">
                                                {{-- Shipping --}}
                                                <div class="bg-base-200 rounded-box p-3">
                                                    <p class="font-semibold mb-1">Shipping Address</p>
                                                    @php $s = $order['shipping_address'] ?? []; @endphp
                                                    <input type="text" value="{{ $s['recipient_name'] ?? '-' }}"
                                                        readonly
                                                        class="input w-full mb-1 cursor-default select-none focus:outline-none focus:ring-0 focus:border-base-300">
                                                    <input type="text" value="{{ $s['street_address'] ?? '-' }}"
                                                        readonly
                                                        class="input w-full mb-1 cursor-default select-none focus:outline-none focus:ring-0 focus:border-base-300">
                                                    <input type="text"
                                                        value="{{ $s['city'] ?? '' }} {{ $s['state'] ?? '' }}" readonly
                                                        class="input w-full mb-1 cursor-default select-none focus:outline-none focus:ring-0 focus:border-base-300">
                                                    <input type="text" value="{{ $s['postal_code'] ?? '' }}" readonly
                                                        class="input w-full mb-1 cursor-default select-none focus:outline-none focus:ring-0 focus:border-base-300">
                                                    <input type="text" value="{{ $s['country'] ?? '' }}" readonly
                                                        class="input w-full mb-1 cursor-default select-none focus:outline-none focus:ring-0 focus:border-base-300">
                                                    <input type="text" value="{{ $s['phone'] ?? '' }}" readonly
                                                        class="input w-full text-xs cursor-default select-none focus:outline-none focus:ring-0 focus:border-base-300">
                                                </div>

                                                {{-- Billing --}}
                                                <div class="bg-base-200 rounded-box p-3">
                                                    <p class="font-semibold mb-1">Billing Address</p>
                                                    @php $b = $order['billing_address'] ?? []; @endphp
                                                    <input type="text" value="{{ $b['recipient_name'] ?? '-' }}"
                                                        readonly
                                                        class="input w-full mb-1 cursor-default select-none focus:outline-none focus:ring-0 focus:border-base-300">
                                                    <input type="text" value="{{ $b['street_address'] ?? '-' }}"
                                                        readonly
                                                        class="input w-full mb-1 cursor-default select-none focus:outline-none focus:ring-0 focus:border-base-300">
                                                    <input type="text"
                                                        value="{{ $b['city'] ?? '' }} {{ $b['state'] ?? '' }}" readonly
                                                        class="input w-full mb-1 cursor-default select-none focus:outline-none focus:ring-0 focus:border-base-300">
                                                    <input type="text" value="{{ $b['postal_code'] ?? '' }}" readonly
                                                        class="input w-full mb-1 cursor-default select-none focus:outline-none focus:ring-0 focus:border-base-300">
                                                    <input type="text" value="{{ $b['country'] ?? '' }}" readonly
                                                        class="input w-full mb-1 cursor-default select-none focus:outline-none focus:ring-0 focus:border-base-300">
                                                    <input type="text" value="{{ $b['phone'] ?? '' }}" readonly
                                                        class="input w-full text-xs cursor-default select-none focus:outline-none focus:ring-0 focus:border-base-300">
                                                </div>
                                            </div>


                                            {{-- Payment Methods --}}
                                            <p class="font-semibold mb-2">Payment Method</p>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div>
                                                    <label class="text-sm">Name</label>
                                                    <input type="text" value="{{ $order['payment_method']['name'] }}"
                                                        readonly
                                                        class="input w-full cursor-default select-none mb-1 focus:outline-none focus:ring-0 focus:border-base-300">
                                                </div>
                                                <div>
                                                    <label class="text-sm">Description</label>
                                                    <input type="text"
                                                        value="{{ $order['payment_method']['description'] ?? '-' }}"
                                                        readonly
                                                        class="input w-full cursor-default select-none mb-1 focus:outline-none focus:ring-0 focus:border-base-300">
                                                </div>
                                                @if (empty($order['payment_method']))
                                                    <div class="md:col-span-2 text-gray-500 italic">No payment method
                                                        available.</div>
                                                @endif
                                            </div>

                                            {{-- Modal Action --}}
                                            <div class="modal-action mt-6">
                                                <form method="dialog">
                                                    <button class="btn btn-primary w-full">Close</button>
                                                </form>
                                            </div>
                                        </div>
                                    </dialog>

                                    {{-- archive model --}}
                                    <dialog id="archive_modal_{{ $order['id'] }}" class="modal">
                                        <div class="modal-box">
                                            <form method="dialog">
                                                <button
                                                    class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                            </form>
                                            <p class="text-lg font-semibold py-0">Confirm Archive</p>

                                            <p class="py-2 mb-0 text-sm">
                                                Are you sure you want to archive
                                                <span class="italic">Order #{{ $order['order_number'] }}</span>
                                                ?
                                            </p>
                                            <div class="modal-action mt-0">
                                                <form method="dialog">
                                                    <button class="btn">Cancel</button>
                                                </form>
                                                <form method="POST"
                                                    action="{{ route('admin.dashboard.order.id.archive.post', ['id' => $order['id']]) }}"
                                                    x-data="{ submitting: false }" @submit="submitting=true">
                                                    @csrf
                                                    <button type="submit" class="w-fit btn btn-primary"
                                                        :disabled="submitting">
                                                        <span x-show="submitting"
                                                            class="loading loading-spinner loading-sm mr-2"></span>
                                                        <span x-show="submitting">Archiving</span>
                                                        <span x-show="!submitting">
                                                            Archive
                                                        </span>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </dialog>

                                    {{-- unarchive model --}}
                                    <dialog id="unarchive_modal_{{ $order['id'] }}" class="modal">
                                        <div class="modal-box">
                                            <form method="dialog">
                                                <button
                                                    class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                            </form>
                                            <p class="text-lg font-semibold py-0">Confirm Unarchive</p>

                                            <p class="py-2 mb-0 text-sm">
                                                Are you sure you want to unarchive
                                                <span class="italic">Order #{{ $order['order_number'] }}</span>
                                                ?
                                            </p>
                                            <div class="modal-action mt-0">
                                                <form method="dialog">
                                                    <button class="btn">Cancel</button>
                                                </form>
                                                <form method="POST"
                                                    action="{{ route('admin.dashboard.order.id.unarchive.post', ['id' => $order['id']]) }}"
                                                    x-data="{ submitting: false }" @submit="submitting=true">
                                                    @csrf
                                                    <button type="submit" class="w-fit btn btn-primary"
                                                        :disabled="submitting">
                                                        <span x-show="submitting"
                                                            class="loading loading-spinner loading-sm mr-2"></span>
                                                        <span x-show="submitting">Unarchiving</span>
                                                        <span x-show="!submitting">
                                                            Unarchive
                                                        </span>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </dialog>

                                    {{-- DELETE DIALOG --}}
                                    <dialog id="delete_modal_{{ $order['id'] }}" class="modal">
                                        <div class="modal-box">
                                            <form method="dialog">
                                                <button
                                                    class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                            </form>
                                            <p class="text-lg font-semibold py-0">Confirm Delete</p>

                                            <p class="py-2 mb-0 text-sm">
                                                Are you sure you want to delete
                                                <span class="italic text-error">Order #{{ $order['order_number'] }}</span>
                                                ?
                                            </p>
                                            <div class="modal-action mt-0">
                                                <form method="dialog">
                                                    <button class="btn">Cancel</button>
                                                </form>
                                                <form method="POST"
                                                    action="{{ route('order_history.delete', ['id' => $order['id']]) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-error">Delete</button>
                                                </form>
                                            </div>
                                        </div>
                                    </dialog>


                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- PAGINATION --}}
                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-3 py-3 px-5">
                    <div class="text-sm text-gray-500">
                        <span class="font-semibold">{{ $orders->firstItem() }}</span>
                        –
                        <span class="font-semibold">{{ $orders->lastItem() }}</span>
                        of
                        <span class="font-semibold">{{ $orders->total() }}</span>
                        results
                    </div>

                    <div class="join">
                        @if ($orders->onFirstPage())
                            <button class="join-item btn btn-sm btn-disabled">«</button>
                        @else
                            <a href="{{ $orders->previousPageUrl() }}" class="join-item btn btn-sm">«</a>
                        @endif

                        <a href="{{ $orders->url(1) }}"
                            class="join-item btn btn-sm {{ $orders->currentPage() === 1 ? 'btn-active' : '' }}">
                            1
                        </a>

                        @php
                            $start = max(2, $orders->currentPage() - 1);
                            $end = min($orders->lastPage() - 1, $orders->currentPage() + 1);
                        @endphp

                        @for ($i = $start; $i <= $end; $i++)
                            <a href="{{ $orders->url($i) }}"
                                class="join-item btn btn-sm {{ $orders->currentPage() === $i ? 'btn-active' : '' }}">
                                {{ $i }}
                            </a>
                        @endfor

                        @if ($orders->lastPage() > 1)
                            <a href="{{ $orders->url($orders->lastPage()) }}"
                                class="join-item btn btn-sm {{ $orders->currentPage() === $orders->lastPage() ? 'btn-active' : '' }}">
                                {{ $orders->lastPage() }}
                            </a>
                        @endif

                        @if ($orders->hasMorePages())
                            <a href="{{ $orders->nextPageUrl() }}" class="join-item btn btn-sm">»</a>
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
                        Toast.show('Please select at least one order', {
                            type: 'error'
                        });
                        return;
                    }
                    const modal = document.getElementById(this.current_action);
                    if (modal) modal.showModal();
                },
            });

            Alpine.store('order_search_setting', {
                showDisplayOption: false,
                showFilterOption: false,
                query: "",
                perPage: "20",
                orderBy: "desc",
                sortBy: "last_updated",
                date_limit: "everyday",

                orderStatusFilter: "",
                archivedFilter: false,

                is_last_updated_filter: @json(request('sortBy') == 'last_updated'),
                is_last_created_filter: @json(request('sortBy') == 'last_created'),

                showIDColumn: false,
                showUpdatedTimeColumn: false,
                showCreatedTimeColumn: false,

                init() {
                    const savedSetting = JSON.parse(localStorage.getItem('order_search_setting') ?? "{}");

                    this.showDisplayOption = savedSetting.showDisplayOption ?? false;
                    this.showFilterOption = savedSetting.showFilterOption ?? false;
                    this.query = savedSetting.query ?? "";
                    this.perPage = savedSetting.perPage ?? "20";
                    this.date_limit = savedSetting.date_limit ?? "everyday";
                    this.orderBy = savedSetting.orderBy ?? "desc";
                    this.sortBy = savedSetting.sortBy ?? "last_updated";

                    this.orderStatusFilter = savedSetting.orderStatusFilter ?? "";
                    this.archivedFilter = savedSetting.archivedFilter ?? false;

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
                        date_limit: this.date_limit,
                        showIDColumn: this.showIDColumn,
                        showUpdatedTimeColumn: this.showUpdatedTimeColumn,
                        showCreatedTimeColumn: this.showCreatedTimeColumn,
                        orderStatusFilter: this.orderStatusFilter,
                        archivedFilter: this.archivedFilter,
                    };
                    localStorage.setItem('order_search_setting', JSON.stringify(data));
                },

                resetFilter() {
                    this.orderStatusFilter = "";
                    this.archivedFilter = false;
                    this.save();
                }
            });

            Alpine.store('order_search_setting').init();
        });
    </script>
@endpush
