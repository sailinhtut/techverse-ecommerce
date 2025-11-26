@php
    $site_name = getParsedTemplate('site_name');
    $site_logo = getSiteLogoURL();
@endphp

<nav
    class="bg-base-100 border-b border-base-300 sticky top-0 flex flex-row justify-between items-center px-2 lg:px-5 py-0 !h-[60px] z-50">
    <a href="{{ config('app.logo_link') }}" class="my-0 flex flex-row items-center text-sm lg:text-base font-semibold">
        <img src="{{ $site_logo }}" alt="{!! $site_name !!}" class="h-8 mr-2">
        {!! $site_name !!}

    </a>

    <ul class="my-0 hidden lg:flex flex-row items-center px-1 gap-5">

        <form action="/shop/search" method="GET">
            <input type="text" name="q" class="w-[250px] input bg-base-200 border border-base-200"
                placeholder="Search Something..." value="{{ old('q', $query ?? '') }}">
        </form>
        <li>
            <a href="{{ route('shop.get') }}" class="{{ request()->is('shop') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                </svg>
            </a>
        </li>
        <li>
            <a x-data href="{{ route('cart.get') }}" class="relative inline-block mt-2">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"
                    aria-hidden="true" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="M3 3h2l1.6 9.6a2 2 0 0 0 2 1.6h7.8a2 2 0 0 0 2-1.6L21 7H6" />
                    <circle cx="10" cy="20" r="1.5" />
                    <circle cx="18" cy="20" r="1.5" />
                </svg>

                <span class="absolute top-0 right-0 -translate-y-1/2 translate-x-1/2 text-xs badge badge-error badge-sm"
                    x-text="$store.cart.totalItems()" x-show="$store.cart.totalItems() > 0"></span>
            </a>
        </li>

        @auth
            <li>
                <a x-data href="{{ route('notification.get') }}" class="relative inline-block mt-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0M3.124 7.5A8.969 8.969 0 0 1 5.292 3m13.416 0a8.969 8.969 0 0 1 2.168 4.5" />
                    </svg>

                    <!-- Badge -->
                    <span class="absolute top-0 right-0 -translate-y-1/2 translate-x-1/2 text-xs badge badge-error badge-sm"
                        x-cloak x-text="$store.notification.unread_count" x-show="$store.notification.unread_count > 0">
                    </span>
                </a>
            </li>
            <div class="dropdown dropdown-end">
                <div tabindex="0">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                </div>

                <ul tabindex="0" class="menu dropdown-content mt-3 p-2 shadow bg-base-100 rounded-box w-40">
                    <li><a href="/profile">Profile</a></li>
                    @if (auth()->user()->role?->is_company_member ?? false)
                        <li><a href="/admin/dashboard">Admin Panel</a></li>
                    @endif
                    <li>
                        <button onclick="logout_modal.showModal()">Logout</button>
                    </li>
                </ul>
            </div>
        @else
            <li><a href="{{ route('login') }}">Log In</a></li>
        @endauth
    </ul>


    <div class="lg:hidden flex flex-row items-center gap-3">
        <a x-data href="{{ route('cart.get') }}" class="relative inline-block">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" aria-hidden="true"
                fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 3h2l1.6 9.6a2 2 0 0 0 2 1.6h7.8a2 2 0 0 0 2-1.6L21 7H6" />
                <circle cx="10" cy="20" r="1.5" />
                <circle cx="18" cy="20" r="1.5" />
            </svg>
            <span class="absolute top-0 right-0 -translate-y-1/2 translate-x-1/2 text-xs badge badge-error badge-sm"
                x-text="$store.cart.totalItems()" x-show="$store.cart.totalItems() > 0"></span>
        </a>

        <a x-data href="{{ route('notification.get') }}" class="relative inline-block">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0M3.124 7.5A8.969 8.969 0 0 1 5.292 3m13.416 0a8.969 8.969 0 0 1 2.168 4.5" />
            </svg>

            <span class="absolute top-0 right-0 -translate-y-1/2 translate-x-1/2 text-xs badge badge-error badge-sm"
                x-cloak x-text="$store.notification.unread_count" x-show="$store.notification.unread_count > 0">
            </span>
        </a>


        <div class="drawer drawer-start">
            <input id="mobile-drawer" type="checkbox" class="drawer-toggle" />
            <div class="drawer-content">
                <label for="mobile-drawer" class="btn btn-ghost btn-circle">
                    <i data-lucide="menu" class="size-5"></i>
                </label>
            </div>
            <div class="drawer-side">
                <label for="mobile-drawer" aria-label="close sidebar" class="drawer-overlay"></label>

                <ul class="menu w-60 min-h-full bg-base-100 text-base-content space-y-2 relative">

                    <p class="font-semibold px-2 py-1">{{ $site_name }}</p>

                    <li>
                        <a href="{{ route('shop.get') }}"
                            class="{{ request()->is('shop') ? 'bg-primary text-primary-content' : '' }}">Shop</a>
                    </li>
                    <li>
                        <a href="{{ route('cart.get') }}"
                            class="{{ request()->is('cart') ? 'bg-primary text-primary-content' : '' }}">Cart</a>
                    </li>
                    @auth
                        <li>
                            <a href="{{ route('profile.get') }}"
                                class="{{ request()->is('profile') ? 'bg-primary text-primary-content' : '' }}">User
                                Profile</a>
                        </li>
                        <li x-data>
                            <a href="{{ route('notification.get') }}"
                                class="{{ request()->is('notification') ? 'bg-primary text-primary-content ' : '' }}w-full">
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
                        <li>
                            <a href="{{ route('address.get') }}"
                                class="{{ request()->is('address') ? 'bg-primary text-primary-content' : '' }}">
                                Addresses</a>
                        </li>
                        <li>
                            <a href="{{ route('wishlist.get') }}"
                                class="{{ request()->is('wishlist') ? 'bg-primary text-primary-content' : '' }}">Wishlists</a>
                        </li>
                        <li>
                            <a href="{{ route('order_history.get') }}"
                                class="{{ request()->is('order-history') ? 'bg-primary text-primary-content' : '' }}">Orders</a>
                        </li>
                        <li>
                            <a href="{{ route('payment.get') }}"
                                class="{{ request()->is('payment-transaction') ? 'bg-primary text-primary-content' : '' }}">Payment
                                Invoices</a>
                        </li>
                        {{-- <li>
                            <a href="{{ route('setting.get') }}"
                                class="{{ request()->is('setting') ? 'bg-primary text-primary-content' : '' }}">Settings</a>
                        </li> --}}
                        @if (auth()->user()->role?->is_company_member ?? false)
                            <li>
                                <a href="{{ route('admin.dashboard.get') }}"
                                    class="{{ request()->is('admin/dashboard') ? 'bg-primary text-primary-content' : '' }}">Admin
                                    Panel</a>
                            </li>
                        @endif
                        <li>
                            <button onclick="logout_modal.showModal()">Logout</button>
                        </li>
                    @else
                        <li><a href="{{ route('login') }}">Log In</a></li>
                    @endauth
                </ul>
            </div>
        </div>
    </div>
</nav>
