@extends('layouts.admin.admin_dashboard')

@section('admin_dashboard_content')
    <div class="p-5 min-h-screen">
        <p class="lg:text-lg font-semibold mb-3">Media Images</p>

        <div class="flex gap-3 mb-3">
            <button class="btn btn-primary" onclick="document.getElementById('create_media_modal').showModal()">Create Media
                Image</button>
        </div>

        <div class="card shadow-sm border border-base-300 mt-4">
            <div class="card-body p-0 m-0 overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Image</th>
                            <th>Priority</th>
                            <th>Active</th>
                            <th>Start From</th>
                            <th>End At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($mediaImages as $media)
                            <tr>
                                <td>{{ $loop->iteration + ($mediaImages->currentPage() - 1) * $mediaImages->perPage() }}
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
                                <td>{{ \Carbon\Carbon::parse($media['start_at'])->format('Y-m-d H:i') }}</td>
                                <td>{{ \Carbon\Carbon::parse($media['end_at'])->format('Y-m-d H:i') }}</td>
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
                                                value="{{ \Carbon\Carbon::parse($media['created_at'])->format('Y-m-d H:i') }}"
                                                readonly>
                                        </div>

                                        <div class="md:col-span-2">
                                            <label class="text-sm">Updated At</label>
                                            <input type="text" class="input w-full border-base-300"
                                                value="{{ \Carbon\Carbon::parse($media['updated_at'])->format('Y-m-d H:i') }}"
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
