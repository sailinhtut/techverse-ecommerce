@extends('layouts.web')

@section('web_content')
    @include('components.shop_navbar')

    <div class="p-5" x-data="productVariantForm()">
        <p class="my-3 text-lg font-semibold">Testing Product Variant Form</p>
        <button type='submit' class="btn btn-primary" x-data
            @click="Toast.show('Product is created successfully',{type:'success'})">Success</button>
        <button type='submit' class="btn btn-primary" x-data
            @click="Toast.show('Product is created successfully',{type:'error'})">Error</button>
        <button type='submit' class="btn btn-primary" x-data
            @click="Toast.show('Product is created successfully',{type:'info'})">info</button>
        <button type='submit' class="btn btn-primary" x-data
            @click="Toast.show('Product is created successfully',{type:'warning'})">warning</button>


        <form action="/debug" method="POST">
            @csrf
            <button type='submit' class="btn btn-primary">Debug</button>
        </form>
    </div>
@endsection

@push('script')
    <script></script>
@endpush
