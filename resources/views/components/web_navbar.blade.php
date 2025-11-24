@php
    $site_name = getParsedTemplate('site_name');
    $site_logo = getSiteLogoURL();
@endphp

<nav
    class="h-[60px] w-full bg-white border border-b-slate-100 flex flex-row justify-content-between align-items-center px-6 sticky top-0 left-0 right-0 z-[1050] ">
    <a href="/" class="text-decoration-none text-black font-semibold text-lg">
        <img src="{{ $site_logo }}" alt="{!! $site_name !!}" class="h-8 mr-2 inline-block align-middle">
        {!! $site_name !!}
    </a>

    <ul class="m-0 p-0 flex flex-row align-items-center gap-2">
        <li>
            <a href="{{ route('shop.get') }}" class="btn">
                <i class="bi bi-bag text-lg"></i>
            </a>
        </li>
        <li>
            <a href="/cart" class="btn p-0 size-[35px] position-relative pt-1">
                <i class="bi bi-cart3 text-lg"></i>

                @empty(!session('cart_items', []))
                    <span class="position-absolute top-0 start-0 translate-middle badge rounded-pill bg-danger">
                        {{ count(session('cart_items', [])) }}
                        <span class="visually-hidden">unread messages</span>
                    </span>
                @endempty
            </a>
        </li>

        @auth
            <li>
                <div class="dropdown">
                    <button class="btn p-0 " data-bs-toggle="dropdown"> <i class="bi bi-person-circle text-lg"></i>
                        {{ auth()->user()->name }}</button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a href="/profile" class="dropdown-item">User Profile</a></li>
                        @if (auth()->user()->role?->is_company_member ?? false)
                            <li><a href="/admin/dashboard" class="dropdown-item">Admin Dashboard</a></li>
                        @endif
                        <li>
                            <form method="POST" action="{{ route('logout.post') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </li>
        @endauth

        @guest
            <li><a href="{{ route('login') }}">Log In</a></li>
        @endguest
    </ul>
</nav>

@push('script')
@endpush
