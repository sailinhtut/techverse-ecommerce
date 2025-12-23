@extends('layouts.admin.admin_dashboard')

@section('admin_dashboard_content')
    <div class="p-3 lg:p-5 min-h-screen">
        <p class="lg:text-lg font-semibold">Product Reviews</p>

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
                <button class="btn btn-sm shadow-none"
                    onclick="document.getElementById('create_review_modal').showModal()">Add Review</button>
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
                        <span class="italic text-error">Selected Reviews</span>?
                    </p>

                    <template x-if="$store.bulk.hasCandidates">
                        <ul class="text-sm pl-10 list-decimal max-h-24 overflow-y-auto bg-base-200 p-3">
                            <template x-for="id in $store.bulk.candidates" :key="id">
                                <li x-text="'Review ID: ' + id"></li>
                            </template>
                        </ul>
                    </template>

                    <div class="mt-3 modal-action">
                        <form method="dialog"><button class="btn" :disabled="loading">Cancel</button></form>

                        <form method="POST" action="{{ route('admin.dashboard.product.review.bulk.delete-selected') }}"
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
                        <span class="text-error">All Reviews</span>?
                    </p>

                    <div class="modal-action">
                        <form method="dialog"><button class="btn" :disabled="loading">Cancel</button></form>

                        <form method="POST" action="{{ route('admin.dashboard.product.review.bulk.delete-all') }}"
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
                    <template x-data x-if="$store.review_search_setting.sortBy">
                        <input type="hidden" name="sortBy" :value="$store.review_search_setting.sortBy">
                    </template>

                    <template x-data x-if="$store.review_search_setting.perPage">
                        <input type="hidden" x-data name="perPage" :value="$store.review_search_setting.perPage">
                    </template>

                    <template x-data x-if="$store.review_search_setting.orderBy">
                        <input type="hidden" x-data name="orderBy" :value="$store.review_search_setting.orderBy">
                    </template>

                    <input type="text" x-data x-cloak class="join-item input input-sm rounded-l-box" name="query"
                        :value="$store.review_search_setting.query"
                        @change="$store.review_search_setting.query = $event.target.value; $store.review_search_setting.save(); $el.form.submit()">

                    <button class="join-item btn btn-sm">Search</button>
                </form>

                {{-- page limiting --}}
                <form method="GET" action="{{ request()->url() }}">
                    <template x-data
                        x-if="$store.review_search_setting.query && $store.review_search_setting.query.length > 0">
                        <input type="hidden" name="query" :value="$store.review_search_setting.query">
                    </template>

                    <template x-data x-if="$store.review_search_setting.sortBy">
                        <input type="hidden" x-data name="sortBy" :value="$store.review_search_setting.sortBy">
                    </template>

                    <template x-data x-if="$store.review_search_setting.orderBy">
                        <input type="hidden" x-data name="orderBy" :value="$store.review_search_setting.orderBy">
                    </template>

                    <select name="perPage" x-data x-cloak class="select select-sm w-fit shrink-0" x-data
                        x-model="$store.review_search_setting.perPage"
                        @change="$store.review_search_setting.perPage = $event.target.value; $store.review_search_setting.save(); $el.form.submit()">
                        <option value="5">Show 5</option>
                        <option value="10">Show 10</option>
                        <option value="20">Show 20</option>
                        <option value="50">Show 50</option>
                    </select>
                </form>

                {{-- ascending & descending --}}
                <form method="GET" action="{{ request()->url() }}">
                    <template x-data
                        x-if="$store.review_search_setting.query && $store.review_search_setting.query.length > 0">
                        <input type="hidden" name="query" :value="$store.review_search_setting.query">
                    </template>

                    <template x-data x-if="$store.review_search_setting.perPage">
                        <input type="hidden" x-data name="perPage" :value="$store.review_search_setting.perPage">
                    </template>

                    <template x-data x-if="$store.review_search_setting.sortBy">
                        <input type="hidden" x-data name="sortBy" :value="$store.review_search_setting.sortBy">
                    </template>

                    <select name="orderBy" x-data x-cloak x-model="$store.review_search_setting.orderBy"
                        class="select select-sm w-fit shrink-0" x-data :value="$store.review_search_setting.orderBy"
                        @change="$store.review_search_setting.orderBy = $event.target.value; $store.review_search_setting.save() ; $el.form.submit()">
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
                        x-if="$store.review_search_setting.query && $store.review_search_setting.query.length > 0">
                        <input type="hidden" name="query" :value="$store.review_search_setting.query">
                    </template>

                    <template x-data x-if="$store.review_search_setting.perPage">
                        <input type="hidden" x-data name="perPage" :value="$store.review_search_setting.perPage">
                    </template>

                    <template x-data x-if="$store.review_search_setting.orderBy">
                        <input type="hidden" x-data name="orderBy" :value="$store.review_search_setting.orderBy">
                    </template>

                    <select name="sortBy" class="select select-sm w-fit shrink-0" x-data x-cloak
                        :value="$store.review_search_setting.sortBy"
                        @change="$store.review_search_setting.sortBy = $event.target.value; $store.review_search_setting.save() ; $el.form.submit()">
                        <option value="last_updated">Sort By Last Updated
                        </option>
                        <option value="last_created">Sort By Last Created
                        </option>
                    </select>
                </form>

                {{-- <button class="btn btn-sm bg-base-100 font-normal shadow-none border-slate-300" x-data x-transition x-cloak
                    @click="$store.review_search_setting.showFilterOption = !$store.review_search_setting.showFilterOption;$store.review_search_setting.save()">
                    <span x-text="$store.review_search_setting.showFilterOption ? 'Hide Filter' : 'Filter Option'"></span>
                </button> --}}
                <button class="btn btn-sm bg-base-100 font-normal shadow-none border-slate-300" x-data x-transition x-cloak
                    @click="$store.review_search_setting.showDisplayOption = !$store.review_search_setting.showDisplayOption;$store.review_search_setting.save()">
                    <span
                        x-text="$store.review_search_setting.showDisplayOption ? 'Hide Display' : 'Display Option'"></span>
                </button>
            </div>
        </div>

        {{-- Filtering Section --}}
        <div class="mt-3 p-3 border border-base-300 rounded-md flex flex-col gap-2" x-cloak x-transition x-data
            x-show="$store.review_search_setting.showFilterOption">
            <p class="text-xs">Filter Options</p>
            <div class="grid grid-cols-2 md:grid-cols-4 xl:grid-cols-6 gap-3">
                {{-- <div class="flex flex-col gap-1">
                    <select x-data class="select select-sm text-xs" name="brand"
                        :value="$store.review_search_setting.saleFilter ? 'true' : 'false'"
                        @change="$store.review_search_setting.saleFilter = $event.target.value; $store.review_search_setting.save()">
                        <option value="false">Filter Sale</option>
                        <option value="true">Sale Product</option>
                    </select>
                </div> --}}
            </div>
            <div class="flex flex-row gap-2">
                  <button class="btn btn-sm" x-data @click="document.getElementById('queryForm').requestSubmit();">Search</button>
                <button class="btn btn-sm" x-data @click="$store.review_search_setting.resetFilter()">Reset</button>
            </div>
        </div>

        {{-- column displaying --}}
        <div class="mt-3 p-3 border border-base-300 rounded-md flex flex-col gap-2" x-cloak x-transition x-data
            x-show="$store.review_search_setting.showDisplayOption">
            <p class="text-xs">Display Options</p>
            <div class="flex sm:flex-row flex-col flex-wrap gap-3">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" class="checkbox checkbox-xs rounded-sm" x-data
                        :checked="$store.review_search_setting.showIDColumn"
                        @change="$store.review_search_setting.showIDColumn = !$store.review_search_setting.showIDColumn; $store.review_search_setting.save()">
                    <span class="text-xs">Show ID</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" class="checkbox checkbox-xs rounded-sm" x-data
                        :checked="$store.review_search_setting.showUpdatedTimeColumn || $store.review_search_setting
                            .is_last_updated_filter"
                        @change="$store.review_search_setting.showUpdatedTimeColumn = !$store.review_search_setting.showUpdatedTimeColumn; $store.review_search_setting.save()">
                    <span class="text-xs">Show Updated Time</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" class="checkbox checkbox-xs rounded-sm" x-data
                        :checked="$store.review_search_setting.showCreatedTimeColumn || $store.review_search_setting
                            .is_last_created_filter"
                        @change="$store.review_search_setting.showCreatedTimeColumn = !$store.review_search_setting.showCreatedTimeColumn; $store.review_search_setting.save()">
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
                            <th x-cloak x-data x-show="$store.review_search_setting.showIDColumn" class="w-[50px]">ID
                            </th>
                            <th>Product</th>
                            <th>User</th>
                            <th>Rating</th>
                            <th>Comment</th>
                            <th>Image</th>
                            <th x-cloak x-data
                                x-show="$store.review_search_setting.showUpdatedTimeColumn || $store.review_search_setting.is_last_updated_filter">
                                Updated At</th>
                            <th x-cloak x-data
                                x-show="$store.review_search_setting.showCreatedTimeColumn || $store.review_search_setting.is_last_created_filter">
                                Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reviews as $review)
                            <tr>
                                <td>
                                    <input type="checkbox" class="checkbox checkbox-xs rounded-sm row-checkbox"
                                        data-id="{{ $review['id'] }}" x-data
                                        :checked="$store.bulk.candidates.includes({{ $review['id'] }})"
                                        @change="$store.bulk.toggleCandidate({{ $review['id'] }})">
                                </td>
                                <td>{{ $loop->iteration + ($reviews->currentPage() - 1) * $reviews->perPage() }}</td>
                                <td x-cloak x-data x-show="$store.review_search_setting.showIDColumn">
                                    {{ $review['id'] }}
                                </td>
                                <td>
                                    {{-- <p onclick="document.getElementById('detail_modal_{{ $review['id'] }}').showModal()"
                                        class="cursor-pointer hover:underline ">
                                        {{ $review['product']['name'] ?? 'Unknown Product' }}
                                    </p> --}}
                                    <a href="{{ route('admin.dashboard.product.review.id.get', $review['id']) }}"
                                        class="cursor-pointer hover:underline ">
                                        {{ $review['product']['name'] ?? 'Unknown Product' }}
                                    </a>
                                </td>
                                <td>
                                    <p onclick="document.getElementById('user_detail_modal_{{ $review['user']['id'] }}').showModal()"
                                        class="cursor-pointer hover:underline ">
                                        {{ $review['user']['name'] ?? 'Guest' }}
                                    </p>
                                </td>
                                <td>
                                    {{-- <div class="flex items-center gap-1">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i data-lucide="{{ $i <= $review['rating'] ? 'star' : 'star' }}"
                                                class="size-3 {{ $i <= $review['rating'] ? 'text-yellow-500' : 'text-gray-300' }}"></i>
                                        @endfor
                                    </div> --}}
                                    <div class="flex gap-2 items-center">
                                        <div>{{ $review['rating'] }}/5</div>
                                        <div class="rating rating-xs rating-half">
                                            @foreach (['0.5', '1', '1.5', '2', '2.5', '3', '3.5', '4', '4.5', '5'] as $rate)
                                                <input disabled type="radio" name="rating-{{ $review['id'] }}"
                                                    value="{{ $rate }}"
                                                    class="mask mask-star-2 cursor-default {{ strpos($rate, '.5') !== false ? 'mask-half-1' : 'mask-half-2' }} bg-amber-500"
                                                    @checked((float) $review['rating'] == (float) $rate) aria-label="{{ $rate }} star" />
                                            @endforeach
                                        </div>
                                    </div>
                                </td>
                                <td class="max-w-[200px] truncate">{{ $review['comment'] }}</td>
                                <td>
                                    @if ($review['image'])
                                        <img src="{{ $review['image'] }}"
                                            class="w-10 h-10 rounded-md object-cover border border-base-300 cursor-pointer"
                                            onclick="document.getElementById('image_modal_{{ $review['id'] }}').showModal()">
                                    @else
                                        <span class="text-gray-400 text-sm">—</span>
                                    @endif
                                </td>
                                <td x-cloak x-data
                                    x-show="$store.review_search_setting.showUpdatedTimeColumn || $store.review_search_setting.is_last_updated_filter">
                                    {{ $review['updated_at'] ? \Carbon\Carbon::parse($review['updated_at'])->format('Y-m-d h:i A') : '-' }}
                                </td>
                                <td x-cloak x-data
                                    x-show="$store.review_search_setting.showCreatedTimeColumn || $store.review_search_setting.is_last_created_filter">
                                    {{ $review['created_at'] ? \Carbon\Carbon::parse($review['created_at'])->format('Y-m-d h:i A') : '-' }}
                                </td>
                                <td>
                                    <div tabindex="0" role="button" class="dropdown dropdown-left">
                                        <div class="btn btn-square btn-sm btn-ghost">
                                            <i data-lucide="ellipsis-vertical" class="size-5"></i>
                                        </div>
                                        <ul tabindex="0"
                                            class="menu dropdown-content bg-base-100 border border-base-300 w-36 rounded-box p-1 shadow-sm">
                                            <li>
                                                <button
                                                    onclick="document.getElementById('detail_modal_{{ $review['id'] }}').showModal()">View</button>
                                            </li>
                                            <li>
                                                <button
                                                    onclick="document.getElementById('edit_modal_{{ $review['id'] }}').showModal()">Edit</button>
                                            </li>
                                            <li>
                                                <button class="text-error"
                                                    onclick="document.getElementById('delete_modal_{{ $review['id'] }}').showModal()">Delete</button>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>

                            <dialog id="user_detail_modal_{{ $review['user']['id'] }}" class="modal">
                                <div class="modal-box max-w-2xl max-h-[85vh] overflow-y-auto">
                                    <form method="dialog">
                                        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                    </form>

                                    <h3 class="text-lg font-semibold text-center mb-3">
                                        User Details
                                    </h3>

                                    <div class="flex items-center justify-center mb-4">
                                        <div class="avatar">
                                            <div
                                                class="w-20 rounded-full ring ring-base-300 ring-offset-base-100 ring-offset-2">
                                                <img src="{{ $review['user']['profile'] ?? asset('assets/images/blank_profile.png') }}"
                                                    alt="Profile" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="text-sm">User ID</label>
                                            <input type="text"
                                                class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                                value="{{ $review['user']['id'] }}" readonly>
                                        </div>
                                        <div>
                                            <label class="text-sm">Name</label>
                                            <input type="text"
                                                class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                                value="{{ $review['user']['name'] }}" readonly>
                                        </div>
                                        <div>
                                            <label class="text-sm">Email</label>
                                            <input type="text"
                                                class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                                value="{{ $review['user']['email'] }}" readonly>
                                        </div>
                                        <div>
                                            <label class="text-sm">Phone 1</label>
                                            <input type="text"
                                                class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                                value="{{ $review['user']['phone_one'] ?? '-' }}" readonly>
                                        </div>
                                        <div>
                                            <label class="text-sm">Phone 2</label>
                                            <input type="text"
                                                class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                                value="{{ $review['user']['phone_two'] ?? '-' }}" readonly>
                                        </div>
                                        <div>
                                            <label class="text-sm">Email Verified State</label>
                                            <input type="text"
                                                class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                                value="{{ $review['user']['email_verified_at'] ? 'Verified' : 'Not Verified' }}"
                                                readonly>
                                        </div>
                                        <div>
                                            <label class="text-sm">Created At</label>
                                            <input type="text"
                                                class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                                value="{{ $review['user']['created_at'] ? \Carbon\Carbon::parse($review['user']['created_at'])->format('Y-m-d h:i A') : '' }}"
                                                readonly>
                                        </div>

                                        <div class="md:col-span-2">
                                            <p class="mb-2"><strong>Role Information</strong></p>
                                            @if ($review['user']['role'])
                                                <div class="bg-base-200 rounded-box p-3 text-sm">
                                                    <p><strong>Role Name:</strong>
                                                        {{ $review['user']['role']['display_name'] }}</p>
                                                    <p><strong>Description:</strong>
                                                        {{ $review['user']['role']['description'] ?? '-' }}</p>

                                                    @php
                                                        $permissions = is_array(
                                                            $review['user']['role']['permissions'] ?? null,
                                                        )
                                                            ? $review['user']['role']['permissions']
                                                            : json_decode(
                                                                $review['user']['role']['permissions'] ?? '[]',
                                                                true,
                                                            );
                                                    @endphp

                                                    @if (!empty($permissions))
                                                        <p class="mt-2"><strong>Permissions:</strong></p>
                                                        <ul class="list-disc list-inside text-xs">
                                                            @foreach ($permissions as $perm)
                                                                <li>{{ ucfirst(str_replace('_', ' ', $perm)) }}
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @else
                                                        <p class="text-gray-500 text-xs italic mt-1">No permissions
                                                            assigned.</p>
                                                    @endif
                                                </div>
                                            @else
                                                <p class="italic text-gray-500">No role assigned.</p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="modal-action mt-6">
                                        <form method="dialog">
                                            <button class="btn btn-primary w-full">Close</button>
                                        </form>
                                    </div>
                                </div>
                            </dialog>

                            <!-- Detail Modal -->
                            <dialog id="detail_modal_{{ $review['id'] }}" class="modal">
                                <div class="modal-box max-w-3xl max-h-[85vh] overflow-y-auto">
                                    <form method="dialog">
                                        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                    </form>
                                    <h3 class="text-lg font-semibold text-center mb-3">Review Details</h3>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <div class="flex flex-col gap-1">
                                            <label class="text-sm">Product</label>
                                            <input type="text" value="{{ $review['product']['name'] ?? 'N/A' }}"
                                                class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                                readonly>
                                        </div>

                                        <div
                                            class="flex flex-col gap-1>
                                            <label class="text-sm">
                                            User</label>
                                            <input type="text" value="{{ $review['user']['name'] ?? 'Guest' }}"
                                                class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                                readonly>
                                        </div>

                                        <div
                                            class="flex flex-col gap-1>
                                            <label class="text-sm">
                                            Rating</label>
                                            <input type="text" value="{{ $review['rating'] }}/5"
                                                class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                                readonly>
                                        </div>

                                        <div
                                            class="flex flex-col gap-1>
                                            <label class="text-sm">
                                            Created At</label>
                                            <input type="text" value="{{ $review['created_at'] }}"
                                                class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                                readonly>
                                        </div>

                                        <div class="md:col-span-2 flex flex-col gap-1">
                                            <label class="text-sm">Comment</label>
                                            <textarea class="textarea w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                                readonly>{{ $review['comment'] }}</textarea>
                                        </div>

                                        @if ($review['image'])
                                            <div class="md:col-span-2 mt-3 flex flex-col gap-1">
                                                <label class="text-sm">Image</label>
                                                <img src="{{ $review['image'] }}"
                                                    class="w-[200px] max-h-[300px] object-contain border border-base-300 rounded-lg">
                                            </div>
                                        @endif
                                    </div>

                                    <div class="modal-action">
                                        <form method="dialog"><button class="btn">Close</button></form>
                                    </div>
                                </div>
                            </dialog>

                            <!-- Image Modal -->
                            @if ($review['image'])
                                <dialog id="image_modal_{{ $review['id'] }}" class="modal">
                                    <div class="modal-box max-w-2xl max-h-[85vh] overflow-y-auto">
                                        <form method="dialog">
                                            <button
                                                class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                        </form>
                                        <img src="{{ $review['image'] }}"
                                            class="w-full h-auto rounded-lg object-contain">
                                    </div>
                                </dialog>
                            @endif

                            <!-- Edit Modal -->
                            <dialog id="edit_modal_{{ $review['id'] }}" class="modal">
                                <div class="modal-box max-w-2xl max-h-[85vh] overflow-y-auto">
                                    <form method="dialog">
                                        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                    </form>

                                    <h3 class="text-lg font-semibold text-center mb-3">Edit Review</h3>

                                    <form method="POST"
                                        action="{{ route('admin.dashboard.product.review.id.post', ['id' => $review['id']]) }}"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                            <div class="flex flex-col gap-1">
                                                <label class="text-sm">Product ID</label>
                                                <input name="product_id" type="number" class="input w-full"
                                                    value="{{ $review['product']['id'] ?? '' }}" required>
                                            </div>

                                            <div class="flex flex-col gap-1">
                                                <label class="text-sm">User ID</label>
                                                <input name="user_id" type="number" class="input w-full"
                                                    value="{{ $review['user']['id'] ?? '' }}" required>
                                            </div>

                                            <div class="flex flex-col gap-1">
                                                <label class="text-sm">Order ID (Optional)</label>
                                                <input name="order_id" type="number" class="input w-full"
                                                    value="{{ $review['order_id'] ?? '' }}">
                                            </div>

                                            <div class="flex flex-col gap-1">
                                                <label class="text-sm">Rating {{ $review['rating'] }}</label>
                                                <select name="rating" class="select w-full" required>
                                                    @for ($i = 0; $i <= 10; $i++)
                                                        @php
                                                            $value = $i * 0.5;
                                                        @endphp
                                                        <option value="{{ $value }}"
                                                            {{ $review['rating'] == $value ? 'selected' : '' }}>
                                                            {{ $value }}</option>
                                                    @endfor
                                                </select>
                                            </div>

                                            <div>
                                                <label class="text-sm">Approved</label>
                                                <select name="is_approved"
                                                    class="select w-full focus:outline-none focus:ring-0 focus:border-base-300">
                                                    <option value="1" {{ $review['is_approved'] ? 'selected' : '' }}>
                                                        Yes</option>
                                                    <option value="0"
                                                        {{ !$review['is_approved'] ? 'selected' : '' }}>No</option>
                                                </select>
                                            </div>

                                            <div class="md:col-span-2 flex flex-col gap-1">
                                                <label class="text-sm">Comment</label>
                                                <textarea name="comment" class="textarea w-full" rows="4">{{ $review['comment'] }}</textarea>
                                            </div>

                                            <div class="md:col-span-2 flex flex-col gap-2">
                                                <label class="text-sm">Image (Optional)</label>
                                                <input type="file" name="image" accept="image/*"
                                                    class="file-input w-full" />
                                            </div>

                                            <div class="flex flex-col items-start gap-3">
                                                @if ($review['image'])
                                                    <div class="mt-3 flex flex-col gap-1">
                                                        <label class="text-sm">Image</label>
                                                        <img src="{{ $review['image'] }}"
                                                            class="w-[200px] max-h-[300px] object-contain border border-base-300 rounded-lg">
                                                    </div>
                                                @endif
                                                <label class="label cursor-pointer flex items-center text-sm">
                                                    <input type="hidden" name="remove_image" value="0" />
                                                    <input type="checkbox" class="checkbox checkbox-xs"
                                                        name="remove_image" value="1" />
                                                    <span class="label-text text-sm">Remove image</span>
                                                </label>
                                            </div>
                                        </div>

                                        <div class="modal-action mt-3">
                                            <button type="submit" class="btn btn-primary">Update Review</button>
                                        </div>
                                    </form>
                                </div>
                            </dialog>

                            <!-- Delete Modal -->
                            <dialog id="delete_modal_{{ $review['id'] }}" class="modal">
                                <div class="modal-box">
                                    <form method="dialog">
                                        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                    </form>

                                    <p class="text-lg font-semibold">Confirm Delete</p>
                                    <p class="text-sm mb-4">Are you sure you want to delete this review by
                                        <span class="text-error">{{ $review['user']['name'] ?? 'Guest' }}</span>?
                                    </p>

                                    <div class="modal-action">
                                        <form method="dialog"><button class="btn">Cancel</button></form>

                                        <form method="POST"
                                            action="{{ route('admin.dashboard.product.review.id.delete', ['id' => $review['id']]) }}">
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
                        <span class="font-semibold">{{ $reviews->firstItem() }}</span>
                        –
                        <span class="font-semibold">{{ $reviews->lastItem() }}</span>
                        of
                        <span class="font-semibold">{{ $reviews->total() }}</span>
                        results
                    </div>

                    <div class="join">
                        @if ($reviews->onFirstPage())
                            <button class="join-item btn btn-sm btn-disabled">«</button>
                        @else
                            <a href="{{ $reviews->previousPageUrl() }}" class="join-item btn btn-sm">«</a>
                        @endif

                        <a href="{{ $reviews->url(1) }}"
                            class="join-item btn btn-sm {{ $reviews->currentPage() === 1 ? 'btn-active' : '' }}">
                            1
                        </a>

                        @php
                            $start = max(2, $reviews->currentPage() - 1);
                            $end = min($reviews->lastPage() - 1, $reviews->currentPage() + 1);
                        @endphp

                        @for ($i = $start; $i <= $end; $i++)
                            <a href="{{ $reviews->url($i) }}"
                                class="join-item btn btn-sm {{ $reviews->currentPage() === $i ? 'btn-active' : '' }}">
                                {{ $i }}
                            </a>
                        @endfor

                        @if ($reviews->lastPage() > 1)
                            <a href="{{ $reviews->url($reviews->lastPage()) }}"
                                class="join-item btn btn-sm {{ $reviews->currentPage() === $reviews->lastPage() ? 'btn-active' : '' }}">
                                {{ $reviews->lastPage() }}
                            </a>
                        @endif

                        @if ($reviews->hasMorePages())
                            <a href="{{ $reviews->nextPageUrl() }}" class="join-item btn btn-sm">»</a>
                        @else
                            <button class="join-item btn btn-sm btn-disabled">»</button>
                        @endif
                    </div>
                </div>

            </div>
        </div>

        <!-- Create Review Modal -->
        <dialog id="create_review_modal" class="modal">
            <div class="modal-box max-w-2xl max-h-[85vh] overflow-y-auto">
                <form method="dialog">
                    <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                </form>

                <h3 class="text-lg font-semibold text-center mb-3">Create Review</h3>

                <form method="POST" action="{{ route('admin.dashboard.product.review.post') }}"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div class="flex flex-col gap-1">
                            <label class="text-sm">Product ID</label>
                            <input name="product_id" type="number" class="input w-full" placeholder="Product ID"
                                required>
                        </div>

                        <div class="flex flex-col gap-1">
                            <label class="text-sm">User ID</label>
                            <input name="user_id" type="number" class="input w-full"
                                placeholder="User ID (or leave blank for current user)">
                        </div>

                        <div class="flex flex-col gap-1">
                            <label class="text-sm">Order ID (Optional)</label>
                            <input name="order_id" type="number" class="input w-full"
                                placeholder="Order ID (Optional)">
                        </div>

                        <div class="flex flex-col gap-1">
                            <label class="text-sm">Rating</label>
                            <select name="rating" class="select w-full" required>
                                @for ($i = 0; $i <= 10; $i++)
                                    @php
                                        $value = $i * 0.5;
                                    @endphp
                                    <option value="{{ $value }}">{{ $value }}</option>
                                @endfor
                            </select>
                        </div>

                        <div>
                            <label class="text-sm">Approved</label>
                            <select name="is_approved" x-model="is_approved"
                                class="select w-full focus:outline-none focus:ring-0 focus:border-base-300">
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>

                        <div class="md:col-span-2 flex flex-col gap-1">
                            <label class="text-sm">Comment</label>
                            <textarea name="comment" class="textarea w-full" rows="4"></textarea>
                        </div>

                        <div class="md:col-span-2 flex flex-col gap-1">
                            <label class="text-sm">Image (Optional)</label>
                            <input type="file" name="image" accept="image/*" class="file-input w-full" />
                        </div>
                    </div>

                    <div class="modal-action mt-3">
                        <button type="submit" class="btn btn-primary">Create Review</button>
                    </div>
                </form>
            </div>
        </dialog>
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
                        Toast.show('Please select at least one review', {
                            type: 'error'
                        });
                        return;
                    }
                    const modal = document.getElementById(this.current_action);
                    if (modal) modal.showModal();
                },
            });

            Alpine.store('review_search_setting', {
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
                    const savedSetting = JSON.parse(localStorage.getItem('review_search_setting') ?? "{}");

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
                    localStorage.setItem('review_search_setting', JSON.stringify(data));
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

            Alpine.store('review_search_setting').init();
        });
    </script>
@endpush
