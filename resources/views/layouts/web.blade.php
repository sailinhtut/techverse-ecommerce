@extends('layouts.app') @section('app_content')
    @if (session('success') || session('error'))
        <div class="toast">
            @if (session('success'))
                <div id="status-success" class="alert border border-base-300 shadow-lg flex justify-between items-center">
                    <span>{{ session('success') }}</span>
                    <button type="button" class="btn btn-xs btn-circle ml-2" onclick="this.parentElement.remove()">
                        <i data-lucide="x" class="size-4"></i>
                    </button>
                </div>
            @endif

            @if (session('error'))
                <div id="status-error" class="alert border border-base-300 shadow-lg flex justify-between items-center">
                    <span>{{ session('error') }}</span>
                    <button type="button" class="btn btn-xs btn-circle ml-2" onclick="this.parentElement.remove()">
                        <i data-lucide="x" class="size-4"></i>
                    </button>
                </div>
            @endif
        </div>
    @endif

    <div>

        @yield('web_content')

        <dialog class="modal" id="logout_modal">
            <div class="modal-box">
                <form method="dialog">
                    <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                </form>
                <p class="font-semibold py-0">Log Out</p>
                <p class="py-2 mb-0">Are you sure you want to log out?</p>
                <div class="modal-action mt-0">
                    <form method="dialog">
                        <button class="btn">Close</button>
                    </form>
                    <form method="POST" action="{{ route('logout.post') }}">
                        @csrf
                        <button type="submit" class="btn btn-error">Logout</button>
                    </form>
                </div>
            </div>
        </dialog>
    </div>



@endsection
@push('script')
    <script>
        window.addEventListener("DOMContentLoaded", function() {
            setTimeout(() => {
                $("#status-error").fadeOut();
                $("#status-success").fadeOut();
            }, 3000);
        });
    </script>
@endpush
