<nav x-data="{ scrolled: false }" x-init="window.addEventListener('scroll', () => { scrolled = window.scrollY > 10 })"
    :class="scrolled ? 'bg-white shadow-sm border-b border-base-300' : 'bg-transparent'"
    class="fixed top-0 left-0 right-0 flex flex-row justify-between items-center px-2 lg:px-5 py-0 !h-[60px] z-50">
    <a href="/" class="my-0 flex flex-row items-center text-sm lg:text-base font-semibold">
        <img src="{{ asset('assets/images/techverse_green_logo.png') }}" alt="{{ config('app.name') }}" class="h-8 mr-2">
        {{ config('app.name') }}
    </a>

    <ul class="my-0 hidden lg:flex flex-row items-center px-1 gap-5">

        <li><a href="{{ route('shop.get') }}" class="{{ request()->is('shop') ? 'active' : '' }} text-primary">Go
                Shopping</a></li>
        <li><a href="{{ route('contact.get') }}" class="{{ request()->is('shop') ? 'active' : '' }}">Contact</a></li>
        <li><a href="{{ route('about_us.get') }}" class="{{ request()->is('shop') ? 'active' : '' }}">About</a></li>

        @auth
            <div class="dropdown dropdown-end">
                <label tabindex="0" class="btn btn-circle avatar size-7">
                    <img src="{{ asset('assets/images/blank_profile.png') }}" alt="Profile" class="rounded-full">
                </label>
                <ul tabindex="0" class="menu dropdown-content mt-3 p-2 shadow bg-base-100 rounded-box w-40">
                    <li><a href="/profile">Profile</a></li>
                    <li><a href="/admin/dashboard">Admin Panel</a></li>
                    <li>
                        <button type="button" onclick="landing_navbar_dialog.showModal()" class="w-full text-left">
                            Log Out
                        </button>
                    </li>

                </ul>
            </div>
        @else
            <li><a href="{{ route('login') }}">Log In</a></li>
        @endauth
    </ul>
    <dialog class="modal" id="landing_navbar_dialog">
        <div class="modal-box">
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
            </form>
            <p class="font-semibold py-0">Log Out</p>
            <p class="py-2 mb-0 text-sm">Are you sure you want to log out?</p>
            <div class="modal-action mt-0">
                <form method="dialog">
                    <button class="btn lg:btn-md">Close</button>
                </form>
                <form method="POST" action="{{ route('logout.post') }}">
                    @csrf
                    <button type="submit" class="btn lg:btn-md btn-error">Logout</button>
                </form>
            </div>
        </div>
    </dialog>

    <div class="flex-none lg:hidden">
        <div class="drawer drawer-start">
            <input id="mobile-drawer" type="checkbox" class="drawer-toggle" />
            <div class="drawer-content">
                <label for="mobile-drawer" class="btn btn-ghost btn-circle">
                    <i data-lucide="menu"></i>
                </label>
            </div>
            <div class="drawer-side">
                <label for="mobile-drawer" aria-label="close sidebar" class="drawer-overlay"></label>
                <ul class="menu w-60 min-h-full bg-base-100 text-base-content space-y-2 relative">

                    <p class="font-semibold px-2 py-1">{{ config('app.name') }}</p>

                    <li><a href="{{ route('shop.get') }}" class="{{ request()->is('shop') ? 'active' : '' }}">Shop</a>
                    </li>
                    <li><a href="{{ route('about_us.get') }}"
                            class="{{ request()->is('about') ? 'active' : '' }}">About</a>
                    </li>
                    <li><a href="{{ route('contact.get') }}"
                            class="{{ request()->is('contact') ? 'active' : '' }}">Contact</a>
                    </li>
                    <li><a href="{{ route('privacy.get') }}"
                            class="{{ request()->is('privacy') ? 'active' : '' }}">Privacy</a>
                    </li>
                    <li><a href="{{ route('terms.get') }}"
                            class="{{ request()->is('terms') ? 'active' : '' }}">Terms</a>
                    </li>
                    @auth
                        <li><a href="{{ route('profile.get') }}">Profile</a></li>
                        <li><a href="{{ route('admin.dashboard.get') }}">Admin Panel</a></li>
                        <li>
                            <button type="button" onclick="landing_navbar_dialog.showModal()">Logout</button>
                        </li>
                    @else
                        <li><a href="{{ route('login') }}">Log In</a></li>
                    @endauth
                </ul>
            </div>
        </div>
    </div>
</nav>
