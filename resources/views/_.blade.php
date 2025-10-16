@extends('layouts.web')

@section('web_content')
    @include('components.shop_navbar')

    <div class="p-5" x-data="justkidding">
        <h1>Work Hard.. Don't Give Up</h1>
        <h2>Cart Item <span x-html="`<i>${count}</i>`"></span></h2>
        <form action="/debug" method="POST">
            @csrf
            <button class="btn btn-primary" type='submit'>Test</button>
        </form>
    </div>
@endsection

@push('script')
    <script></script>
@endpush
