@extends('layouts.web')

@section('web_content')
    @include('components.shop_navbar')

    <div class="p-5" x-data="productVariantForm()">
        <p class="my-3 text-lg font-semibold">Testing Product Variant Form</p>
        
        <form action="/debug" method="POST">
            @csrf
            <button type='submit' class="btn btn-primary">Debug</button>
        </form>
    </div>
@endsection

@push('script')
    <script></script>
@endpush
