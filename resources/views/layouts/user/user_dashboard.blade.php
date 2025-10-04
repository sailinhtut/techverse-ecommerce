@extends('layouts.app')

@section('app_content')
    @include('components.shop_navbar')
    <div class="flex flex-row flex-nowrap p-0 m-0">
        <div class="hidden lg:flex w-[300px] h-[calc(100vh-60px)] sticky top-[60px] flex-col pl-[50px] pt-3 pb-5">
            <ul class="menu menu-vertical m-0 mt-3 p-0 flex flex-col gap-1 ">
                <li class="rounded-box {{ request()->is('profile') ? 'bg-neutral dark:bg-primary text-white' : '' }}">
                    <a href="/profile">User Profile</a>
                </li>
                <li class="rounded-box {{ request()->is('wish-list') ? 'bg-neutral dark:bg-primary text-white' : '' }}">
                    <a href="/wish-list">Wish List</a>
                </li>
                <li class="rounded-box {{ request()->is('order-history') ? 'bg-neutral dark:bg-primary text-white' : '' }}">
                    <a href="/order-history">Order History</a>
                </li>
                <li
                    class="rounded-box {{ request()->is('shipping-address') ? 'bg-neutral dark:bg-primary text-white' : '' }}">
                    <a href="/shipping-address">Shipping Address</a>
                </li>
                <li
                    class="rounded-box {{ request()->is('payment-transaction') ? 'bg-neutral dark:bg-primary text-white' : '' }}">
                    <a href="/payment-transaction">Payment Transaction</a>
                </li>
                <li
                    class="rounded-box {{ request()->is('setting') ? 'bg-neutral dark:bg-primary text-white' : '' }}">
                    <a href="/setting">General Setting</a>
                </li>
                <button type="button" class="mt-10 btn btn-outline btn-error btn-sm"
                    onclick="shop_navbar_logout_dialog.showModal()">Log
                    Out
                </button>
            </ul>
        </div>
        <div class="w-full">
            @yield('user_dashboard_content')
        </div>
    </div>
@endsection
