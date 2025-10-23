@extends('layouts.admin')

@section('admin_content')
    <nav
        class="z-50 bg-base-100 border-b border-base-300 sticky top-0 flex flex-row justify-between items-center px-2 lg:px-3 py-0 !h-[50px] ">
        <a href="{{ config('app.admin_logo_link') }}"
            class="my-0 flex flex-row items-center text-sm lg:text-base font-semibold">
            <img src="{{ asset(config('app.app_logo_bare_path')) }}" alt="{{ config('app.name') }} Admin Panel"
                class="h-6 mr-2 text-sm">
            {{ config('app.name') }} Admin Dashboard
        </a>

        <ul class="my-0 hidden lg:flex flex-row items-center px-1 gap-5">
            @auth
                <div class="dropdown dropdown-end">
                    <label tabindex="0" class="btn btn-circle avatar size-6">
                        <img src="{{ asset('assets/images/blank_profile.png') }}" alt="Profile" class="rounded-full">
                    </label>
                    <ul tabindex="0" class="menu dropdown-content mt-3 p-2 shadow bg-base-100 rounded-box w-40">
                        <li><a href="/profile">Visit Site</a></li>
                        <li><a href="/shop">Visit Shop</a></li>
                        <li>
                            <button onclick="logout_modal.showModal()">Logout</button>
                        </li>
                    </ul>
                </div>
            @else
                <li><a href="{{ route('login') }}">Log In</a></li>
            @endauth
        </ul>

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

                        <li><a href="{{ route('shop.get') }}" class="{{ request()->is('shop') ? 'active' : '' }}">Shop</a>
                        </li>

                        <li><a href="{{ route('admin.dashboard.get') }}"
                                class="{{ request()->is('admin/dashboard') ? 'active' : '' }}">Dashboard</a></li>

                        <div class="collapse collapse-arrow p-0 m-0 py-0">
                            <input type="checkbox"
                                {{ request()->is('admin/dashboard/user') || request()->is('admin/dashboard/user/*') ? 'checked' : '' }} />
                            <div class="collapse-title px-3 py-2 my-0 mb-0 ">
                                User
                            </div>
                            <div class="collapse-content m-0 pl-5">
                                <ul class="w-full space-y-1">
                                    <li><a href="{{ route('admin.dashboard.user.get') }}"
                                            class="{{ request()->is('admin/dashboard/user') ? 'bg-primary text-primary-content' : '' }}">Users</a>
                                    </li>

                                    <li><a href="{{ route('admin.dashboard.user.role.get') }}"
                                            class="{{ request()->is('admin/dashboard/user/role') ? 'bg-primary text-primary-content' : '' }}">Roles</a>
                                    </li>
                                    <li><a href="{{ route('admin.dashboard.user.permission.get') }}"
                                            class="{{ request()->is('admin/dashboard/user/permission') ? 'bg-primary text-primary-content' : '' }}">Permissions</a>
                                    </li>

                                </ul>
                            </div>
                        </div>

                        <div class="collapse collapse-arrow p-0 m-0 py-0">
                            <input type="checkbox"
                                {{ request()->is('admin/dashboard/product') || request()->is('admin/dashboard/product/*') ? 'checked' : '' }} />
                            <div class="collapse-title px-3 py-2 my-0 mb-0 ">
                                Product
                            </div>
                            <div class="collapse-content m-0 pl-5">
                                <ul class="w-full space-y-1">
                                    <li><a href="{{ route('admin.dashboard.product.get') }}"
                                            class="{{ request()->is('admin/dashboard/product') || request()->is('admin/dashboard/product/edit/*') || request()->is('admin/dashboard/product/add') ? 'bg-primary text-primary-content' : '' }}">Products</a>
                                    </li>
                                    <li><a href="{{ route('admin.dashboard.product.category.get') }}"
                                            class="{{ request()->is('admin/dashboard/product/category') ? 'bg-primary text-primary-content' : '' }}">Categories</a>
                                    </li>
                                    <li><a href="{{ route('admin.dashboard.product.brand.get') }}"
                                            class="{{ request()->is('admin/dashboard/product/brand') ? 'bg-primary text-primary-content' : '' }}">Brands</a>
                                    </li>
                                    <li><a href="{{ route('admin.dashboard.product.attribute.get') }}"
                                            class="{{ request()->is('admin/dashboard/product/attribute') ? 'bg-primary text-primary-content' : '' }}">Attributes</a>
                                    </li>
                                </ul>
                            </div>
                        </div>


                        <div class="collapse collapse-arrow p-0 m-0">
                            <input type="checkbox"
                                {{ request()->is('admin/dashboard/shipping') || request()->is('admin/dashboard/shipping/*') ? 'checked' : '' }} />
                            <div class="collapse-title px-3 py-2 my-0 mb-0 ">
                                Shipping
                            </div>
                            <div class="collapse-content m-0 pl-5">
                                <ul class="w-full space-y-1">
                                    <li><a href="{{ route('admin.dashboard.shipping.shipping-class.get') }}"
                                            class="{{ request()->is('admin/dashboard/shipping/shipping-class') ? 'bg-primary text-primary-content' : '' }}">Shipping
                                            Classes</a>
                                    </li>
                                    <li><a href="{{ route('admin.dashboard.shipping.shipping-zone.get') }}"
                                            class="{{ request()->is('admin/dashboard/shipping/shipping-zone') ? 'bg-primary text-primary-content' : '' }}">Shipping
                                            Zones</a>
                                    </li>
                                    <li><a href="{{ route('admin.dashboard.shipping.shipping-method.get') }}"
                                            class="{{ request()->is('admin/dashboard/shipping/shipping-method') ? 'bg-primary text-primary-content' : '' }}">Shipping
                                            Methods</a>
                                    </li>
                                    <li><a href="{{ route('admin.dashboard.shipping.shipping-rate.get') }}"
                                            class="{{ request()->is('admin/dashboard/shipping/shipping-rate') ? 'bg-primary text-primary-content' : '' }}">Shipping
                                            Rates</a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="collapse collapse-arrow p-0 m-0">
                            <input type="checkbox"
                                {{ request()->is('admin/dashboard/tax') || request()->is('admin/dashboard/tax/*') ? 'checked' : '' }} />
                            <div class="collapse-title px-3 py-2 my-0 mb-0 ">
                                Tax
                            </div>
                            <div class="collapse-content m-0 pl-5">
                                <ul class="w-full space-y-1">
                                    <li><a href="{{ route('admin.dashboard.tax.tax-class.get') }}"
                                            class="{{ request()->is('admin/dashboard/tax/tax-class') ? 'bg-primary text-primary-content' : '' }}">Tax
                                            Classes</a>
                                    </li>
                                    <li><a href="{{ route('admin.dashboard.tax.tax-zone.get') }}"
                                            class="{{ request()->is('admin/dashboard/tax/tax-zone') ? 'bg-primary text-primary-content' : '' }}">Tax
                                            Zones</a>
                                    </li>
                                    <li><a href="{{ route('admin.dashboard.tax.tax-rate.get') }}"
                                            class="{{ request()->is('admin/dashboard/tax/tax-rate') ? 'bg-primary text-primary-content' : '' }}">Tax
                                            Rates</a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="collapse collapse-arrow p-0 m-0 py-0">
                            <input type="checkbox"
                                {{ request()->is('admin/dashboard/order') || request()->is('admin/dashboard/order/*') ? 'checked' : '' }} />
                            <div class="collapse-title px-3 py-2 my-0 mb-0 ">
                                Order
                            </div>
                            <div class="collapse-content m-0 pl-5">
                                <ul class="w-full space-y-1">
                                    <li><a href="{{ route('admin.dashboard.order.get') }}"
                                            class="{{ request()->is('admin/dashboard/order') ? 'bg-primary text-primary-content' : '' }}">Orders</a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="collapse collapse-arrow p-0 m-0 py-0">
                            <input type="checkbox"
                                {{ request()->is('admin/dashboard/payment') || request()->is('admin/dashboard/payment/*') ? 'checked' : '' }} />
                            <div class="collapse-title px-3 py-2 my-0 mb-0 ">
                                Payment
                            </div>
                            <div class="collapse-content m-0 pl-5">
                                <ul class="w-full space-y-1">
                                    <li><a href="{{ route('admin.dashboard.payment.invoice.get') }}"
                                            class="{{ request()->is('admin/dashboard/payment/invoice') ? 'bg-primary text-primary-content' : '' }}">Invoices</a>
                                    </li>

                                    <li><a href="{{ route('admin.dashboard.payment.payment.get') }}"
                                            class="{{ request()->is('admin/dashboard/payment/payment') ? 'bg-primary text-primary-content' : '' }}">Payments</a>
                                    </li>

                                    <li><a href="{{ route('admin.dashboard.payment.transaction.get') }}"
                                            class="{{ request()->is('admin/dashboard/payment/transaction') ? 'bg-primary text-primary-content' : '' }}">Transactions</a>
                                    </li>
                                    <li><a href="{{ route('admin.dashboard.payment.payment-method.get') }}"
                                            class="{{ request()->is('admin/dashboard/payment/payment-method') ? 'bg-primary text-primary-content' : '' }}">Payment
                                            Methods</a>
                                    </li>

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
                            <button onclick="logout_modal.showModal()">Logout</button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="flex flex-row flex-nowrap p-0 m-0">
        <div
            class="hidden lg:flex w-[250px] h-[calc(100vh-50px)] overflow-y-auto advanced-scrollbar sticky top-[50px] border-r border-base-300 flex-col justify-between">
            <div>
                <ul class="w-full menu mt-1 p-2 gap-0">
                    <li><a href="{{ route('admin.dashboard.get') }}"
                            class="{{ request()->is('admin/dashboard') ? 'bg-primary text-primary-content' : '' }}">Dashboard</a>
                    </li>

                    <div class="collapse collapse-arrow p-0 m-0">
                        <input type="checkbox"
                            {{ request()->is('admin/dashboard/user') || request()->is('admin/dashboard/user/*') ? 'checked' : '' }} />
                        <div class="collapse-title px-3 py-1.5">
                            User
                        </div>
                        <div class="collapse-content m-0 pl-5">
                            <ul class="w-full space-y-1">
                                <li><a href="{{ route('admin.dashboard.user.get') }}"
                                        class="{{ request()->is('admin/dashboard/user') ? 'bg-primary text-primary-content' : '' }}">Users</a>
                                </li>
                                <li><a href="{{ route('admin.dashboard.user.role.get') }}"
                                        class="{{ request()->is('admin/dashboard/user/role') ? 'bg-primary text-primary-content' : '' }}">Roles</a>
                                </li>

                                <li><a href="{{ route('admin.dashboard.user.permission.get') }}"
                                        class="{{ request()->is('admin/dashboard/user/permission') ? 'bg-primary text-primary-content' : '' }}">Permissions</a>
                                </li>
                            </ul>
                        </div>
                    </div>


                    <div class="collapse collapse-arrow p-0 m-0">
                        <input type="checkbox"
                            {{ request()->is('admin/dashboard/product') || request()->is('admin/dashboard/product/*') ? 'checked' : '' }} />
                        <div class="collapse-title px-3 py-1.5">
                            Product
                        </div>
                        <div class="collapse-content m-0 pl-5">
                            <ul class="w-full space-y-1">
                                <li><a href="{{ route('admin.dashboard.product.get') }}"
                                        class="{{ request()->is('admin/dashboard/product') || request()->is('admin/dashboard/product/add') || request()->is('admin/dashboard/product/edit/*') ? 'bg-primary text-primary-content' : '' }}">Products</a>
                                </li>
                                <li><a href="{{ route('admin.dashboard.product.category.get') }}"
                                        class="{{ request()->is('admin/dashboard/product/category') ? 'bg-primary text-primary-content' : '' }}">Categories</a>
                                </li>
                                <li><a href="{{ route('admin.dashboard.product.brand.get') }}"
                                        class="{{ request()->is('admin/dashboard/product/brand') ? 'bg-primary text-primary-content' : '' }}">Brands</a>
                                </li>
                                <li><a href="{{ route('admin.dashboard.product.attribute.get') }}"
                                        class="{{ request()->is('admin/dashboard/product/attribute') ? 'bg-primary text-primary-content' : '' }}">Attributes</a>
                                </li>
                            </ul>
                        </div>
                    </div>


                    <div class="collapse collapse-arrow p-0 m-0">
                        <input type="checkbox"
                            {{ request()->is('admin/dashboard/order') || request()->is('admin/dashboard/order/*') ? 'checked' : '' }} />
                        <div class="collapse-title px-3 py-1.5">
                            Order
                        </div>
                        <div class="collapse-content m-0 pl-5">
                            <ul class="w-full space-y-1">
                                <li><a href="{{ route('admin.dashboard.order.get') }}"
                                        class="{{ request()->is('admin/dashboard/order') ? 'bg-primary text-primary-content' : '' }}">Orders</a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="collapse collapse-arrow p-0 m-0">
                        <input type="checkbox"
                            {{ request()->is('admin/dashboard/shipping') || request()->is('admin/dashboard/shipping/*') ? 'checked' : '' }} />
                        <div class="collapse-title px-3 py-1.5">
                            Shipping
                        </div>
                        <div class="collapse-content m-0 pl-5">
                            <ul class="w-full space-y-1">
                                <li><a href="{{ route('admin.dashboard.shipping.shipping-class.get') }}"
                                        class="{{ request()->is('admin/dashboard/shipping/shipping-class') ? 'bg-primary text-primary-content' : '' }}">Shipping
                                        Classes</a></li>
                                <li><a href="{{ route('admin.dashboard.shipping.shipping-zone.get') }}"
                                        class="{{ request()->is('admin/dashboard/shipping/shipping-zone') ? 'bg-primary text-primary-content' : '' }}">Shipping
                                        Zones</a>
                                </li>
                                <li><a href="{{ route('admin.dashboard.shipping.shipping-method.get') }}"
                                        class="{{ request()->is('admin/dashboard/shipping/shipping-method') ? 'bg-primary text-primary-content' : '' }}">Shipping
                                        Methods</a></li>
                                <li><a href="{{ route('admin.dashboard.shipping.shipping-rate.get') }}"
                                        class="{{ request()->is('admin/dashboard/shipping/shipping-rate') ? 'bg-primary text-primary-content' : '' }}">Shipping
                                        Rates</a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="collapse collapse-arrow p-0 m-0">
                        <input type="checkbox"
                            {{ request()->is('admin/dashboard/tax') || request()->is('admin/dashboard/tax/*') ? 'checked' : '' }} />
                        <div class="collapse-title px-3 py-1.5">
                            Tax
                        </div>
                        <div class="collapse-content m-0 pl-5">
                            <ul class="w-full space-y-1">
                                <li><a href="{{ route('admin.dashboard.tax.tax-class.get') }}"
                                        class="{{ request()->is('admin/dashboard/tax/tax-class') ? 'bg-primary text-primary-content' : '' }}">Tax
                                        Classes</a></li>
                                <li><a href="{{ route('admin.dashboard.tax.tax-zone.get') }}"
                                        class="{{ request()->is('admin/dashboard/tax/tax-zone') ? 'bg-primary text-primary-content' : '' }}">Tax
                                        Zones</a>
                                </li>
                                <li><a href="{{ route('admin.dashboard.tax.tax-rate.get') }}"
                                        class="{{ request()->is('admin/dashboard/tax/tax-rate') ? 'bg-primary text-primary-content' : '' }}">Tax
                                        Rates</a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="collapse collapse-arrow p-0 m-0">
                        <input type="checkbox"
                            {{ request()->is('admin/dashboard/payment') || request()->is('admin/dashboard/payment/*') ? 'checked' : '' }} />
                        <div class="collapse-title px-3 py-1.5">
                            Payment
                        </div>
                        <div class="collapse-content m-0 pl-5">
                            <ul class="w-full space-y-1">
                                <li><a href="{{ route('admin.dashboard.payment.invoice.get') }}"
                                        class="{{ request()->is('admin/dashboard/payment/invoice') ? 'bg-primary text-primary-content' : '' }}">Invoices</a>
                                <li><a href="{{ route('admin.dashboard.payment.payment.get') }}"
                                        class="{{ request()->is('admin/dashboard/payment/payment') ? 'bg-primary text-primary-content' : '' }}">Payments</a>
                                </li>
                                </li>
                                <li><a href="{{ route('admin.dashboard.payment.transaction.get') }}"
                                        class="{{ request()->is('admin/dashboard/payment/transaction') ? 'bg-primary text-primary-content' : '' }}">Transactions</a>
                                </li>

                                <li><a href="{{ route('admin.dashboard.payment.payment-method.get') }}"
                                        class="{{ request()->is('admin/dashboard/payment/payment-method') ? 'bg-primary text-primary-content' : '' }}">Payment
                                        Methods</a>
                                </li>
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
            <button type="button" class="btn btn-outline btn-error mx-5 mb-3" onclick="logout_modal.showModal()">
                Log Out
            </button>
        </div>

        <div class="w-full">
            @yield('admin_dashboard_content')
        </div>
    </div>
@endsection
