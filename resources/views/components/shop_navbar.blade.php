<nav
    class="bg-base-100 border-b border-base-300 sticky top-0 flex flex-row justify-between items-center px-2 lg:px-5 py-0 !h-[60px] z-50">
    <a href="{{ config('app.logo_link') }}" class="my-0 flex flex-row items-center text-sm lg:text-base font-semibold">
        <img src="{{ asset(config('app.app_logo_bare_path')) }}" alt="{{ config('app.name') }}" class="h-8 mr-2">
        {{ config('app.name') }}
    </a>



    <ul class="my-0 hidden lg:flex flex-row items-center px-1 gap-5">
        {{-- <li><a href="{{ route('home.get') }}" class="{{ request()->is('/') ? 'active' : '' }}">Home</a></li> --}}
        <li><a href="{{ route('shop.get') }}" class="{{ request()->is('shop') ? 'active' : '' }}">Shop</a></li>
        <li>
            {{-- <a href="{{ route('cart.get') }}" class="relative">
                Cart
                @empty(!session('cart_items', []))
                    <span class="badge badge-error badge-sm absolute -top-2 -right-3">
                        {{ count(session('cart_items', [])) }}
                    </span>
                @endempty
            </a> --}}
            <a x-data href="{{ route('cart.get') }}" class="relative">
                Cart
                <span class="badge badge-error badge-sm absolute -top-2 -right-3" x-text="$store.cart.totalItems()"
                    x-show="$store.cart.totalItems() > 0"></span>
            </a>
        </li>

        @auth
            <div class="dropdown dropdown-end">
                <label tabindex="0" class="btn btn-circle avatar size-7">
                    <img src="{{ asset('assets/images/blank_profile.png') }}" alt="Profile" class="rounded-full">
                </label>
                <ul tabindex="0" class="menu dropdown-content mt-3 p-2 shadow bg-base-100 rounded-box w-40">
                    <li><a href="/profile">Profile</a></li>
                    @if (auth()->user()->hasRole('admin'))
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


        <a x-data href="{{ route('cart.get') }}" class="relative">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
            </svg>
            <span class="badge badge-error badge-sm absolute -top-2 -right-3" x-text="$store.cart.totalItems()"
                x-show="$store.cart.totalItems() > 0"></span>
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

                    <p class="font-semibold px-2 py-1">{{ config('app.name') }}</p>

                    <li><a href="{{ route('shop.get') }}"
                            class="{{ request()->is('shop') ? 'bg-primary text-primary-content' : '' }}">Shop</a>
                    </li>
                    <li><a href="{{ route('cart.get') }}"
                            class="{{ request()->is('cart') ? 'bg-primary text-primary-content' : '' }}">Cart</a>
                    </li>
                    @auth
                        <li>
                            <a href="{{ route('profile.get') }}"
                                class="{{ request()->is('profile') ? 'bg-primary text-primary-content' : '' }}">User
                                Profile</a>
                        </li>
                        <li>
                            <a href="{{ route('notification.get') }}"
                                class="{{ request()->is('notification') ? 'bg-primary text-primary-content' : '' }}">Notifications</a>
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
                        <li>
                            <a href="{{ route('setting.get') }}"
                                class="{{ request()->is('setting') ? 'bg-primary text-primary-content' : '' }}">Settings</a>
                        </li>
                        @if (auth()->user()->hasRole('admin'))
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
