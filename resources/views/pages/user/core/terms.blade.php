@extends('layouts.app')

@section('app_content')
    @include('components.landing_navbar')
    @php
        $terms_conditions = getParsedTemplate('site_terms_conditions');
    @endphp
    <div class="p-6 lg:p-7 mt-[60px] max-w-4xl mx-auto">
        <p class="text-xl md:text-2xl font-semibold mb-4">Terms & Conditions</p>
        <div class="prose !text-justify w-full overflow-scroll">
            {!! $terms_conditions !!}
        </div>
    </div>
    @include('components.web_footer')
@endsection
