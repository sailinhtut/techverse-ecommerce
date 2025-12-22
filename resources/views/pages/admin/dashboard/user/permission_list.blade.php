@extends('layouts.admin.admin_dashboard')
@section('admin_dashboard_content')
    <div class="p-3 lg:p-5 min-h-screen">
        <p class="lg:text-lg font-semibold">Role Permissions</p>

        <div class="mt-3 card shadow-sm border border-base-300">
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
                                <td>{{ $loop->iteration }}.
                                </td>
                                <td>{{ $permission['display_name'] ?? '-' }}</td>
                                <td>{{ $permission['name'] ?? '-' }}</td>
                                <td>{{ $permission['description'] ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>


                
            </div>
        </div>
    </div>
@endsection
