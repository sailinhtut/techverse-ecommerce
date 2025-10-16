@extends('layouts.app')
@push('script')
    @php
        session()->flash('success', 'Session expired. Please log in again.');
    @endphp
    <script>
        document.cookie.split(";").forEach(c => {
            document.cookie = c.trim().split("=")[0] + '=;expires=Thu, 01 Jan 1970 00:00:00 UTC;path=/';
        });

        window.location.href = "{{ route('login') }}";
    </script>
@endpush
