@extends('layouts.admin')

@section('admin_content')
    <nav
        class="z-50 bg-base-100 border-b border-base-300 sticky top-0 flex flex-row justify-between items-center px-2 lg:px-3 py-0 !h-[50px] ">
        <a href="/" class="my-0 flex flex-row items-center text-sm lg:text-base font-semibold">
            <img src="{{ asset('assets/images/techverse_green_logo.png') }}" alt="{{ config('app.name') }} Admin Panel"
                class="h-6 mr-2 text-sm">
            {{ config('app.name') }} Admin Dashboard
        </a>

        <ul class="my-0 hidden lg:flex flex-row items-center px-1 gap-5">
            {{-- <li><a href="{{ route('shop.get') }}" class="{{ request()->is('shop') ? 'active' : '' }}">Setting</a></li> --}}
            @auth
                <div class="dropdown dropdown-end">
                    <label tabindex="0" class="btn btn-circle avatar size-6">
                        <img src="{{ asset('assets/images/blank_profile.png') }}" alt="Profile" class="rounded-full">
                    </label>
                    <ul tabindex="0" class="menu dropdown-content mt-3 p-2 shadow bg-base-100 rounded-box w-40">
                        <li><a href="/profile">Visit Site</a></li>
                        <li><a href="/shop">Visit Shop</a></li>
                        <li>
                            <button onclick="shop_navbar_logout_dialog.showModal()">Logout</button>
                        </li>
                    </ul>
                </div>
            @else
                <li><a href="{{ route('login') }}">Log In</a></li>
            @endauth
        </ul>


        <dialog id="shop_navbar_logout_dialog" class="modal">
            <div class="modal-box">
                <form method="dialog">
                    <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                </form>
                <p class="font-semibold py-0">Log Out</p>
                <p class="py-2 mb-0 text-sm">Are you sure you want to log out?</p>
                <div class="modal-action mt-0">
                    <form method="dialog">
                        <button class="btn  lg:btn-md">Close</button>
                    </form>
                    <form method="POST" action="{{ route('logout.post') }}">
                        @csrf
                        <button type="submit" class="btn  lg:btn-md btn-error">Logout</button>
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

                    <ul class="menu w-60 min-h-full bg-base-100 gap-1">
                        <p class="font-semibold px-2 py-1">{{ config('app.name') }} Admin Dashboard</p>

                        <li><a href="{{ route('home.get') }}" class="{{ request()->is('') ? 'active' : '' }}">Visit Site</a>
                        </li>
                        <li><a href="{{ route('shop.get') }}" class="{{ request()->is('shop') ? 'active' : '' }}">Visit
                                Shop</a></li>

                        <li><a href="{{ route('admin.dashboard.get') }}"
                                class="{{ request()->is('admin/dashboard') ? 'active' : '' }}">Dashboard</a></li>

                        <div class="collapse collapse-arrow p-0 m-0 py-0">
                            <input type="checkbox"
                                {{ request()->is('admin/dashboard/product') || request()->is('admin/dashboard/product/*') ? 'checked' : '' }} />
                            <div class="collapse-title px-3 py-2 my-0 mb-0 ">
                                Product
                            </div>
                            <div class="collapse-content m-0 pl-5">
                                <ul class="w-full space-y-1">
                                    <li><a href="{{ route('admin.dashboard.product.get') }}"
                                            class="{{ request()->is('admin/dashboard/product') ? 'bg-primary text-primary-content' : '' }}">Product
                                            List</a></li>
                                    <li><a href="{{ route('admin.dashboard.product.add.get') }}"
                                            class="{{ request()->is('admin/dashboard/product/add') || request()->is('admin/dashboard/product/edit/*') ? 'bg-primary text-primary-content' : '' }}">Add
                                            Product</a></li>
                                </ul>
                            </div>
                        </div>

                        <div class="collapse collapse-arrow p-0 m-0">
                            <input type="checkbox"
                                {{ request()->is('admin/dashboard/category') || request()->is('admin/dashboard/category/*') ? 'checked' : '' }} />
                            <div class="collapse-title px-3 py-2 my-0 mb-0 ">
                                Category
                            </div>
                            <div class="collapse-content m-0 pl-5">
                                <ul class="w-full space-y-1">
                                    <li><a href="{{ route('admin.dashboard.category.get') }}"
                                            class="{{ request()->is('admin/dashboard/category') ? 'bg-primary text-primary-content' : '' }}">Category
                                            List</a></li>
                                    <li><a href="{{ route('admin.dashboard.category.add.get') }}"
                                            class="{{ request()->is('admin/dashboard/category/add') || request()->is('admin/dashboard/category/edit/*') ? 'bg-primary text-primary-content' : '' }}">Add
                                            Category</a></li>
                                </ul>
                            </div>
                        </div>

                        <li><a href="{{ route('admin.media_storage.get') }}"
                                class="{{ request()->is('admin/media-storage') ? 'active' : '' }}">Media Storage</a></li>

                        <li>
                            <a href="{{ route('admin.dashboard.get') }}"
                                class="{{ request()->is('admin/dashboard') ? 'active' : '' }}">Setting</a>
                        </li>

                        <li>

                            <button onclick="shop_navbar_logout_dialog.showModal()">Logout</button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="flex flex-row flex-nowrap p-0 m-0">
        <div
            class="hidden lg:flex w-[250px] h-[calc(100vh-50px)] sticky top-[50px] border-r border-base-300 flex-col justify-between">
            <div>
                <ul class="w-full menu mt-1 p-2 gap-0">
                    <li><a href="{{ route('admin.dashboard.get') }}"
                            class="{{ request()->is('admin/dashboard') ? 'bg-primary text-primary-content' : '' }}">Dashboard</a>
                    </li>

                    <div class="collapse collapse-arrow p-0 m-0">
                        <input type="checkbox"
                            {{ request()->is('admin/dashboard/product') || request()->is('admin/dashboard/product/*') ? 'checked' : '' }} />
                        <div class="collapse-title px-3 py-1.5">
                            Product
                        </div>
                        <div class="collapse-content m-0 pl-5">
                            <ul class="w-full space-y-1">
                                <li><a href="{{ route('admin.dashboard.product.get') }}"
                                        class="{{ request()->is('admin/dashboard/product') ? 'bg-primary text-primary-content' : '' }}">Product
                                        List</a></li>
                                <li><a href="{{ route('admin.dashboard.product.add.get') }}"
                                        class="{{ request()->is('admin/dashboard/product/add') || request()->is('admin/dashboard/product/edit/*') ? 'bg-primary text-primary-content' : '' }}">Add
                                        Product</a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="collapse collapse-arrow p-0 m-0">
                        <input type="checkbox"
                            {{ request()->is('admin/dashboard/category') || request()->is('admin/dashboard/category/*') ? 'checked' : '' }} />
                        <div class="collapse-title px-3 py-1.5">
                            Category
                        </div>
                        <div class="collapse-content m-0 pl-5">
                            <ul class="w-full space-y-1">
                                <li><a href="{{ route('admin.dashboard.category.get') }}"
                                        class="{{ request()->is('admin/dashboard/category') ? 'bg-primary text-primary-content' : '' }}">Category
                                        List</a></li>
                                <li><a href="{{ route('admin.dashboard.category.add.get') }}"
                                        class="{{ request()->is('admin/dashboard/category/add') || request()->is('admin/dashboard/category/edit/*') ? 'bg-primary text-primary-content' : '' }}">Add
                                        Category</a></li>
                            </ul>
                        </div>
                    </div>

                    <li><a href="{{ route('admin.media_storage.get') }}"
                            class="{{ request()->is('admin/media-storage') ? 'bg-primary text-primary-content' : '' }}">Media
                            Storage</a>
                    </li>

                    <li><a href="{{ route('admin.dashboard.get') }}"
                            class="{{ request()->is('admin/setting') ? 'bg-primary text-primary-content' : '' }}">Setting</a>
                    </li>
                </ul>
            </div>
            <button type="button" class="btn btn-outline btn-error mx-5 mb-3"
                onclick="shop_navbar_logout_dialog.showModal()">
                Log Out
            </button>
        </div>

        <div class="w-full">
            @yield('admin_dashboard_content')
        </div>
    </div>
@endsection
