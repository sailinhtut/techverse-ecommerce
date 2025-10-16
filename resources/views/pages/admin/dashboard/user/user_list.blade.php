@extends('layouts.admin.admin_dashboard')
@section('admin_dashboard_content')
    <div class="p-5 min-h-screen ">
        <p class="lg:text-lg font-semibold mb-3">User List</p>

        <div class="card overflow-x-auto shadow-sm border border-base-300">
            <div class="card-body p-0 m-0 ">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="w-[50px]">No.</th>
                            <th class="w-[150px]">Profile</th>
                            <th class="w-[180px]">Name</th>
                            <th class="w-[200px]">Email</th>
                            <th class="w-[150px]">Phone</th>
                            <th class="w-[150px]">Role</th>
                            <th class="w-[150px]">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>{{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}.</td>

                                <td>
                                    <div class="avatar">
                                        <div class="w-5 rounded-full ring ring-base-300 ring-offset-base-100 ring-offset-2">
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
                                            <li>
                                                <button type="button" class="text-error"
                                                    onclick="document.getElementById('delete_modal_{{ $user['id'] }}').showModal()">
                                                    Delete
                                                </button>
                                            </li>
                                        </ul>
                                    </div>

                                    {{-- DETAIL MODAL --}}
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

                                            <div class="text-sm space-y-2">
                                                <p><strong>ID:</strong> {{ $user['id'] }}</p>
                                                <p><strong>Name:</strong> {{ $user['name'] }}</p>
                                                <p><strong>Email:</strong> {{ $user['email'] }}</p>
                                                <p><strong>Phone 1:</strong> {{ $user['phone_one'] ?? '-' }}</p>
                                                <p><strong>Phone 2:</strong> {{ $user['phone_two'] ?? '-' }}</p>
                                                <p><strong>Email Verified:</strong>
                                                    @if ($user['email_verified_at'])
                                                        <span class="badge badge-success badge-outline">Verified</span>
                                                    @else
                                                        <span class="badge badge-warning badge-outline">Unverified</span>
                                                    @endif
                                                </p>
                                                <p><strong>Created At:</strong>
                                                    {{ \Carbon\Carbon::parse($user['created_at'])->format('Y-m-d H:i') }}
                                                </p>

                                                <div>
                                                    <p class="mb-2"><strong>Role Information</strong></p>
                                                    @if ($user['role'])
                                                        <div class="bg-base-200 rounded-box p-3 text-sm">
                                                            <p><strong>Role Name:</strong> {{ $user['role']['name'] }}</p>
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

                                    {{-- DELETE DIALOG --}}
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

                {{-- PAGINATION --}}
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
    </div>
@endsection
