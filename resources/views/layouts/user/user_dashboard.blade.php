@extends('layouts.web')

@section('web_content')
    @include('components.shop_navbar')
    <div class="flex flex-row flex-nowrap p-0 m-0">
        <div class="hidden lg:flex w-[300px] h-[calc(100vh-60px)] sticky top-[60px] flex-col pl-[50px] pt-3 pb-5">
            <ul class="w-3/4 menu menu-vertical m-0 mt-3 p-0 flex flex-col gap-1">
                <li class="rounded-box {{ request()->is('profile') ? 'bg-neutral dark:bg-primary text-white' : '' }}">
                    <a href="/profile">User Profile</a>
                </li>
                <li class="rounded-box {{ request()->is('notification') ? 'bg-neutral dark:bg-primary text-white' : '' }}">
                    <a href="/notification">Notifications</a>
                </li>
                <li class="rounded-box {{ request()->is('address') ? 'bg-neutral dark:bg-primary text-white' : '' }}">
                    <a href="/address">Addresses</a>
                </li>
                <li class="rounded-box {{ request()->is('wishlist') ? 'bg-neutral dark:bg-primary text-white' : '' }}">
                    <a href="/wishlist">Wishlists</a>
                </li>
                <li class="rounded-box {{ request()->is('order-history') ? 'bg-neutral dark:bg-primary text-white' : '' }}">
                    <a href="/order-history">Orders</a>
                </li>
                <li class="rounded-box {{ request()->is('payment') ? 'bg-neutral dark:bg-primary text-white' : '' }}">
                    <a href="/payment">Payments</a>
                </li>
                <li class="rounded-box {{ request()->is('setting') ? 'bg-neutral dark:bg-primary text-white' : '' }}">
                    <a href="/setting">Setting</a>
                </li>
                <button type="button" class="mt-10 btn btn-outline btn-error btn-sm" onclick="logout_modal.showModal()">Log
                    Out
                </button>
            </ul>
        </div>
        <div class="w-full">
            @yield('user_dashboard_content')
        </div>
    </div>
@endsection
