@extends('layouts.app') @section('app_content')
    @if (session('success') || session('error'))
        <div class="fixed top-16 right-3 flex flex-col align-items-end gap-2 z-50">
            @if (session('success'))
                <div id="status-success" class="flex items-start bg-black rounded-lg px-3 py-2 w-fit text-white shadow-md">
                    <i class="bi bi-check-circle-fill text-green-500"></i>
                    <div class="text-sm mx-3 max-w-[300px]">{{ session('success') }}</div>
                    <button class="text-white" aria-label="Close" onclick="$('#status-success').slideUp()">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            @endif

            @if (session('error'))
                <div id="status-error" class="flex items-start bg-black rounded-lg px-3 py-2 w-fit text-white shadow-md">
                    <i class="bi bi-exclamation-circle text-red-500"></i>
                    <div class="text-sm mx-3 max-w-[300px]">{{ session('error') }}</div>
                    <button class="text-white" aria-label="Close" onclick="$('#status-error').slideUp()">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            @endif
        </div>
    @endif
    @yield('admin_content')
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
