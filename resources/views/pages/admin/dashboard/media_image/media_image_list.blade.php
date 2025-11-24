@extends('layouts.admin.admin_dashboard')

@section('admin_dashboard_content')
    <div class="p-5 min-h-screen">
        <p class="lg:text-lg font-semibold">Media Images</p>

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
                    onclick="document.getElementById('create_media_modal').showModal()">Add Image</button>
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
                        <span class="italic text-error">Selected Images</span>?
                    </p>

                    <template x-if="$store.bulk.hasCandidates">
                        <ul class="text-sm pl-10 list-decimal max-h-24 overflow-y-auto bg-base-200 p-3">
                            <template x-for="id in $store.bulk.candidates" :key="id">
                                <li x-text="'Media Image ID: ' + id"></li>
                            </template>
                        </ul>
                    </template>

                    <div class="mt-3 modal-action">
                        <form method="dialog"><button class="btn" :disabled="loading">Cancel</button></form>

                        <form method="POST" action="{{ route('admin.dashboard.store.media-image.bulk.delete-selected') }}"
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
                        <span class="text-error">All Images</span>?
                    </p>

                    <div class="modal-action">
                        <form method="dialog"><button class="btn" :disabled="loading">Cancel</button></form>

                        <form method="POST" action="{{ route('admin.dashboard.store.media-image.bulk.delete-all') }}"
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
                    <template x-data x-if="$store.media_image_search_setting.sortBy">
                        <input type="hidden" name="sortBy" :value="$store.media_image_search_setting.sortBy">
                    </template>

                    <template x-data x-if="$store.media_image_search_setting.perPage">
                        <input type="hidden" x-data name="perPage" :value="$store.media_image_search_setting.perPage">
                    </template>

                    <template x-data x-if="$store.media_image_search_setting.orderBy">
                        <input type="hidden" x-data name="orderBy" :value="$store.media_image_search_setting.orderBy">
                    </template>

                    <input type="text" x-data x-cloak class="join-item input input-sm rounded-l-box" name="query"
                        :value="$store.media_image_search_setting.query"
                        @change="$store.media_image_search_setting.query = $event.target.value; $store.media_image_search_setting.save(); $el.form.submit()">

                    <button class="join-item btn btn-sm">Search</button>
                </form>

                {{-- page limiting --}}
                <form method="GET" action="{{ request()->url() }}">
                    <template x-data
                        x-if="$store.media_image_search_setting.query && $store.media_image_search_setting.query.length > 0">
                        <input type="hidden" name="query" :value="$store.media_image_search_setting.query">
                    </template>

                    <template x-data x-if="$store.media_image_search_setting.sortBy">
                        <input type="hidden" x-data name="sortBy" :value="$store.media_image_search_setting.sortBy">
                    </template>

                    <template x-data x-if="$store.media_image_search_setting.orderBy">
                        <input type="hidden" x-data name="orderBy" :value="$store.media_image_search_setting.orderBy">
                    </template>

                    <select name="perPage" x-data x-cloak class="select select-sm w-fit shrink-0" x-data
                        x-model="$store.media_image_search_setting.perPage"
                        @change="$store.media_image_search_setting.perPage = $event.target.value; $store.media_image_search_setting.save(); $el.form.submit()">
                        <option value="5">Show 5</option>
                        <option value="10">Show 10</option>
                        <option value="20">Show 20</option>
                        <option value="50">Show 50</option>
                    </select>
                </form>

                {{-- ascending & descending --}}
                <form method="GET" action="{{ request()->url() }}">
                    <template x-data
                        x-if="$store.media_image_search_setting.query && $store.media_image_search_setting.query.length > 0">
                        <input type="hidden" name="query" :value="$store.media_image_search_setting.query">
                    </template>

                    <template x-data x-if="$store.media_image_search_setting.perPage">
                        <input type="hidden" x-data name="perPage" :value="$store.media_image_search_setting.perPage">
                    </template>

                    <template x-data x-if="$store.media_image_search_setting.sortBy">
                        <input type="hidden" x-data name="sortBy" :value="$store.media_image_search_setting.sortBy">
                    </template>

                    <select name="orderBy" x-data x-cloak x-model="$store.media_image_search_setting.orderBy"
                        class="select select-sm w-fit shrink-0" x-data :value="$store.media_image_search_setting.orderBy"
                        @change="$store.media_image_search_setting.orderBy = $event.target.value; $store.media_image_search_setting.save() ; $el.form.submit()">
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
                        x-if="$store.media_image_search_setting.query && $store.media_image_search_setting.query.length > 0">
                        <input type="hidden" name="query" :value="$store.media_image_search_setting.query">
                    </template>

                    <template x-data x-if="$store.media_image_search_setting.perPage">
                        <input type="hidden" x-data name="perPage" :value="$store.media_image_search_setting.perPage">
                    </template>

                    <template x-data x-if="$store.media_image_search_setting.orderBy">
                        <input type="hidden" x-data name="orderBy" :value="$store.media_image_search_setting.orderBy">
                    </template>

                    <select name="sortBy" class="select select-sm w-fit shrink-0" x-data x-cloak
                        :value="$store.media_image_search_setting.sortBy"
                        @change="$store.media_image_search_setting.sortBy = $event.target.value; $store.media_image_search_setting.save() ; $el.form.submit()">
                        <option value="last_updated">Sort By Last Updated
                        </option>
                        <option value="last_created">Sort By Last Created
                        </option>
                    </select>
                </form>

                {{-- <button class="btn btn-sm bg-base-100 font-normal shadow-none border-slate-300" x-data x-transition x-cloak
                    @click="$store.media_image_search_setting.showFilterOption = !$store.media_image_search_setting.showFilterOption;$store.media_image_search_setting.save()">
                    <span x-text="$store.media_image_search_setting.showFilterOption ? 'Hide Filter' : 'Filter Option'"></span>
                </button> --}}
                <button class="btn btn-sm bg-base-100 font-normal shadow-none border-slate-300" x-data x-transition x-cloak
                    @click="$store.media_image_search_setting.showDisplayOption = !$store.media_image_search_setting.showDisplayOption;$store.media_image_search_setting.save()">
                    <span
                        x-text="$store.media_image_search_setting.showDisplayOption ? 'Hide Display' : 'Display Option'"></span>
                </button>
            </div>
        </div>

        {{-- filtering --}}
        <div class="mt-3 p-3 border border-base-300 rounded-md flex flex-col gap-2" x-cloak x-transition x-data
            x-show="$store.media_image_search_setting.showFilterOption">
            <p class="text-xs">Filter Options</p>
            <div class="grid grid-cols-2 md:grid-cols-4 xl:grid-cols-6 gap-3">
                {{-- <div class="flex flex-col gap-1">
                    <select x-data class="select select-sm text-xs" name="brand"
                        :value="$store.media_image_search_setting.pinnedFilter ? 'true' : 'false'"
                        @change="$store.media_image_search_setting.pinnedFilter = $event.target.value; $store.media_image_search_setting.save()">
                        <option value="false">Filter Pinned</option>
                        <option value="true">Pinned Product</option>
                    </select>
                </div> --}}
            </div>
            <div class="flex flex-row gap-2">
                {{-- <button class="btn btn-primary btn-sm">Save</button> --}}
                <button class="btn btn-sm" x-data @click="$store.media_image_search_setting.resetFilter()">Reset</button>
            </div>
        </div>

        {{-- column displaying --}}
        <div class="mt-3 p-3 border border-base-300 rounded-md flex flex-col gap-2" x-cloak x-transition x-data
            x-show="$store.media_image_search_setting.showDisplayOption">
            <p class="text-xs">Display Options</p>
            <div class="flex sm:flex-row flex-col flex-wrap gap-3">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" class="checkbox checkbox-xs rounded-sm" x-data
                        :checked="$store.media_image_search_setting.showIDColumn"
                        @change="$store.media_image_search_setting.showIDColumn = !$store.media_image_search_setting.showIDColumn; $store.media_image_search_setting.save()">
                    <span class="text-xs">Show ID</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" class="checkbox checkbox-xs rounded-sm" x-data
                        :checked="$store.media_image_search_setting.showUpdatedTimeColumn || $store.media_image_search_setting
                            .is_last_updated_filter"
                        @change="$store.media_image_search_setting.showUpdatedTimeColumn = !$store.media_image_search_setting.showUpdatedTimeColumn; $store.media_image_search_setting.save()">
                    <span class="text-xs">Show Updated Time</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" class="checkbox checkbox-xs rounded-sm" x-data
                        :checked="$store.media_image_search_setting.showCreatedTimeColumn || $store.media_image_search_setting
                            .is_last_created_filter"
                        @change="$store.media_image_search_setting.showCreatedTimeColumn = !$store.media_image_search_setting.showCreatedTimeColumn; $store.media_image_search_setting.save()">
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
                            <th x-cloak x-data x-show="$store.media_image_search_setting.showIDColumn" class="w-[50px]">ID
                            </th>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Image</th>
                            <th>Priority</th>
                            <th>Active</th>
                            <th>Start From</th>
                            <th>End At</th>
                            <th x-cloak x-data
                                x-show="$store.media_image_search_setting.showUpdatedTimeColumn || $store.media_image_search_setting.is_last_updated_filter">
                                Updated At</th>
                            <th x-cloak x-data
                                x-show="$store.media_image_search_setting.showCreatedTimeColumn || $store.media_image_search_setting.is_last_created_filter">
                                Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($mediaImages as $media)
                            <tr>
                                <td>
                                    <input type="checkbox" class="checkbox checkbox-xs rounded-sm row-checkbox"
                                        data-id="{{ $media['id'] }}" x-data
                                        :checked="$store.bulk.candidates.includes({{ $media['id'] }})"
                                        @change="$store.bulk.toggleCandidate({{ $media['id'] }})">
                                </td>
                                <td>{{ $loop->iteration + ($mediaImages->currentPage() - 1) * $mediaImages->perPage() }}
                                </td>
                                <td x-cloak x-data x-show="$store.media_image_search_setting.showIDColumn">
                                    {{ $media['id'] }}
                                </td>
                                <td>
                                    <p onclick="document.getElementById('detail_modal_{{ $media['id'] }}').showModal()"
                                        class="cursor-pointer hover:underline">
                                        {{ $media['title'] ?? '-' }}
                                    </p>
                                </td>
                                <td>{{ $media['type'] }}</td>
                                <td>
                                    @if ($media['image'])
                                        <img src="{{ $media['image'] }}"
                                            class="w-10 h-10 rounded-md object-cover border cursor-pointer"
                                            onclick="document.getElementById('image_modal_{{ $media['id'] }}').showModal()">
                                    @else
                                        <span class="text-gray-400 text-sm">—</span>
                                    @endif
                                </td>
                                <td>{{ $media['priority'] ?? 0 }}</td>
                                <td>{{ $media['is_active'] ? 'Enabled' : 'Disabled' }}</td>
                                <td>{{ \Carbon\Carbon::parse($media['start_at'])->format('Y-m-d h:i A') }}</td>
                                <td>{{ \Carbon\Carbon::parse($media['end_at'])->format('Y-m-d h:i A') }}</td>
                                <td x-cloak x-data
                                    x-show="$store.media_image_search_setting.showUpdatedTimeColumn || $store.media_image_search_setting.is_last_updated_filter">
                                    {{ $media['updated_at'] ? \Carbon\Carbon::parse($media['updated_at'])->format('Y-m-d h:i A') : '-' }}
                                </td>
                                <td x-cloak x-data
                                    x-show="$store.media_image_search_setting.showCreatedTimeColumn || $store.media_image_search_setting.is_last_created_filter">
                                    {{ $media['created_at'] ? \Carbon\Carbon::parse($media['created_at'])->format('Y-m-d h:i A') : '-' }}
                                </td>
                                <td>
                                    <div tabindex="0" role="button" class="dropdown dropdown-left">
                                        <div class="btn btn-square btn-sm btn-ghost">
                                            <i data-lucide="ellipsis-vertical" class="size-5"></i>
                                        </div>
                                        <ul tabindex="0"
                                            class="menu dropdown-content bg-base-100 border border-base-300 w-36 rounded-box p-1 shadow-sm">
                                            <li><button
                                                    onclick="document.getElementById('edit_modal_{{ $media['id'] }}').showModal()">Edit</button>
                                            </li>
                                            <li><button class="text-error"
                                                    onclick="document.getElementById('delete_modal_{{ $media['id'] }}').showModal()">Delete</button>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>

                            <!-- Image Modal -->
                            @if ($media['image'])
                                <dialog id="image_modal_{{ $media['id'] }}" class="modal">
                                    <div class="modal-box max-w-2xl max-h-[85vh] overflow-y-auto">
                                        <form method="dialog">
                                            <button
                                                class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                        </form>
                                        <img src="{{ $media['image'] }}" class="w-full h-auto rounded-lg object-contain">
                                    </div>
                                </dialog>
                            @endif

                            {{-- Detail Modal --}}
                            <dialog id="detail_modal_{{ $media['id'] }}" class="modal">
                                <div class="modal-box max-w-3xl max-h-[85vh] overflow-y-auto">
                                    <form method="dialog">
                                        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                    </form>

                                    <h3 class="text-lg font-semibold text-center mb-3">Media Image Details</h3>

                                    <img src="{{ $media['image'] }}"
                                        class="w-full h-auto rounded-lg object-contain mb-4 border border-base-300">

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <div>
                                            <label class="text-sm">ID</label>
                                            <input type="text" class="input w-full border-base-300"
                                                value="{{ $media['id'] }}" readonly>
                                        </div>

                                        <div>
                                            <label class="text-sm">Title</label>
                                            <input type="text" class="input w-full border-base-300"
                                                value="{{ $media['title'] ?? '-' }}" readonly>
                                        </div>

                                        <div>
                                            <label class="text-sm">Type</label>
                                            <input type="text" class="input w-full border-base-300"
                                                value="{{ ucfirst($media['type']) ?? '-' }}" readonly>
                                        </div>

                                        <div>
                                            <label class="text-sm">Priority</label>
                                            <input type="number" class="input w-full border-base-300"
                                                value="{{ $media['priority'] ?? 0 }}" readonly>
                                        </div>

                                        <div>
                                            <label class="text-sm">Active Status</label>
                                            <input type="text" class="input w-full border-base-300"
                                                value="{{ $media['is_active'] ? 'Active' : 'Inactive' }}" readonly>
                                        </div>

                                        <div>
                                            <label class="text-sm">Start At</label>
                                            <input type="datetime-local" class="input w-full border-base-300"
                                                value="{{ $media['start_at'] ? \Carbon\Carbon::parse($media['start_at'])->format('Y-m-d\TH:i') : '' }}"
                                                readonly>
                                        </div>

                                        <div>
                                            <label class="text-sm">End At</label>
                                            <input type="datetime-local" class="input w-full border-base-300"
                                                value="{{ $media['end_at'] ? \Carbon\Carbon::parse($media['end_at'])->format('Y-m-d\TH:i') : '' }}"
                                                readonly>
                                        </div>

                                        <div class="md:col-span-2">
                                            <label class="text-sm">Link</label>
                                            @if (!empty($media['link']))
                                                <input type="text"
                                                    class="input w-full border-base-300 text-blue-600 underline"
                                                    value="{{ $media['link'] }}" readonly
                                                    onclick="window.open(this.value,'_blank')">
                                            @else
                                                <input type="text" class="input w-full border-base-300" value="-"
                                                    readonly>
                                            @endif
                                        </div>

                                        <div class="md:col-span-2">
                                            <label class="text-sm">Created At</label>
                                            <input type="text" class="input w-full border-base-300"
                                                value="{{ \Carbon\Carbon::parse($media['created_at'])->format('Y-m-d h:i A') }}"
                                                readonly>
                                        </div>

                                        <div class="md:col-span-2">
                                            <label class="text-sm">Updated At</label>
                                            <input type="text" class="input w-full border-base-300"
                                                value="{{ \Carbon\Carbon::parse($media['updated_at'])->format('Y-m-d h:i A') }}"
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
                            <dialog id="edit_modal_{{ $media['id'] }}" class="modal">
                                <div class="modal-box max-w-2xl max-h-[85vh] overflow-y-auto">
                                    <form method="dialog">
                                        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                    </form>
                                    <h3 class="text-lg font-semibold text-center mb-3">Edit Media Image</h3>
                                    <form method="POST"
                                        action="{{ route('admin.dashboard.media-image.id.post', $media['id']) }}"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                            <div class="flex flex-col gap-1">
                                                <label>Title</label>
                                                <input type="text" name="title" class="input w-full"
                                                    value="{{ $media['title'] }}">
                                            </div>
                                            <div class="flex flex-col gap-1">
                                                <label>Type</label>
                                                <select name="type" class="select w-full" required>
                                                    <option value="carousel_slider" @selected($media['type'] == 'carousel_slider')>Landing
                                                        Slider (Carousel)</option>
                                                    <option value="landing_pop_up" @selected($media['type'] == 'landing_pop_up')>Landing Pop
                                                        Up</option>
                                                    <option value="side_banner" @selected($media['type'] == 'side_banner')>Side Banner
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="md:col-span-2 flex flex-col gap-1">
                                                <label>Link (Optional)</label>
                                                <input type="url" name="link" class="input w-full"
                                                    value="{{ $media['link'] ?? '' }}">
                                            </div>
                                            <div>
                                                <label>Priority</label>
                                                <input type="number" name="priority" class="input w-full"
                                                    value="{{ $media['priority'] ?? 0 }}">
                                            </div>
                                            <div>
                                                <label>Active</label>
                                                <select name="is_active" class="select w-full">
                                                    <option value="1" {{ $media['is_active'] ? 'selected' : '' }}>
                                                        Enabled
                                                    </option>
                                                    <option value="0" {{ !$media['is_active'] ? 'selected' : '' }}>
                                                        Disabled
                                                    </option>
                                                </select>
                                            </div>
                                            <div>
                                                <label>Start From</label>
                                                <input type="datetime-local" name="start_at"
                                                    value="{{ old('start_at', isset($media['start_at']) ? $media['start_at']->format('Y-m-d\TH:i') : '') }}"
                                                    class="input input-bordered w-full" />
                                            </div>
                                            <div>
                                                <label>End At</label>
                                                <input type="datetime-local" name="end_at"
                                                    value="{{ old('end_at', isset($media['end_at']) ? $media['end_at']->format('Y-m-d\TH:i') : '') }}"
                                                    class="input input-bordered w-full" />
                                            </div>
                                            <div class="md:col-span-2 flex flex-col gap-2">
                                                <label>Image (Optional)</label>
                                                <input type="file" name="image" accept="image/*"
                                                    class="file-input w-full" />
                                                @if ($media['image'])
                                                    <img src="{{ $media['image'] }}"
                                                        class="w-[200px] max-h-[300px] object-contain border rounded-lg">
                                                @endif
                                                <label class="label cursor-pointer flex items-center">
                                                    <input type="hidden" name="remove_image" value="0">
                                                    <input type="checkbox" class="checkbox checkbox-xs"
                                                        name="remove_image" value="1">
                                                    <span class="label-text text-sm">Remove image</span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="modal-action mt-3">
                                            <button type="submit" class="btn btn-primary">Update Media Image</button>
                                        </div>
                                    </form>
                                </div>
                            </dialog>

                            <!-- Delete Modal -->
                            <dialog id="delete_modal_{{ $media['id'] }}" class="modal">
                                <div class="modal-box">
                                    <form method="dialog">
                                        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                    </form>
                                    <p class="text-lg font-semibold">Confirm Delete</p>
                                    <p class="text-sm mb-4">Are you sure you want to delete this media image?</p>
                                    <div class="modal-action">
                                        <form method="dialog"><button class="btn">Cancel</button></form>
                                        <form method="POST"
                                            action="{{ route('admin.dashboard.media-image.id.delete', $media['id']) }}">
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
                        <span class="font-semibold">{{ $mediaImages->firstItem() }}</span>
                        –
                        <span class="font-semibold">{{ $mediaImages->lastItem() }}</span>
                        of
                        <span class="font-semibold">{{ $mediaImages->total() }}</span>
                        results
                    </div>

                    <div class="join">
                        @if ($mediaImages->onFirstPage())
                            <button class="join-item btn btn-sm btn-disabled">«</button>
                        @else
                            <a href="{{ $mediaImages->previousPageUrl() }}" class="join-item btn btn-sm">«</a>
                        @endif

                        @for ($i = 1; $i <= $mediaImages->lastPage(); $i++)
                            <a href="{{ $mediaImages->url($i) }}"
                                class="join-item btn btn-sm {{ $mediaImages->currentPage() === $i ? 'btn-active' : '' }}">{{ $i }}</a>
                        @endfor

                        @if ($mediaImages->hasMorePages())
                            <a href="{{ $mediaImages->nextPageUrl() }}" class="join-item btn btn-sm">»</a>
                        @else
                            <button class="join-item btn btn-sm btn-disabled">»</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Create Modal -->
        <dialog id="create_media_modal" class="modal">
            <div class="modal-box max-w-2xl max-h-[85vh] overflow-y-auto">
                <form method="dialog">
                    <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                </form>
                <h3 class="text-lg font-semibold text-center mb-3">Create Media Image</h3>
                <form method="POST" action="{{ route('admin.dashboard.store.media-image.post') }}"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div class="flex flex-col gap-1">
                            <label>Title</label>
                            <input type="text" name="title" class="input w-full">
                        </div>
                        <div class="flex flex-col gap-1">
                            <label>Type</label>
                            <select name="type" class="select w-full" required>
                                <option value="carousel_slider">Landing
                                    Slider (Carousel)</option>
                                <option value="landing_pop_up">Landing Pop
                                    Up</option>
                                <option value="side_banner">Side Banner
                                </option>
                            </select>
                        </div>
                        <div class="md:col-span-2 flex flex-col gap-1">
                            <label>Link (Optional)</label>
                            <input type="url" name="link" class="input w-full">
                        </div>
                        <div>
                            <label>Priority</label>
                            <input type="number" name="priority" class="input w-full" value="0">
                        </div>
                        <div>
                            <label>Active</label>
                            <select name="is_active" class="select w-full">
                                <option value="1" selected>Enabled</option>
                                <option value="0">Disabled</option>
                            </select>
                        </div>
                        <div>
                            <label>Start From</label>
                            <input type="datetime-local" name="start_at"
                                value="{{ old('start_at', isset($edit_product['start_at']) ? $edit_product['start_at']->format('Y-m-d\TH:i') : '') }}"
                                class="input input-bordered w-full" />
                        </div>
                        <div>
                            <label>End At</label>
                            <input type="datetime-local" name="end_at"
                                value="{{ old('end_at', isset($edit_product['end_at']) ? $edit_product['end_at']->format('Y-m-d\TH:i') : '') }}"
                                class="input input-bordered w-full" />
                        </div>
                        <div class="md:col-span-2 flex flex-col gap-1">
                            <label>Image</label>
                            <input type="file" name="image" accept="image/*" class="file-input w-full" required>
                        </div>
                    </div>
                    <div class="modal-action mt-3">
                        <button type="submit" class="btn btn-primary">Create Media Image</button>
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
                        Toast.show('Please select at least one brand', {
                            type: 'error'
                        });
                        return;
                    }
                    const modal = document.getElementById(this.current_action);
                    if (modal) modal.showModal();
                },
            });

            Alpine.store('media_image_search_setting', {
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
                    const savedSetting = JSON.parse(localStorage.getItem('media_image_search_setting') ??
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
                    localStorage.setItem('media_image_search_setting', JSON.stringify(data));
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

            Alpine.store('media_image_search_setting').init();
        });
    </script>
@endpush
