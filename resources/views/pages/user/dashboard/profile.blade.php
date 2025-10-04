@extends('layouts.user.user_dashboard')

@section('user_dashboard_content')
    <div class="p-3 lg:p-5">
        <p class="text-base lg:text-lg font-semibold">Profile</p>
        <p class="text-sm">Welcome, {{ auth()->user()->name }}</p>
        <div class="mt-5 flex flex-row gap-3">
            <a class="btn btn-primary btn-sm">Edit Profile</a>
        </div>
    </div>
@endsection
