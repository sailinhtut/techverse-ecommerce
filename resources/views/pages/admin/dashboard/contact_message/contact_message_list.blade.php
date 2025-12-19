@extends('layouts.admin.admin_dashboard')

@section('admin_dashboard_content')
    <div class="p-5 min-h-screen">
        <p class="lg:text-lg font-semibold">Contact Messages</p>

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
                        <span class="italic text-error">Selected Message</span>?
                    </p>

                    <template x-if="$store.bulk.hasCandidates">
                        <ul class="text-sm pl-10 list-decimal max-h-24 overflow-y-auto bg-base-200 p-3">
                            <template x-for="id in $store.bulk.candidates" :key="id">
                                <li x-text="'Method ID: ' + id"></li>
                            </template>
                        </ul>
                    </template>

                    <div class="mt-3 modal-action">
                        <form method="dialog"><button class="btn" :disabled="loading">Cancel</button></form>

                        <form method="POST" action="{{ route('admin.dashboard.contact_message.bulk.delete-selected') }}"
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
                        <span class="text-error">All Messages</span>?
                    </p>

                    <div class="modal-action">
                        <form method="dialog"><button class="btn" :disabled="loading">Cancel</button></form>

                        <form method="POST" action="{{ route('admin.dashboard.contact_message.bulk.delete-all') }}"
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
                    <template x-data x-if="$store.contact_message_search_setting.sortBy">
                        <input type="hidden" name="sortBy" :value="$store.contact_message_search_setting.sortBy">
                    </template>

                    <template x-data x-if="$store.contact_message_search_setting.perPage">
                        <input type="hidden" x-data name="perPage" :value="$store.contact_message_search_setting.perPage">
                    </template>

                    <template x-data x-if="$store.contact_message_search_setting.orderBy">
                        <input type="hidden" x-data name="orderBy" :value="$store.contact_message_search_setting.orderBy">
                    </template>

                    <input type="text" x-data x-cloak class="join-item input input-sm rounded-l-box" name="query"
                        :value="$store.contact_message_search_setting.query"
                        @change="$store.contact_message_search_setting.query = $event.target.value; $store.contact_message_search_setting.save(); $el.form.submit()">

                    <button class="join-item btn btn-sm">Search</button>
                </form>

                {{-- page limiting --}}
                <form method="GET" action="{{ request()->url() }}">
                    <template x-data
                        x-if="$store.contact_message_search_setting.query && $store.contact_message_search_setting.query.length > 0">
                        <input type="hidden" name="query" :value="$store.contact_message_search_setting.query">
                    </template>

                    <template x-data x-if="$store.contact_message_search_setting.sortBy">
                        <input type="hidden" x-data name="sortBy" :value="$store.contact_message_search_setting.sortBy">
                    </template>

                    <template x-data x-if="$store.contact_message_search_setting.orderBy">
                        <input type="hidden" x-data name="orderBy" :value="$store.contact_message_search_setting.orderBy">
                    </template>

                    <select name="perPage" x-data x-cloak class="select select-sm w-fit shrink-0" x-data
                        x-model="$store.contact_message_search_setting.perPage"
                        @change="$store.contact_message_search_setting.perPage = $event.target.value; $store.contact_message_search_setting.save(); $el.form.submit()">
                        <option value="5">Show 5</option>
                        <option value="10">Show 10</option>
                        <option value="20">Show 20</option>
                        <option value="50">Show 50</option>
                    </select>
                </form>

                {{-- ascending & descending --}}
                <form method="GET" action="{{ request()->url() }}">
                    <template x-data
                        x-if="$store.contact_message_search_setting.query && $store.contact_message_search_setting.query.length > 0">
                        <input type="hidden" name="query" :value="$store.contact_message_search_setting.query">
                    </template>

                    <template x-data x-if="$store.contact_message_search_setting.perPage">
                        <input type="hidden" x-data name="perPage"
                            :value="$store.contact_message_search_setting.perPage">
                    </template>

                    <template x-data x-if="$store.contact_message_search_setting.sortBy">
                        <input type="hidden" x-data name="sortBy"
                            :value="$store.contact_message_search_setting.sortBy">
                    </template>

                    <select name="orderBy" x-data x-cloak x-model="$store.contact_message_search_setting.orderBy"
                        class="select select-sm w-fit shrink-0" x-data
                        :value="$store.contact_message_search_setting.orderBy"
                        @change="$store.contact_message_search_setting.orderBy = $event.target.value; $store.contact_message_search_setting.save() ; $el.form.submit()">
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
                        x-if="$store.contact_message_search_setting.query && $store.contact_message_search_setting.query.length > 0">
                        <input type="hidden" name="query" :value="$store.contact_message_search_setting.query">
                    </template>

                    <template x-data x-if="$store.contact_message_search_setting.perPage">
                        <input type="hidden" x-data name="perPage"
                            :value="$store.contact_message_search_setting.perPage">
                    </template>

                    <template x-data x-if="$store.contact_message_search_setting.orderBy">
                        <input type="hidden" x-data name="orderBy"
                            :value="$store.contact_message_search_setting.orderBy">
                    </template>

                    <select name="sortBy" class="select select-sm w-fit shrink-0" x-data x-cloak
                        :value="$store.contact_message_search_setting.sortBy"
                        @change="$store.contact_message_search_setting.sortBy = $event.target.value; $store.contact_message_search_setting.save() ; $el.form.submit()">
                        <option value="last_updated">Sort By Last Updated
                        </option>
                        <option value="last_created">Sort By Last Created
                        </option>
                    </select>
                </form>

                {{-- <button class="btn btn-sm bg-base-100 font-normal shadow-none border-slate-300" x-data x-transition x-cloak
                    @click="$store.contact_message_search_setting.showFilterOption = !$store.contact_message_search_setting.showFilterOption;$store.contact_message_search_setting.save()">
                    <span x-text="$store.contact_message_search_setting.showFilterOption ? 'Hide Filter' : 'Filter Option'"></span>
                </button> --}}
                <button class="btn btn-sm bg-base-100 font-normal shadow-none border-slate-300" x-data x-transition x-cloak
                    @click="$store.contact_message_search_setting.showDisplayOption = !$store.contact_message_search_setting.showDisplayOption;$store.contact_message_search_setting.save()">
                    <span
                        x-text="$store.contact_message_search_setting.showDisplayOption ? 'Hide Display' : 'Display Option'"></span>
                </button>
            </div>
        </div>

        {{-- filtering --}}
        <div class="mt-3 p-3 border border-base-300 rounded-md flex flex-col gap-2" x-cloak x-transition x-data
            x-show="$store.contact_message_search_setting.showFilterOption">
            <p class="text-xs">Filter Options</p>
            <div class="grid grid-cols-2 md:grid-cols-4 xl:grid-cols-6 gap-3">
                {{-- <div class="flex flex-col gap-1">
                    <select x-data class="select select-sm text-xs" name="brand"
                        :value="$store.contact_message_search_setting.pinnedFilter ? 'true' : 'false'"
                        @change="$store.contact_message_search_setting.pinnedFilter = $event.target.value; $store.contact_message_search_setting.save()">
                        <option value="false">Filter Pinned</option>
                        <option value="true">Pinned Product</option>
                    </select>
                </div> --}}
            </div>
            <div class="flex flex-row gap-2">
                {{-- <button class="btn btn-primary btn-sm">Save</button> --}}
                <button class="btn btn-sm" x-data
                    @click="$store.contact_message_search_setting.resetFilter()">Reset</button>
            </div>
        </div>

        {{-- column displaying --}}
        <div class="mt-3 p-3 border border-base-300 rounded-md flex flex-col gap-2" x-cloak x-transition x-data
            x-show="$store.contact_message_search_setting.showDisplayOption">
            <p class="text-xs">Display Options</p>
            <div class="flex sm:flex-row flex-col flex-wrap gap-3">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" class="checkbox checkbox-xs rounded-sm" x-data
                        :checked="$store.contact_message_search_setting.showIDColumn"
                        @change="$store.contact_message_search_setting.showIDColumn = !$store.contact_message_search_setting.showIDColumn; $store.contact_message_search_setting.save()">
                    <span class="text-xs">Show ID</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" class="checkbox checkbox-xs rounded-sm" x-data
                        :checked="$store.contact_message_search_setting.showUpdatedTimeColumn || $store
                            .contact_message_search_setting
                            .is_last_updated_filter"
                        @change="$store.contact_message_search_setting.showUpdatedTimeColumn = !$store.contact_message_search_setting.showUpdatedTimeColumn; $store.contact_message_search_setting.save()">
                    <span class="text-xs">Show Updated Time</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" class="checkbox checkbox-xs rounded-sm" x-data
                        :checked="$store.contact_message_search_setting.showCreatedTimeColumn || $store
                            .contact_message_search_setting
                            .is_last_created_filter"
                        @change="$store.contact_message_search_setting.showCreatedTimeColumn = !$store.contact_message_search_setting.showCreatedTimeColumn; $store.contact_message_search_setting.save()">
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
                            <th x-cloak x-data x-show="$store.contact_message_search_setting.showIDColumn"
                                class="w-[50px]">ID
                            </th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Message</th>
                            <th>Status</th>
                            <th x-cloak x-data
                                x-show="$store.contact_message_search_setting.showUpdatedTimeColumn || $store.contact_message_search_setting.is_last_updated_filter">
                                Updated At</th>
                            <th x-cloak x-data
                                x-show="$store.contact_message_search_setting.showCreatedTimeColumn || $store.contact_message_search_setting.is_last_created_filter">
                                Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($messages as $message)
                            <tr>
                                <td>
                                    <input type="checkbox" class="checkbox checkbox-xs rounded-sm row-checkbox"
                                        data-id="{{ $message['id'] }}" x-data
                                        :checked="$store.bulk.candidates.includes({{ $message['id'] }})"
                                        @change="$store.bulk.toggleCandidate({{ $message['id'] }})">
                                </td>
                                <td>{{ $loop->iteration + ($messages->currentPage() - 1) * $messages->perPage() }}
                                </td>
                                <td x-cloak x-data x-show="$store.contact_message_search_setting.showIDColumn">
                                    {{ $message['id'] }}
                                </td>
                                <td>
                                    <p onclick="document.getElementById('detail_modal_{{ $message['id'] }}').showModal()"
                                        class="cursor-pointer hover:underline">{{ $message['name'] }}</p>
                                </td>
                                <td>{{ $message['email'] ?? '-' }}</td>
                                <td class="w-[200px] line-clamp-2">{{ $message['message'] }}</td>
                                <td>
                                    @php
                                        $color = match ($message['status'] ?? 'new') {
                                            'new' => 'badge-warning',
                                            'read' => 'badge-error',
                                            'responded' => 'badge-success',
                                            default => 'badge-ghost',
                                        };
                                    @endphp
                                    <div class="badge {{ $color }} badge-outline capitalize">
                                        {{ $message['status'] ?? 'new' }}</div>
                                </td>
                                <td x-cloak x-data
                                    x-show="$store.contact_message_search_setting.showUpdatedTimeColumn || $store.contact_message_search_setting.is_last_updated_filter">
                                    {{ $message['updated_at'] ? \Carbon\Carbon::parse($message['updated_at'])->format('Y-m-d h:i A') : '-' }}
                                </td>
                                <td x-cloak x-data
                                    x-show="$store.contact_message_search_setting.showCreatedTimeColumn || $store.contact_message_search_setting.is_last_created_filter">
                                    {{ $message['created_at'] ? \Carbon\Carbon::parse($message['created_at'])->format('Y-m-d h:i A') : '-' }}
                                </td>
                                <td>
                                    <div tabindex="0" role="button" class="dropdown dropdown-left">
                                        <div class="btn btn-square btn-sm btn-ghost">
                                            <i data-lucide="ellipsis-vertical" class="size-5"></i>
                                        </div>
                                        <ul tabindex="0"
                                            class="menu dropdown-content bg-base-100 border border-base-300 w-30 rounded-box p-1 shadow-sm">
                                            <li><button
                                                    onclick="document.getElementById('detail_modal_{{ $message['id'] }}').showModal()">View</button>
                                            </li>
                                            <li><button
                                                    onclick="document.getElementById('edit_modal_{{ $message['id'] }}').showModal()">Edit</button>
                                            </li>
                                            <li><button class="text-error"
                                                    onclick="document.getElementById('delete_modal_{{ $message['id'] }}').showModal()">Delete</button>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>

                            <dialog id="detail_modal_{{ $message['id'] }}" class="modal">
                                <div class="modal-box max-w-3xl max-h-[85vh] overflow-y-auto">
                                    <form method="dialog">
                                        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                    </form>
                                    <h3 class="text-lg font-semibold text-center mb-3">Message Details</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <div>
                                            <label class="text-sm">ID</label>
                                            <input type="text"
                                                class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                                value="{{ $message['id'] }}" readonly>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="text-sm">Name</label>
                                            <input type="text"
                                                class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                                value="{{ $message['name'] }}" readonly>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="text-sm">Email</label>
                                            <input type="email"
                                                class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                                value="{{ $message['email'] }}" readonly>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="text-sm">Message</label>
                                            <textarea class="textarea w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                                readonly>{{ $message['message'] }}</textarea>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="text-sm">Status</label>
                                            <input type="text"
                                                class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                                value="{{ ucfirst($message['status']) }}" readonly>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="text-sm">Created At</label>
                                            <input type="text"
                                                class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                                value="{{ $message['created_at'] ? \Carbon\Carbon::parse($message['created_at'])->format('Y-m-d h:i A') : '-' }}"
                                                readonly>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="text-sm">Updated At</label>
                                            <input type="text"
                                                class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                                value="{{ $message['updated_at'] ? \Carbon\Carbon::parse($message['updated_at'])->format('Y-m-d h:i A') : '-' }}"
                                                readonly>
                                        </div>
                                    </div>
                                    <div class="modal-action">
                                        <form method="dialog" class="w-full"><button class="btn">Close</button>
                                        </form>
                                    </div>
                                </div>
                            </dialog>

                            {{-- Edit Modal --}}
                            <dialog id="edit_modal_{{ $message['id'] }}" class="modal">
                                <div class="modal-box max-w-3xl max-h-[85vh] overflow-y-auto">
                                    <form method="dialog">
                                        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                    </form>
                                    <h3 class="text-lg font-semibold text-center mb-3">Edit Contact Message</h3>

                                    <form method="POST"
                                        action="{{ route('admin.dashboard.contact_message.id.post', ['id' => $message['id']]) }}">
                                        @csrf
                                        @method('POST')
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                            <div class="flex flex-col gap-1 md:col-span-2">
                                                <label class="text-sm">Status</label>
                                                <select name="status" class="select min-w-full border-base-300" required>
                                                    <option disabled selected>Select Status</option>
                                                    <option value="new"
                                                        <?= $message['status'] == 'new' ? 'selected' : '' ?>>
                                                        New
                                                    </option>
                                                    <option value="read"
                                                        <?= $message['status'] == 'read' ? 'selected' : '' ?>>
                                                        Read
                                                    </option>
                                                    <option value="responded"
                                                        <?= $message['status'] == 'responded' ? 'selected' : '' ?>>
                                                        Responded
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-action mt-3">
                                            <button type="submit" class="btn btn-primary">Update Message</button>

                                        </div>
                                    </form>
                                </div>
                            </dialog>

                            {{-- Delete Modal --}}
                            <dialog id="delete_modal_{{ $message['id'] }}" class="modal">
                                <div class="modal-box">
                                    <form method="dialog"><button
                                            class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                    </form>
                                    <p class="text-lg font-semibold">Confirm Delete</p>
                                    <p class="text-sm mb-4">Are you sure you want to delete <span
                                            class="text-error">{{ $message['name'] }}</span>?</p>
                                    <div class="modal-action">
                                        <button type="button" class="btn"
                                            onclick="delete_modal_{{ $message['id'] }}.close()">Cancel</button>
                                        <form method="POST"
                                            action="{{ route('admin.dashboard.contact_message.id.delete', ['id' => $message['id']]) }}">
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
                        <span class="font-semibold">{{ $messages->firstItem() }}</span>
                        –
                        <span class="font-semibold">{{ $messages->lastItem() }}</span>
                        of
                        <span class="font-semibold">{{ $messages->total() }}</span>
                        results
                    </div>

                    <div class="join">
                        @if ($messages->onFirstPage())
                            <button class="join-item btn btn-sm btn-disabled">«</button>
                        @else
                            <a href="{{ $messages->previousPageUrl() }}" class="join-item btn btn-sm">«</a>
                        @endif

                        <a href="{{ $messages->url(1) }}"
                            class="join-item btn btn-sm {{ $messages->currentPage() === 1 ? 'btn-active' : '' }}">
                            1
                        </a>

                        @php
                            $start = max(2, $messages->currentPage() - 1);
                            $end = min($messages->lastPage() - 1, $messages->currentPage() + 1);
                        @endphp

                        @for ($i = $start; $i <= $end; $i++)
                            <a href="{{ $messages->url($i) }}"
                                class="join-item btn btn-sm {{ $messages->currentPage() === $i ? 'btn-active' : '' }}">
                                {{ $i }}
                            </a>
                        @endfor

                        @if ($messages->lastPage() > 1)
                            <a href="{{ $messages->url($messages->lastPage()) }}"
                                class="join-item btn btn-sm {{ $messages->currentPage() === $messages->lastPage() ? 'btn-active' : '' }}">
                                {{ $messages->lastPage() }}
                            </a>
                        @endif

                        @if ($messages->hasMorePages())
                            <a href="{{ $messages->nextPageUrl() }}" class="join-item btn btn-sm">»</a>
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
                        Toast.show('Please select at least one message', {
                            type: 'error'
                        });
                        return;
                    }
                    const modal = document.getElementById(this.current_action);
                    if (modal) modal.showModal();
                },
            });

            Alpine.store('contact_message_search_setting', {
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
                            'contact_message_search_setting') ??
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
                    localStorage.setItem('contact_message_search_setting', JSON.stringify(data));
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

            Alpine.store('contact_message_search_setting').init();
        });
    </script>
@endpush
