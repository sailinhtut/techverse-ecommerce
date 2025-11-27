@php
    $site_name = getParsedTemplate('site_name');
    $site_logo = getSiteLogoURL();
@endphp

@extends('layouts.user.user_dashboard')

@section('user_dashboard_content')
    <div class="p-3 lg:p-5">
        <p class="lg:text-lg font-semibold">Wish List</p>
        <div class="mt-3 card shadow-sm border border-base-300">
            <div class="card-body p-0 m-0 overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="w-[50px] hidden md:block">No.</th>
                            {{-- <th class="w-[50px]">Image</th> --}}
                            <th class="">Name</th>
                            {{-- <th class="w-[200px]">Description</th> --}}
                            {{-- <th class="">Note</th>
                            <th class="">Added At</th> --}}
                            <th class=""></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($wishlists as $wishlist)
                            <tr>
                                <td style="" class="w-[50px] hidden md:block">
                                    {{ $loop->iteration + ($wishlists->currentPage() - 1) * $wishlists->perPage() }}.
                                </td>

                                <td class="min-w-[200px]">
                                    <div class="flex flex-row flex-nowrap items-start gap-3">
                                        <div class="w-[50px] shrink-0">
                                            @if ($wishlist['product']['image'])
                                                <img src="{{ $wishlist['product']['image'] }}"
                                                    alt="{{ $wishlist['product']['name'] }}"
                                                    class="w-[50px] h-auto object-contain  border border-base-300">
                                            @else
                                                <img src="{{ $site_logo }}" alt="{{ $wishlist['product']['name'] }}"
                                                    class="w-[50px] h-auto object-contain   border border-base-300">
                                            @endif
                                        </div>
                                        <a href="{{ route('shop.slug.get', ['slug' => $wishlist['product']['slug']]) }}"
                                            class="flex flex-col">
                                            <span
                                                class="font-semibold cursor-default hover:underline">{{ $wishlist['product']['name'] }}</span>
                                            <span
                                                class="line-clamp-2">{{ $wishlist['product']['short_description'] ?? 'No Description' }}</span>
                                        </a>
                                    </div>


                                </td>

                                {{-- <td class="max-w-[200px] truncate">{{ $wishlist['product']['short_description'] ?? '-' }}
                                </td> --}}
                                {{-- <td>{{ $wishlist['note'] ?? '-' }}</td> --}}
                                {{-- <td>{{ $wishlist['created_at'] ?? '-' }}</td> --}}
                                <td class="">
                                    <div tabindex="0" role="button" class="dropdown dropdown-left">
                                        <div class="btn btn-square btn-sm btn-ghost">
                                            <i data-lucide="ellipsis-vertical" class="size-5"></i>
                                        </div>
                                        <ul tabindex="0"
                                            class="menu dropdown-content bg-base-100 border border-base-300 w-30 rounded-box p-1 shadow-sm">
                                            <li>
                                                <button type="button" class="text-error"
                                                    onclick="document.getElementById('deleteModal{{ $wishlist['id'] }}').showModal()">
                                                    Delete
                                                </button>
                                            </li>
                                        </ul>
                                    </div>



                                    <dialog id="deleteModal{{ $wishlist['id'] }}" class="modal">
                                        <div class="modal-box">
                                            <form method="dialog">
                                                <button
                                                    class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                            </form>
                                            <p class="text-lg font-semibold py-0">Confirm Delete</p>

                                            <p class="py-2 mb-0 text-sm">
                                                Are you sure you want to delete
                                                <span class="italic text-error">{{ $wishlist['product']['name'] }}</span> ?
                                            </p>
                                            <div class="modal-action mt-0">
                                                <form method="dialog">
                                                    <button class="btn  lg:btn-md">Close</button>
                                                </form>
                                                <form method="POST"
                                                    action="{{ route('wishlist.id.delete', ['id' => $wishlist['id']]) }}">
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
                        <span class="font-semibold">{{ $wishlists->firstItem() }}</span>
                        –
                        <span class="font-semibold">{{ $wishlists->lastItem() }}</span>
                        of
                        <span class="font-semibold">{{ $wishlists->total() }}</span>
                        results
                    </div>

                    <div class="join">
                        @if ($wishlists->onFirstPage())
                            <button class="join-item btn btn-sm btn-disabled">«</button>
                        @else
                            <a href="{{ $wishlists->previousPageUrl() }}" class="join-item btn btn-sm">«</a>
                        @endif

                        <a href="{{ $wishlists->url(1) }}"
                            class="join-item btn btn-sm {{ $wishlists->currentPage() === 1 ? 'btn-active' : '' }}">
                            1
                        </a>

                        @php
                            $start = max(2, $wishlists->currentPage() - 1);
                            $end = min($wishlists->lastPage() - 1, $wishlists->currentPage() + 1);
                        @endphp

                        @for ($i = $start; $i <= $end; $i++)
                            <a href="{{ $wishlists->url($i) }}"
                                class="join-item btn btn-sm {{ $wishlists->currentPage() === $i ? 'btn-active' : '' }}">
                                {{ $i }}
                            </a>
                        @endfor

                        @if ($wishlists->lastPage() > 1)
                            <a href="{{ $wishlists->url($wishlists->lastPage()) }}"
                                class="join-item btn btn-sm {{ $wishlists->currentPage() === $wishlists->lastPage() ? 'btn-active' : '' }}">
                                {{ $wishlists->lastPage() }}
                            </a>
                        @endif

                        @if ($wishlists->hasMorePages())
                            <a href="{{ $wishlists->nextPageUrl() }}" class="join-item btn btn-sm">»</a>
                        @else
                            <button class="join-item btn btn-sm btn-disabled">»</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
