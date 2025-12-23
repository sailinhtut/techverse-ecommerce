@extends('layouts.admin.admin_dashboard')
@section('admin_dashboard_content')
    <div class="p-3 lg:p-5 min-h-screen ">
        <p class="lg:text-lg font-semibold">Users</p>

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
                <button class="btn btn-sm shadow-none" onclick="create_user_modal.showModal()">Add User</button>
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
                        <span class="italic text-error">Selected Users</span>?
                    </p>

                    <template x-if="$store.bulk.hasCandidates">
                        <ul class="text-sm pl-10 list-decimal max-h-24 overflow-y-auto bg-base-200 p-3">
                            <template x-for="id in $store.bulk.candidates" :key="id">
                                <li x-text="'User ID: ' + id"></li>
                            </template>
                        </ul>
                    </template>

                    <div class="mt-3 modal-action">
                        <form method="dialog"><button class="btn" :disabled="loading">Cancel</button></form>

                        <form method="POST" action="{{ route('admin.dashboard.user.bulk.delete-selected') }}"
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
                        <span class="text-error">All Users</span>?
                    </p>

                    <div class="modal-action">
                        <form method="dialog"><button class="btn" :disabled="loading">Cancel</button></form>

                        <form method="POST" action="{{ route('admin.dashboard.user.bulk.delete-all') }}"
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
                    <template x-data x-if="$store.user_search_setting.sortBy">
                        <input type="hidden" name="sortBy" :value="$store.user_search_setting.sortBy">
                    </template>

                    <template x-data x-if="$store.user_search_setting.perPage">
                        <input type="hidden" x-data name="perPage" :value="$store.user_search_setting.perPage">
                    </template>

                    <template x-data x-if="$store.user_search_setting.orderBy">
                        <input type="hidden" x-data name="orderBy" :value="$store.user_search_setting.orderBy">
                    </template>

                    <input type="text" x-data x-cloak class="join-item input input-sm rounded-l-box" name="query"
                        :value="$store.user_search_setting.query"
                        @change="$store.user_search_setting.query = $event.target.value; $store.user_search_setting.save(); $el.form.submit()">

                    <button class="join-item btn btn-sm">Search</button>
                </form>

                {{-- page limiting --}}
                <form method="GET" action="{{ request()->url() }}">
                    <template x-data x-if="$store.user_search_setting.query && $store.user_search_setting.query.length > 0">
                        <input type="hidden" name="query" :value="$store.user_search_setting.query">
                    </template>

                    <template x-data x-if="$store.user_search_setting.sortBy">
                        <input type="hidden" x-data name="sortBy" :value="$store.user_search_setting.sortBy">
                    </template>

                    <template x-data x-if="$store.user_search_setting.orderBy">
                        <input type="hidden" x-data name="orderBy" :value="$store.user_search_setting.orderBy">
                    </template>

                    <select name="perPage" x-data x-cloak class="select select-sm w-fit shrink-0" x-data
                        x-model="$store.user_search_setting.perPage"
                        @change="$store.user_search_setting.perPage = $event.target.value; $store.user_search_setting.save(); $el.form.submit()">
                        <option value="5">Show 5</option>
                        <option value="10">Show 10</option>
                        <option value="20">Show 20</option>
                        <option value="50">Show 50</option>
                    </select>
                </form>

                {{-- ascending & descending --}}
                <form method="GET" action="{{ request()->url() }}">
                    <template x-data
                        x-if="$store.user_search_setting.query && $store.user_search_setting.query.length > 0">
                        <input type="hidden" name="query" :value="$store.user_search_setting.query">
                    </template>

                    <template x-data x-if="$store.user_search_setting.perPage">
                        <input type="hidden" x-data name="perPage" :value="$store.user_search_setting.perPage">
                    </template>

                    <template x-data x-if="$store.user_search_setting.sortBy">
                        <input type="hidden" x-data name="sortBy" :value="$store.user_search_setting.sortBy">
                    </template>

                    <select name="orderBy" x-data x-cloak x-model="$store.user_search_setting.orderBy"
                        class="select select-sm w-fit shrink-0" x-data :value="$store.user_search_setting.orderBy"
                        @change="$store.user_search_setting.orderBy = $event.target.value; $store.user_search_setting.save() ; $el.form.submit()">
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
                        x-if="$store.user_search_setting.query && $store.user_search_setting.query.length > 0">
                        <input type="hidden" name="query" :value="$store.user_search_setting.query">
                    </template>

                    <template x-data x-if="$store.user_search_setting.perPage">
                        <input type="hidden" x-data name="perPage" :value="$store.user_search_setting.perPage">
                    </template>

                    <template x-data x-if="$store.user_search_setting.orderBy">
                        <input type="hidden" x-data name="orderBy" :value="$store.user_search_setting.orderBy">
                    </template>

                    <select name="sortBy" class="select select-sm w-fit shrink-0" x-data x-cloak
                        :value="$store.user_search_setting.sortBy"
                        @change="$store.user_search_setting.sortBy = $event.target.value; $store.user_search_setting.save() ; $el.form.submit()">
                        <option value="last_updated">Sort By Last Updated
                        </option>
                        <option value="last_created">Sort By Last Created
                        </option>
                    </select>
                </form>

                {{-- <button class="btn btn-sm bg-base-100 font-normal shadow-none border-slate-300" x-data x-transition x-cloak
                    @click="$store.user_search_setting.showFilterOption = !$store.user_search_setting.showFilterOption;$store.user_search_setting.save()">
                    <span x-text="$store.user_search_setting.showFilterOption ? 'Hide Filter' : 'Filter Option'"></span>
                </button> --}}
                <button class="btn btn-sm bg-base-100 font-normal shadow-none border-slate-300" x-data x-transition x-cloak
                    @click="$store.user_search_setting.showDisplayOption = !$store.user_search_setting.showDisplayOption;$store.user_search_setting.save()">
                    <span x-text="$store.user_search_setting.showDisplayOption ? 'Hide Display' : 'Display Option'"></span>
                </button>
            </div>
        </div>

        {{-- filtering --}}
        <div class="mt-3 p-3 border border-base-300 rounded-md flex flex-col gap-2" x-cloak x-transition x-data
            x-show="$store.user_search_setting.showFilterOption">
            <p class="text-xs">Filter Options</p>
            <div class="grid grid-cols-2 md:grid-cols-4 xl:grid-cols-6 gap-3">
                {{-- <div class="flex flex-col gap-1">
                    <select x-data class="select select-sm text-xs" name="brand"
                        :value="$store.user_search_setting.pinnedFilter ? 'true' : 'false'"
                        @change="$store.user_search_setting.pinnedFilter = $event.target.value; $store.user_search_setting.save()">
                        <option value="false">Filter Pinned</option>
                        <option value="true">Pinned Product</option>
                    </select>
                </div> --}}
            </div>
            <div class="flex flex-row gap-2">
                  <button class="btn btn-sm" x-data @click="document.getElementById('queryForm').requestSubmit();">Search</button>
                <button class="btn btn-sm" x-data @click="$store.user_search_setting.resetFilter()">Reset</button>
            </div>
        </div>

        {{-- column displaying --}}
        <div class="mt-3 p-3 border border-base-300 rounded-md flex flex-col gap-2" x-cloak x-transition x-data
            x-show="$store.user_search_setting.showDisplayOption">
            <p class="text-xs">Display Options</p>
            <div class="flex sm:flex-row flex-col flex-wrap gap-3">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" class="checkbox checkbox-xs rounded-sm" x-data
                        :checked="$store.user_search_setting.showIDColumn"
                        @change="$store.user_search_setting.showIDColumn = !$store.user_search_setting.showIDColumn; $store.user_search_setting.save()">
                    <span class="text-xs">Show ID</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" class="checkbox checkbox-xs rounded-sm" x-data
                        :checked="$store.user_search_setting.showUpdatedTimeColumn || $store.user_search_setting
                            .is_last_updated_filter"
                        @change="$store.user_search_setting.showUpdatedTimeColumn = !$store.user_search_setting.showUpdatedTimeColumn; $store.user_search_setting.save()">
                    <span class="text-xs">Show Updated Time</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" class="checkbox checkbox-xs rounded-sm" x-data
                        :checked="$store.user_search_setting.showCreatedTimeColumn || $store.user_search_setting
                            .is_last_created_filter"
                        @change="$store.user_search_setting.showCreatedTimeColumn = !$store.user_search_setting.showCreatedTimeColumn; $store.user_search_setting.save()">
                    <span class="text-xs">Show Created Time</span>
                </label>
            </div>
        </div>


        <div class="mt-3 card overflow-x-auto shadow-sm border border-base-300">
            <div class="card-body p-0 m-0 ">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="w-[10px]">
                                <input type="checkbox" class="checkbox checkbox-xs rounded-sm" x-data
                                    :checked="$store.bulk.isAllSelected()"
                                    @change="$store.bulk.toggleSelectAll($el.checked)">
                            </th>
                            <th class="w-[50px]">No.</th>
                            <th x-cloak x-data x-show="$store.user_search_setting.showIDColumn" class="w-[50px]">ID
                            </th>
                            <th class="w-[150px]">Profile</th>
                            <th class="w-[180px]">Name</th>
                            <th class="w-[200px]">Email</th>
                            <th class="w-[150px]">Phone</th>
                            <th class="w-[150px]">Role</th>
                            <th x-cloak x-data
                                x-show="$store.user_search_setting.showUpdatedTimeColumn || $store.user_search_setting.is_last_updated_filter">
                                Updated At</th>
                            <th x-cloak x-data
                                x-show="$store.user_search_setting.showCreatedTimeColumn || $store.user_search_setting.is_last_created_filter">
                                Created At</th>
                            <th class="w-[150px]">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>
                                    <input type="checkbox" class="checkbox checkbox-xs rounded-sm row-checkbox"
                                        data-id="{{ $user['id'] }}" x-data
                                        :checked="$store.bulk.candidates.includes({{ $user['id'] }})"
                                        @change="$store.bulk.toggleCandidate({{ $user['id'] }})">
                                </td>
                                <td>{{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}.</td>
                                <td x-cloak x-data x-show="$store.user_search_setting.showIDColumn">
                                    {{ $user['id'] }}
                                </td>
                                <td>
                                    <div class="avatar">
                                        <div
                                            class="w-5 rounded-full ring ring-base-300 ring-offset-base-100 ring-offset-2">
                                            <img src="{{ $user['profile'] ?? asset('assets/images/blank_profile.png') }}"
                                                alt="Profile" />
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <div onclick="document.getElementById('detail_modal_{{ $user['id'] }}').showModal()"
                                        class="cursor-default hover:underline"> {{ $user['name'] }}</div>
                                </td>

                                <td>{{ $user['email'] }}</td>

                                <td>
                                    {{ $user['phone_one'] ?? '-' }}
                                </td>

                                <td>
                                    <div class="badge badge-outline">
                                        {{ $user['role']['display_name'] ?? 'N/A' }}
                                    </div>
                                </td>

                                <td x-cloak x-data
                                    x-show="$store.user_search_setting.showUpdatedTimeColumn || $store.user_search_setting.is_last_updated_filter">
                                    {{ $user['updated_at'] ? \Carbon\Carbon::parse($user['updated_at'])->format('Y-m-d h:i A') : '-' }}
                                </td>
                                <td x-cloak x-data
                                    x-show="$store.user_search_setting.showCreatedTimeColumn || $store.user_search_setting.is_last_created_filter">
                                    {{ $user['created_at'] ? \Carbon\Carbon::parse($user['created_at'])->format('Y-m-d h:i A') : '-' }}
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
                                                    onclick="document.getElementById('detail_modal_{{ $user['id'] }}').showModal()">
                                                    View Details
                                                </button>
                                            </li>
                                            <li><button
                                                    onclick="document.getElementById('edit_modal_{{ $user['id'] }}').showModal()">Edit</button>
                                            </li>
                                            <li>
                                                <button type="button" class="text-error"
                                                    onclick="document.getElementById('delete_modal_{{ $user['id'] }}').showModal()">
                                                    Delete
                                                </button>
                                            </li>
                                        </ul>
                                    </div>

                                    {{-- user detail modal --}}
                                    <dialog id="detail_modal_{{ $user['id'] }}" class="modal">
                                        <div class="modal-box max-w-2xl max-h-[85vh] overflow-y-auto">
                                            <form method="dialog">
                                                <button
                                                    class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                            </form>

                                            <h3 class="text-lg font-semibold text-center mb-3">
                                                User Details
                                            </h3>

                                            <div class="flex items-center justify-center mb-4">
                                                <div class="avatar">
                                                    <div
                                                        class="w-20 rounded-full ring ring-base-300 ring-offset-base-100 ring-offset-2">
                                                        <img src="{{ $user['profile'] ?? asset('assets/images/blank_profile.png') }}"
                                                            alt="Profile" />
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div>
                                                    <label class="text-sm">User ID</label>
                                                    <input type="text"
                                                        class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                                        value="{{ $user['id'] }}" readonly>
                                                </div>
                                                <div>
                                                    <label class="text-sm">Name</label>
                                                    <input type="text"
                                                        class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                                        value="{{ $user['name'] }}" readonly>
                                                </div>
                                                <div>
                                                    <label class="text-sm">Email</label>
                                                    <input type="text"
                                                        class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                                        value="{{ $user['email'] }}" readonly>
                                                </div>
                                                <div>
                                                    <label class="text-sm">Phone 1</label>
                                                    <input type="text"
                                                        class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                                        value="{{ $user['phone_one'] ?? '-' }}" readonly>
                                                </div>
                                                <div>
                                                    <label class="text-sm">Phone 2</label>
                                                    <input type="text"
                                                        class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                                        value="{{ $user['phone_two'] ?? '-' }}" readonly>
                                                </div>
                                                <div>
                                                    <label class="text-sm">Email Verified State</label>
                                                    <input type="text"
                                                        class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                                        value="{{ $user['email_verified_at'] ? 'Verified' : 'Not Verified' }}"
                                                        readonly>
                                                </div>
                                                <div>
                                                    <label class="text-sm">Created At</label>
                                                    <input type="text"
                                                        class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                                        value="{{ $user['created_at'] ? \Carbon\Carbon::parse($user['created_at'])->format('Y-m-d h:i A') : '' }}"
                                                        readonly>
                                                </div>

                                                <div class="md:col-span-2">
                                                    <p class="mb-2"><strong>Role Information</strong></p>
                                                    @if ($user['role'])
                                                        <div class="bg-base-200 rounded-box p-3 text-sm">
                                                            <p><strong>Role Name:</strong>
                                                                {{ $user['role']['display_name'] }}</p>
                                                            <p><strong>Description:</strong>
                                                                {{ $user['role']['description'] ?? '-' }}</p>

                                                            @php
                                                                $permissions = is_array(
                                                                    $user['role']['permissions'] ?? null,
                                                                )
                                                                    ? $user['role']['permissions']
                                                                    : json_decode(
                                                                        $user['role']['permissions'] ?? '[]',
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

                                    {{-- user edit modal --}}
                                    <dialog id="edit_modal_{{ $user['id'] }}" class="modal">
                                        <div class="modal-box max-w-2xl max-h-[85vh] overflow-y-auto">
                                            <form method="dialog">
                                                <button
                                                    class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                            </form>

                                            <h3 class="text-lg font-semibold text-center mb-3">Edit User</h3>

                                            <form method="POST" enctype="multipart/form-data"
                                                action="{{ route('admin.dashboard.user.id.post', ['id' => $user['id']]) }}">
                                                @csrf
                                                @method('POST')

                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">

                                                    <div class="md:col-span-2">
                                                        <label class="text-sm">Name</label>
                                                        <input name="name" class="input w-full border-base-300"
                                                            value="{{ $user['name'] }}" required>
                                                    </div>

                                                    <div class="md:col-span-2">
                                                        <label class="text-sm">Email</label>
                                                        <input name="email" type="email"
                                                            class="input w-full border-base-300"
                                                            value="{{ $user['email'] }}" required>
                                                    </div>

                                                    <div class="md:col-span-2">
                                                        <label class="text-sm flex items-center">Reset Password <span
                                                                class="tooltip font-normal ml-3 z-50"
                                                                data-tip="Enter a new password for this user. Updating their password will automatically log them out from all sessions. Proceed with caution ⚠️.">
                                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                    viewBox="0 0 24 24" stroke-width="1.5"
                                                                    stroke="currentColor" class="size-4">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                                                                </svg>
                                                            </span></label>
                                                        <div x-data="{ show: false }" class="relative">
                                                            <input name="password" :type="show ? 'text' : 'password'"
                                                                class="input w-full border-base-300"
                                                                value="{{ old('password') }}">
                                                            <span
                                                                class="absolute top-1/2 right-3 -translate-y-1/2 cursor-pointer z-20"
                                                                @click="show = !show">
                                                                <template x-if="!show">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                        viewBox="0 0 24 24" stroke-width="1.5"
                                                                        stroke="currentColor" class="size-4">
                                                                        <path stroke-linecap="round"
                                                                            stroke-linejoin="round"
                                                                            d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                                        <path stroke-linecap="round"
                                                                            stroke-linejoin="round"
                                                                            d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                                    </svg>
                                                                </template>
                                                                <template x-if="show">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                        viewBox="0 0 24 24" stroke-width="1.5"
                                                                        stroke="currentColor" class="size-4">
                                                                        <path stroke-linecap="round"
                                                                            stroke-linejoin="round"
                                                                            d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                                                    </svg>
                                                                </template>
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <div>
                                                        <label class="text-sm">Phone 1</label>
                                                        <input name="phone_one" class="input w-full border-base-300"
                                                            value="{{ $user['phone_one'] }}">
                                                    </div>

                                                    <div>
                                                        <label class="text-sm">Phone 2</label>
                                                        <input name="phone_two" class="input w-full border-base-300"
                                                            value="{{ $user['phone_two'] }}">
                                                    </div>

                                                    <div class="md:col-span-2">
                                                        <label class="text-sm">Profile (Optional)</label>
                                                        <input name="profile" type="file"
                                                            class="file-input file-input-bordered w-full">
                                                    </div>

                                                    <div class="md:col-span-2 flex items-center gap-2">
                                                        <input type="hidden" name="remove_profile" value="0">
                                                        <input type="checkbox" name="remove_profile"
                                                            class="checkbox checkbox-sm" value="1"
                                                            id="remove_profile">
                                                        <label class="text-sm" for="remove_profile">Remove Profile</label>
                                                    </div>

                                                    <div>
                                                        <label class="text-sm">Role</label>
                                                        <select name="role_id" class="select w-full border-base-300"
                                                            required>
                                                            <option disabled>Select Role</option>
                                                            @foreach ($roles as $role)
                                                                <option value="{{ $role['id'] }}"
                                                                    @selected($user['role_id'] == $role['id'])>
                                                                    {{ ucfirst($role['name']) }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div >
                                                        <label class="text-sm">Date of Birth</label>
                                                        <input name="date_of_birth" type="date"
                                                            class="input w-full border-base-300"
                                                            value="{{ $user['date_of_birth'] }}">
                                                    </div>

                                                </div>

                                                <div class="modal-action mt-3">
                                                    <button type="submit" class="btn btn-primary">Update User</button>
                                                </div>
                                            </form>
                                        </div>
                                    </dialog>


                                    {{-- user delete modal --}}
                                    <dialog id="delete_modal_{{ $user['id'] }}" class="modal">
                                        <div class="modal-box">
                                            <form method="dialog">
                                                <button
                                                    class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                            </form>
                                            <p class="text-lg font-semibold py-0">Confirm Delete</p>

                                            <p class="py-2 mb-0 text-sm">
                                                Are you sure you want to delete
                                                <span class="italic text-error">{{ $user['name'] }}</span> ?
                                            </p>
                                            <div class="modal-action mt-0">
                                                <form method="dialog">
                                                    <button class="btn">Cancel</button>
                                                </form>
                                                <form method="POST"
                                                    action="{{ route('admin.dashboard.user.id.delete', ['id' => $user['id']]) }}">
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

                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-3 py-3 px-5">
                    <div class="text-sm text-gray-500">
                        <span class="font-semibold">{{ $users->firstItem() }}</span>
                        –
                        <span class="font-semibold">{{ $users->lastItem() }}</span>
                        of
                        <span class="font-semibold">{{ $users->total() }}</span>
                        results
                    </div>

                    <div class="join">
                        @if ($users->onFirstPage())
                            <button class="join-item btn btn-sm btn-disabled">«</button>
                        @else
                            <a href="{{ $users->previousPageUrl() }}" class="join-item btn btn-sm">«</a>
                        @endif

                        <a href="{{ $users->url(1) }}"
                            class="join-item btn btn-sm {{ $users->currentPage() === 1 ? 'btn-active' : '' }}">
                            1
                        </a>

                        @php
                            $start = max(2, $users->currentPage() - 1);
                            $end = min($users->lastPage() - 1, $users->currentPage() + 1);
                        @endphp

                        @for ($i = $start; $i <= $end; $i++)
                            <a href="{{ $users->url($i) }}"
                                class="join-item btn btn-sm {{ $users->currentPage() === $i ? 'btn-active' : '' }}">
                                {{ $i }}
                            </a>
                        @endfor

                        @if ($users->lastPage() > 1)
                            <a href="{{ $users->url($users->lastPage()) }}"
                                class="join-item btn btn-sm {{ $users->currentPage() === $users->lastPage() ? 'btn-active' : '' }}">
                                {{ $users->lastPage() }}
                            </a>
                        @endif

                        @if ($users->hasMorePages())
                            <a href="{{ $users->nextPageUrl() }}" class="join-item btn btn-sm">»</a>
                        @else
                            <button class="join-item btn btn-sm btn-disabled">»</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <dialog id="create_user_modal" class="modal">
            <div class="modal-box max-w-2xl max-h-[85vh] overflow-y-auto">
                <form method="dialog">
                    <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                </form>

                <h3 class="text-lg font-semibold text-center mb-3">Create User</h3>

                <form method="POST" enctype="multipart/form-data"
                    action="{{ route('admin.dashboard.user.create.post') }}">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">

                        <div class="md:col-span-2">
                            <label class="text-sm">Name</label>
                            <input name="name" class="input w-full border-base-300" value="{{ old('name') }}"
                                required>
                        </div>

                        <div class="md:col-span-2">
                            <label class="text-sm">Email</label>
                            <input name="email" type="email" class="input w-full border-base-300"
                                value="{{ old('email') }}" required>
                        </div>

                        <div class="md:col-span-2">
                            <label class="text-sm">Password</label>
                            <div x-data="{ show: false }" class="relative">
                                <input name="password" :type="show ? 'text' : 'password'"
                                    class="input w-full border-base-300" value="{{ old('password') }}">
                                <span class="absolute top-1/2 right-3 -translate-y-1/2 cursor-pointer z-20"
                                    @click="show = !show">
                                    <template x-if="!show">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-4">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                    </template>
                                    <template x-if="show">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-4">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                        </svg>
                                    </template>
                                </span>
                            </div>
                        </div>

                        <div>
                            <label class="text-sm">Phone 1</label>
                            <input name="phone_one" class="input w-full border-base-300" value="{{ old('phone_one') }}">
                        </div>

                        <div>
                            <label class="text-sm">Phone 2</label>
                            <input name="phone_two" class="input w-full border-base-300" value="{{ old('phone_two') }}">
                        </div>

                        <div>
                            <label class="text-sm">Profile</label>
                            <input name="profile" type="file" class="file-input file-input-bordered w-full">
                        </div>

                        <div>
                            <label class="text-sm">Role</label>
                            <select name="role_id" class="select w-full border-base-300" required>
                                <option disabled selected>Select Role</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role['id'] }}" @selected(old('role_id') == $role['id'])>
                                        {{ $role['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label class="text-sm">Date of Birth</label>
                            <input name="date_of_birth" type="date" class="input w-full border-base-300"
                                value="{{ old('date_of_birth') }}">
                        </div>
                    </div>

                    <div class="modal-action mt-3">
                        <button type="submit" class="btn btn-primary">Create User</button>
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
                        Toast.show('Please select at least one user', {
                            type: 'error'
                        });
                        return;
                    }
                    const modal = document.getElementById(this.current_action);
                    if (modal) modal.showModal();
                },
            });

            Alpine.store('user_search_setting', {
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
                    const savedSetting = JSON.parse(localStorage.getItem('user_search_setting') ?? "{}");

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
                    localStorage.setItem('user_search_setting', JSON.stringify(data));
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

            Alpine.store('user_search_setting').init();
        });
    </script>
@endpush
