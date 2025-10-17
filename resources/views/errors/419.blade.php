@extends('layouts.app')
@push('script')
    @php
        session()->flash('success', 'Session expired. Please log in again.');
    @endphp
    <script>
        window.location.href = "{{ route('login') }}";
    </script>
@endpush
