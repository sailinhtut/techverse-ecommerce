@extends('layouts.web')

@section('web_content')
    @include('components.shop_navbar')

    <div class="p-5" x-data>
        <p class="text-lg font-semibold">Test Page</p>
        <div>
            <p>Count: <span x-text="$store.global.count"></span></p>
            <p class="rounded p-3 border border-slate-300" x-show="$store.global.open" x-transition>
                Lorem ipsum dolor sit, amet consectetur adipisicing elit.
            </p>
            <button @click="$store.global.addCount()" class="btn btn-primary">Add+</button>
            <button @click="$store.global.open = !$store.global.open" class="btn btn-primary">Toggle Content</button>
        </div>
    </div>
@endsection

@push('script')
    <script>
         
    </script>
@endpush
