@php
    $site_name = getParsedTemplate('site_name');
    $site_logo = getSiteLogoURL();
@endphp

@extends('layouts.user.user_dashboard')

@section('user_dashboard_content')
    <div class="p-3 lg:p-5">
        <p class="lg:text-lg font-semibold ">Notifications</p>

        <div class="card shadow-sm border border-base-300 mt-3">
            <div class="card-body p-0 m-0 overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="w-[50px]">No.</th>
                            <th class="">Notification</th>
                            <th style="width:180px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($notifications as $notification)
                            <tr>
                                <td style="" class="">
                                    {{ $loop->iteration + ($notifications->currentPage() - 1) * $notifications->perPage() }}.
                                </td>

                                <td class="">
                                    <div class="flex flex-row gap-3">
                                        <div class="w-[45px] h-full flex-shrink-0">
                                            @if ($notification['image'])
                                                <img src="{{ $notification['image'] }}" alt="{{ $notifications['title'] }}"
                                                    class="w-[45px] h-auto object-contain border border-base-300">
                                            @else
                                                <img src="{{ $site_logo }}" alt="{{ $notification['title'] }}"
                                                    class="w-[45px] h-auto object-contain border border-base-300">
                                            @endif
                                        </div>

                                        <a href="{{ $notification['link'] }}" class="flex flex-col">
                                            <span
                                                class="font-semibold cursor-default hover:underline">{{ $notification['title'] }}</span>
                                            <span class="line-clamp-2">{{ $notification['message'] ?? 'No Message' }}</span>
                                            <span
                                                class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($notification['created_at'])->diffForHumans() }}
                                                at
                                                {{ $notification['created_at'] ? \Carbon\Carbon::parse($notification['created_at'])->format('Y-m-d h:i A') : '-' }}</span>
                                        </a>
                                    </div>
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
                                                    onclick="document.getElementById('detailModal{{ $notification['id'] }}').showModal()">
                                                    View
                                                </button>
                                            </li>
                                            <li>
                                                <button type="button" class="text-error"
                                                    onclick="document.getElementById('deleteModal{{ $notification['id'] }}').showModal()">
                                                    Delete
                                                </button>
                                            </li>
                                        </ul>
                                    </div>


                                    <dialog id="detailModal{{ $notification['id'] }}" class="modal">
                                        <div class="modal-box max-h-[80vh]">
                                            <form method="dialog">
                                                <button
                                                    class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                            </form>
                                            <p class="text-lg font-semibold py-0">{{ $notification['title'] }}</p>
                                            <div class="mt-4 space-y-2">
                                                @if ($notification['image'])
                                                    <img src="{{ $notification['image'] }}"
                                                        alt="{{ $notification['title'] }}"
                                                        class="w-[100px] h-auto object-contain border border-base-300">
                                                @else
                                                    <img src="{{ $site_logo }}" alt="{{ $notification['title'] }}"
                                                        class="w-[100px] h-auto object-contain border border-base-300">
                                                @endif
                                                <p class="text-sm text-gray-500">
                                                    Sent:
                                                    {{ $notification['created_at'] ? \Carbon\Carbon::parse($notification['updated_at'])->format('Y-m-d h:i A') : '-' }} 
                                                    ({{ \Carbon\Carbon::parse($notification['created_at'])->diffForHumans() }})
                                                </p>
                                                <p class="white-space-pre-wrap"><strong>
                                                        {{ $notification['title'] ?? 'No Title' }}</strong></p>
                                                <p class="white-space-pre-wrap text-justify">
                                                    {{ $notification['message'] ?? 'Message' }}</p>
                                            </div>
                                            <div class="modal-action mt-3">
                                                <form method="dialog">
                                                    <button class="btn lg:btn-md">Close</button>
                                                </form>
                                            </div>
                                        </div>

                                    </dialog>


                                    <dialog id="deleteModal{{ $notification['id'] }}" class="modal">
                                        <div class="modal-box">
                                            <form method="dialog">
                                                <button
                                                    class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                            </form>
                                            <p class="text-lg font-semibold py-0">Confirm Delete</p>

                                            <p class="py-2 mb-0 text-sm">
                                                Are you sure you want to delete
                                                <span class="italic text-error">{{ $notification['title'] }}</span> ?
                                            </p>
                                            <div class="modal-action mt-0">
                                                <form method="dialog">
                                                    <button class="btn  lg:btn-md">Close</button>
                                                </form>
                                                <form method="POST"
                                                    action="{{ route('notification.id.delete', ['id' => $notification['id']]) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn  lg:btn-md btn-error">Delete</button>
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
                        <span class="font-semibold">{{ $notifications->firstItem() }}</span>
                        –
                        <span class="font-semibold">{{ $notifications->lastItem() }}</span>
                        of
                        <span class="font-semibold">{{ $notifications->total() }}</span>
                        results
                    </div>

                    <div class="join">
                        @if ($notifications->onFirstPage())
                            <button class="join-item btn btn-sm btn-disabled">«</button>
                        @else
                            <a href="{{ $notifications->previousPageUrl() }}" class="join-item btn btn-sm">«</a>
                        @endif

                        <a href="{{ $notifications->url(1) }}"
                            class="join-item btn btn-sm {{ $notifications->currentPage() === 1 ? 'btn-active' : '' }}">
                            1
                        </a>

                        @php
                            $start = max(2, $notifications->currentPage() - 1);
                            $end = min($notifications->lastPage() - 1, $notifications->currentPage() + 1);
                        @endphp

                        @for ($i = $start; $i <= $end; $i++)
                            <a href="{{ $notifications->url($i) }}"
                                class="join-item btn btn-sm {{ $notifications->currentPage() === $i ? 'btn-active' : '' }}">
                                {{ $i }}
                            </a>
                        @endfor

                        @if ($notifications->lastPage() > 1)
                            <a href="{{ $notifications->url($notifications->lastPage()) }}"
                                class="join-item btn btn-sm {{ $notifications->currentPage() === $notifications->lastPage() ? 'btn-active' : '' }}">
                                {{ $notifications->lastPage() }}
                            </a>
                        @endif

                        @if ($notifications->hasMorePages())
                            <a href="{{ $notifications->nextPageUrl() }}" class="join-item btn btn-sm">»</a>
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
            Alpine.store('notification').markAsRead([]);
        })
    </script>
@endpush
