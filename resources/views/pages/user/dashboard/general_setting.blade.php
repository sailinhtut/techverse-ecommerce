@extends('layouts.user.user_dashboard')

@section('user_dashboard_content')
    <div class="p-3 lg:p-5">
        <p class="lg:text-lg font-semibold ">Setting</p>
        <p class="text-sm">Welcome, {{ auth()->user()->name }}</p>
    </div>
@endsection
