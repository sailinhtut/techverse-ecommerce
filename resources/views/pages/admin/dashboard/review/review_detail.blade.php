@extends('layouts.admin.admin_dashboard')

@section('admin_dashboard_content')
    <div class="p-5 min-h-screen">
        <div class="mb-4">
            <button onclick="history.back()" class="btn btn-sm flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Back
            </button>
        </div>
        <div class="w-fit max-w-full text-sm bg-base-300 rounded px-2 overflow-x-auto ">
            <div class="breadcrumbs text-sm my-0 py-1">
                <ul>
                    <li>
                        <a href="{{ route('admin.dashboard.product.review.get') }}" class="btn btn-xs btn-ghost">
                            Reviews
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.dashboard.product.review.id.get', ['id' => $review['id']]) }}"
                            class="btn btn-xs btn-ghost">
                            Review #{{ $review['id'] }}
                        </a>
                    </li>
                </ul>

            </div>
        </div>

        <p class="lg:text-lg font-semibold mt-5">Review Detail</p>
        <div class="flex flex-wrap gap-3 mt-3">
            <a class="btn btn-sm" href="{{ route('admin.dashboard.product.edit.id.get', $review['product_id']) }}">See
                Product
                [#{{ $review['product_id'] }}]</a>
            @if (isset($review['order_id']))
                <a class="btn btn-sm" href="{{ route('admin.dashboard.order.id.get', $review['order_id']) }}">See Order
                    [#{{ $review['order_id'] }}]</a>
            @endif
        </div>

        <!-- Tabs -->
        <div class="tabs tabs-box bg-base-100 shadow-none mt-7">
            <input type="radio" name="review_tabs" id="tab_detail" class="tab" aria-label="Review Detail" checked />
            <div class="tab-content">
                <div class="border border-base-300 rounded-box p-5 mt-5">
                    <p class="font-semibold mb-2">Review Information</p>
                    <div class="w-full grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        <div class="flex flex-col gap-1">
                            <label class="text-sm">Product</label>
                            <input type="text" class="input input-bordered w-full cursor-default select-none"
                                value="{{ $review['product']['name'] ?? 'N/A' }}" readonly>
                        </div>

                        <div class="flex flex-col gap-1">
                            <label class="text-sm">User</label>
                            <input type="text" class="input input-bordered w-full cursor-default select-none"
                                value="{{ $review['user']['name'] ?? 'Guest' }}" readonly>
                        </div>

                        <div class="flex flex-col gap-1">
                            <label class="text-sm">Rating</label>
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
                        </div>

                        <div class="flex flex-col gap-1">
                            <label class="text-sm">Approved</label>
                            <input type="text" class="input input-bordered w-full cursor-default select-none"
                                value="{{ $review['is_approved'] ? 'Yes' : 'No' }}" readonly>
                        </div>

                        <div class="flex flex-col gap-1">
                            <label class="text-sm">Created At</label>
                            <input type="text" class="input input-bordered w-full cursor-default select-none"
                                value="{{ $review['created_at'] ? \Carbon\Carbon::parse($review['created_at'])->format('Y-m-d H:i') : ''}}" readonly>
                        </div>

                        <div class="md:col-span-2 lg:col-span-3 flex flex-col gap-1">
                            <label class="text-sm">Comment</label>
                            <textarea class="textarea textarea-bordered w-full cursor-default select-none" readonly>{{ $review['comment'] }}</textarea>
                        </div>

                        @if ($review['image'])
                            <div class="md:col-span-2 flex flex-col gap-1 mt-2">
                                <label class="text-sm">Image</label>
                                <img src="{{ $review['image'] }}"
                                    class="w-[200px] max-h-[300px] border border-base-300 rounded-lg object-contain cursor-pointer"
                                    onclick="document.getElementById('image_modal').showModal()">
                            </div>
                        @endif


                    </div>
                </div>

                <div class="border border-base-300 rounded-box p-5 mt-5">
                    <p class="font-semibold mb-2">User Information</p>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="text-sm">User ID</label>
                            <input type="text"
                                class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                value="{{ $review['user']['id'] }}" readonly>
                        </div>

                        <div>
                            <label class="text-sm">Full Name</label>
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
                            <label class="text-sm">Role</label>
                            <input type="text"
                                class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                value="{{ $review['user']['role']['display_name'] ?? 'N/A' }}" readonly>
                        </div>

                        <div>
                            <label class="text-sm">Phone (Primary)</label>
                            <input type="text"
                                class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                value="{{ $review['user']['phone_one'] ?? '-' }}" readonly>
                        </div>

                        <div>
                            <label class="text-sm">Phone (Secondary)</label>
                            <input type="text"
                                class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                value="{{ $review['user']['phone_two'] ?? '-' }}" readonly>
                        </div>

                        <div>
                            <label class="text-sm">Date of Birth</label>
                            <input type="text"
                                class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                value="{{ $review['user']['date_of_birth'] ?? '-' }}" readonly>
                        </div>

                        <div>
                            <label class="text-sm">Email Verified At</label>
                            <input type="text"
                                class="input w-full focus:outline-none focus:ring-0 focus:border-base-300 cursor-default select-none"
                                value="{{ $review['user']['email_verified_at'] ? \Carbon\Carbon::parse($review['user']['email_verified_at'])->format('Y-m-d H:i') : 'Not Verified' }}"
                                readonly>
                        </div>

                        <div>
                            <label class="text-sm">Account Created</label>
                            <input type="text"
                                class="input w-full focus:outline-none focus:ring-0 focus-border-base-300 cursor-default select-none"
                                value="{{ $review['user']['created_at'] ? \Carbon\Carbon::parse($review['user']['created_at'])->format('Y-m-d H:i') : '' }}" readonly>
                        </div>

                        <div>
                            <label class="text-sm">Permissions</label>
                            <textarea readonly rows="2"
                                class="textarea w-full focus:outline-none focus:ring-0 focus-border-base-300 cursor-default select-none">{{ implode(', ', $review['user']['role']['permissions']) ?: 'No Permissions' }}
                            </textarea>
                        </div>
                    </div>
                </div>


            </div>

            <!-- REPLY TAB -->
            <input type="radio" name="review_tabs" id="tab_replies" class="tab" aria-label="Replies" />
            <div class="tab-content">

                <div class="flex justify-between items-center mb-3">
                    <p class="font-semibold text-sm lg:text-base">Replies</p>
                    <button class="btn btn-sm btn-primary"
                        onclick="document.getElementById('create_reply_modal').showModal()">Add Reply</button>
                </div>

                <div class="card border border-base-300 shadow-sm overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>User</th>
                                <th>Comment</th>
                                <th>Updated At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($replies as $reply)
                                <tr>
                                    <td>{{ $loop->iteration + ($replies->currentPage() - 1) * $replies->perPage() }}</td>
                                    <td>{{ $reply['user']['name'] ?? 'Unknown' }}</td>
                                    <td class="truncate max-w-[300px]">{{ $reply['reply'] }}</td>
                                    <td>{{ $reply['updated_at'] ? \Carbon\Carbon::parse($reply['updated_at'])->format('Y-m-d H:i'):'-'}}</td>
                                    <td>
                                        <div tabindex="0" role="button" class="dropdown dropdown-left">
                                            <div class="btn btn-sm btn-ghost">
                                                <i data-lucide="ellipsis-vertical" class="size-4"></i>
                                            </div>
                                            <ul tabindex="0"
                                                class="menu dropdown-content bg-base-100 border border-base-300 rounded-box w-32 shadow-sm p-1">
                                                <li><button
                                                        onclick="document.getElementById('detail_reply_modal_{{ $reply['id'] }}').showModal()">View</button>
                                                </li>
                                                <li><button class="text-error"
                                                        onclick="document.getElementById('delete_reply_modal_{{ $reply['id'] }}').showModal()">Delete</button>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>

                                <!-- REPLY DETAIL MODAL -->
                                <dialog id="detail_reply_modal_{{ $reply['id'] }}" class="modal">
                                    <div class="modal-box max-w-xl">
                                        <form method="dialog">
                                            <button
                                                class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                        </form>
                                        <h3 class="font-semibold mb-3 text-center">Reply Detail</h3>
                                        <div class="space-y-3">
                                            <div>
                                                <label class="text-sm">User</label>
                                                <input type="text" class="input input-bordered w-full"
                                                    value="{{ $reply['user']['name'] ?? 'Unknown' }}" readonly>
                                            </div>
                                            <div>
                                                <label class="text-sm">Comment</label>
                                                <textarea class="textarea textarea-bordered w-full" rows="4" readonly>{{ $reply['reply'] }}</textarea>
                                            </div>
                                            <div>
                                                <label class="text-sm">Updated At</label>
                                                <input type="text" class="input input-bordered w-full"
                                                    value="{{ $reply['updated_at'] ? \Carbon\Carbon::parse($reply['updated_at'])->format('Y-m-d H:i') : '' }}"
                                                    readonly>
                                            </div>
                                        </div>
                                        <div class="modal-action mt-4">
                                            <form method="dialog"><button class="btn">Close</button></form>
                                        </div>
                                    </div>
                                </dialog>

                                <!-- DELETE REPLY MODAL -->
                                <dialog id="delete_reply_modal_{{ $reply['id'] }}" class="modal">
                                    <div class="modal-box">
                                        <form method="dialog">
                                            <button
                                                class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                        </form>
                                        <p class="text-lg font-semibold mb-2">Confirm Delete</p>
                                        <p class="text-sm mb-4">Delete this reply from
                                            <strong>{{ $reply['user']['name'] ?? 'Unknown' }}</strong>?
                                        </p>
                                        <div class="modal-action">
                                            <form method="dialog"><button class="btn">Cancel</button></form>
                                            <form method="POST"
                                                action="{{ route('admin.dashboard.product.review.review_id.reply.reply_id.delete', ['review_id' => $review['id'], 'reply_id' => $reply['id']]) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-error">Delete</button>
                                            </form>
                                        </div>
                                    </div>
                                </dialog>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-gray-500 py-5">No replies yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{-- Pagination --}}
                    <div class="flex justify-between items-center py-3 px-5">
                        <div class="text-sm text-gray-500">
                            @if ($replies->total() > 0)
                                <span class="font-semibold">{{ $replies->firstItem() }}</span> –
                                <span class="font-semibold">{{ $replies->lastItem() }}</span> of
                                <span class="font-semibold">{{ $replies->total() }}</span> replies
                            @else
                                <span>No data</span>
                            @endif
                        </div>
                        <div class="join">
                            @if ($replies->onFirstPage())
                                <button class="join-item btn btn-sm btn-disabled">«</button>
                            @else
                                <a href="{{ $replies->previousPageUrl() }}" class="join-item btn btn-sm">«</a>
                            @endif

                            @for ($i = 1; $i <= $replies->lastPage(); $i++)
                                <a href="{{ $replies->url($i) }}"
                                    class="join-item btn btn-sm {{ $replies->currentPage() === $i ? 'btn-active' : '' }}">{{ $i }}</a>
                            @endfor

                            @if ($replies->hasMorePages())
                                <a href="{{ $replies->nextPageUrl() }}" class="join-item btn btn-sm">»</a>
                            @else
                                <button class="join-item btn btn-sm btn-disabled">»</button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CREATE REPLY MODAL -->
    <dialog id="create_reply_modal" class="modal">
        <div class="modal-box max-w-xl">
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
            </form>

            <h3 class="text-lg font-semibold text-center mb-3">Add Reply</h3>
            <form method="POST"
                action="{{ route('admin.dashboard.product.review.review_id.reply.post', ['review_id' => $review['id']]) }}">
                @csrf
                <input type="hidden" name="review_id" value="{{ $review['id'] }}">
                <div class="flex flex-col gap-2">
                    <label class="text-sm">Comment</label>
                    <textarea name="reply" class="textarea textarea-bordered w-full" rows="4" required></textarea>
                </div>
                <div class="modal-action mt-3">
                    <button type="submit" class="btn btn-primary w-full">Submit Reply</button>
                </div>
            </form>
        </div>
    </dialog>

    @if ($review['image'])
        <!-- IMAGE PREVIEW MODAL -->
        <dialog id="image_modal" class="modal">
            <div class="modal-box max-w-3xl max-h-[85vh] overflow-y-auto">
                <form method="dialog">
                    <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                </form>
                <img src="{{ $review['image'] }}" class="w-full rounded-lg object-contain" />
            </div>
        </dialog>
    @endif
@endsection
