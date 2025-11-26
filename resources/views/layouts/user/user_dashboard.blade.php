@extends('layouts.web')

@section('web_content')
    @include('components.shop_navbar')
    <div class="flex flex-row flex-nowrap p-0 m-0">
        <div class="hidden lg:flex w-[300px] h-[calc(100vh-60px)] sticky top-[60px] flex-col pl-[50px] pt-3 pb-5 shrink-0">
            <ul class="w-3/4 menu menu-vertical m-0 mt-3 p-0 flex flex-col gap-1">
                <li class="rounded-box {{ request()->is('profile') ? 'bg-neutral dark:bg-primary text-white' : '' }}">
                    <a href="/profile">User Profile</a>
                </li>
                <li class="rounded-box {{ request()->is('notification') ? 'bg-neutral dark:bg-primary text-white' : '' }}"
                    x-data>
                    <a href="/notification" class="w-full">
                        <div class="w-fit inline-block relative">
                            <span>Notifications</span>
                            <span
                                class="absolute top-0 right-0 -translate-y-1/2 translate-x-1/2 text-xs badge badge-error badge-sm"
                                x-cloak x-text="$store.notification.unread_count"
                                x-show="$store.notification.unread_count > 0">
                            </span>
                        </div>
                    </a>
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
