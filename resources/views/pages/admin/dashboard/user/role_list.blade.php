@extends('layouts.admin.admin_dashboard')
@section('admin_dashboard_content')
    <div class="p-5 min-h-screen">
        <p class="lg:text-lg font-semibold mb-3">User Roles</p>

        <div class="card shadow-sm border border-base-300">
            <div class="card-body p-0 m-0 overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="w-[50px]">No.</th>
                            <th class="w-[200px]">Role Name</th>
                            <th class="w-[200px]">Type</th>
                            <th class="w-[300px]">Description</th>
                            <th class="w-[150px]">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($roles as $role)
                            <tr>
                                <td>{{ $loop->iteration + ($roles->currentPage() - 1) * $roles->perPage() }}.</td>
                                <td>{{ $role['display_name'] ?? '-' }}</td>
                                <td>{{ $role['name'] }}</td>
                                <td>{{ $role['description'] ?? '-' }}</td>

                                <td>
                                    <div tabindex="0" role="button" class="dropdown dropdown-left">
                                        <div class="btn btn-square btn-sm btn-ghost">
                                            <i data-lucide="ellipsis-vertical" class="size-5"></i>
                                        </div>
                                        <ul tabindex="0"
                                            class="menu dropdown-content bg-base-100 border border-base-300 w-30 rounded-box p-1 shadow-sm">
                                            <li>
                                                <button
                                                    onclick="document.getElementById('detail_modal_{{ $role['id'] }}').showModal()">
                                                    View Details
                                                </button>
                                            </li>

                                            <li>
                                                <button
                                                    onclick="document.getElementById('perm_modal_{{ $role['id'] }}').showModal()">
                                                    Manage Permissions
                                                </button>
                                            </li>

                                            {{-- <li>
                                                <button type="button" class="text-error"
                                                    onclick="document.getElementById('delete_modal_{{ $role['id'] }}').showModal()">
                                                    Delete
                                                </button>
                                            </li> --}}
                                        </ul>
                                    </div>

                                    <dialog id="perm_modal_{{ $role['id'] }}" class="modal">
                                        <div class="modal-box max-w-2xl max-h-[85vh] overflow-y-auto">
                                            <form method="dialog">
                                                <button
                                                    class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                            </form>

                                            <h3 class="text-lg font-semibold text-center mb-3">
                                                Manage Permissions — {{ $role['display_name'] ?? ucfirst($role['name']) }}
                                            </h3>

                                            <form method="POST"
                                                action="{{ route('admin.dashboard.user.role.id.post', ['id' => $role['id']]) }}">
                                                @csrf

                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-6">
                                                    @foreach ($permissions as $perm)
                                                        <label
                                                            class="flex items-center space-x-2 border border-base-300 rounded-lg p-2 hover:bg-base-200">
                                                            <input type="checkbox" name="permissions[]"
                                                                value="{{ $perm['name'] }}"
                                                                {{ in_array($perm['name'], $role['permissions'] ?? []) ? 'checked' : '' }}
                                                                class="checkbox checkbox-sm" />
                                                            <span class="text-sm">
                                                                {{ $perm['display_name'] ?? ucfirst(str_replace('_', ' ', $perm['name'])) }}
                                                            </span>
                                                        </label>
                                                    @endforeach
                                                </div>

                                                <div class="modal-action mt-4">
                                                    <button type="submit" class="btn btn-primary w-full">Update
                                                        Permissions</button>
                                                </div>
                                            </form>
                                        </div>
                                    </dialog>


                                    <dialog id="detail_modal_{{ $role['id'] }}" class="modal">
                                        <div class="modal-box max-w-2xl max-h-[85vh] overflow-y-auto">
                                            <form method="dialog">
                                                <button
                                                    class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                            </form>

                                            <h3 class="text-lg font-semibold text-center mb-3">
                                                Role Details
                                            </h3>

                                            <div class="text-sm space-y-2">
                                                <p><strong>ID:</strong> {{ $role['id'] }}</p>
                                                <p><strong>Name:</strong> {{ $role['display_name'] ?? '-' }}</p>
                                                <p><strong>Type:</strong> {{ $role['name'] }}</p>
                                                <p><strong>Description:</strong> {{ $role['description'] ?? '-' }}</p>

                                                <p class="font-semibold">Permissions</p>

                                                @if (!empty($role['permissions']))
                                                    <ul class="list-disc list-inside text-xs">
                                                        @foreach ($role['permissions'] as $perm)
                                                            <li>{{ ucfirst(str_replace('_', ' ', $perm)) }}</li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <p class="italic text-gray-500 text-xs">No permissions assigned.</p>
                                                @endif
                                            </div>

                                            <div class="modal-action mt-6">
                                                <form method="dialog">
                                                    <button class="btn btn-primary w-full">Close</button>
                                                </form>
                                            </div>
                                        </div>
                                    </dialog>

                                    {{-- DELETE MODAL --}}
                                    <dialog id="delete_modal_{{ $role['id'] }}" class="modal">
                                        <div class="modal-box">
                                            <form method="dialog">
                                                <button
                                                    class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                                            </form>
                                            <p class="text-lg font-semibold py-0">Confirm Delete</p>
                                            <p class="py-2 mb-0 text-sm">
                                                Are you sure you want to delete role
                                                <span class="italic text-error">{{ $role['name'] }}</span> ?
                                            </p>
                                            <div class="modal-action mt-0">
                                                <form method="dialog">
                                                    <button class="btn">Cancel</button>
                                                </form>
                                                <form method="POST"
                                                    action="{{ route('admin.dashboard.user.role.delete', ['id' => $role['id']]) }}">
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
                        <span class="font-semibold">{{ $roles->firstItem() }}</span>
                        –
                        <span class="font-semibold">{{ $roles->lastItem() }}</span>
                        of
                        <span class="font-semibold">{{ $roles->total() }}</span>
                        results
                    </div>

                    <div class="join">
                        @if ($roles->onFirstPage())
                            <button class="join-item btn btn-sm btn-disabled">«</button>
                        @else
                            <a href="{{ $roles->previousPageUrl() }}" class="join-item btn btn-sm">«</a>
                        @endif

                        <a href="{{ $roles->url(1) }}"
                            class="join-item btn btn-sm {{ $roles->currentPage() === 1 ? 'btn-active' : '' }}">
                            1
                        </a>

                        @php
                            $start = max(2, $roles->currentPage() - 1);
                            $end = min($roles->lastPage() - 1, $roles->currentPage() + 1);
                        @endphp

                        @for ($i = $start; $i <= $end; $i++)
                            <a href="{{ $roles->url($i) }}"
                                class="join-item btn btn-sm {{ $roles->currentPage() === $i ? 'btn-active' : '' }}">
                                {{ $i }}
                            </a>
                        @endfor

                        @if ($roles->lastPage() > 1)
                            <a href="{{ $roles->url($roles->lastPage()) }}"
                                class="join-item btn btn-sm {{ $roles->currentPage() === $roles->lastPage() ? 'btn-active' : '' }}">
                                {{ $roles->lastPage() }}
                            </a>
                        @endif

                        @if ($roles->hasMorePages())
                            <a href="{{ $roles->nextPageUrl() }}" class="join-item btn btn-sm">»</a>
                        @else
                            <button class="join-item btn btn-sm btn-disabled">»</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
