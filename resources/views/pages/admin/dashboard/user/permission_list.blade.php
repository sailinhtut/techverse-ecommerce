@extends('layouts.admin.admin_dashboard')
@section('admin_dashboard_content')
    <div class="p-5 min-h-screen">
        <p class="lg:text-lg font-semibold mb-3">Role Permissions</p>

        <div class="card shadow-sm border border-base-300">
            <div class="card-body p-0 m-0 overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="w-[50px]">No.</th>
                            <th>Permission Name</th>
                            <th>Type</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($permissions as $permission)
                            <tr>
                                <td>{{ $loop->iteration + ($permissions->currentPage() - 1) * $permissions->perPage() }}.
                                </td>
                                <td>{{ $permission['display_name'] ?? '-' }}</td>
                                <td>{{ $permission['name'] ?? '-' }}</td>
                                <td>{{ $permission['description'] ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>


                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-3 py-3 px-5">
                    <div class="text-sm text-gray-500">
                        <span class="font-semibold">{{ $permissions->firstItem() }}</span>
                        –
                        <span class="font-semibold">{{ $permissions->lastItem() }}</span>
                        of
                        <span class="font-semibold">{{ $permissions->total() }}</span>
                        results
                    </div>

                    <div class="join">
                        @if ($permissions->onFirstPage())
                            <button class="join-item btn btn-sm btn-disabled">«</button>
                        @else
                            <a href="{{ $permissions->previousPageUrl() }}" class="join-item btn btn-sm">«</a>
                        @endif

                        @for ($i = 1; $i <= $permissions->lastPage(); $i++)
                            <a href="{{ $permissions->url($i) }}"
                                class="join-item btn btn-sm {{ $permissions->currentPage() === $i ? 'btn-active' : '' }}">
                                {{ $i }}
                            </a>
                        @endfor

                        @if ($permissions->hasMorePages())
                            <a href="{{ $permissions->nextPageUrl() }}" class="join-item btn btn-sm">»</a>
                        @else
                            <button class="join-item btn btn-sm btn-disabled">»</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
