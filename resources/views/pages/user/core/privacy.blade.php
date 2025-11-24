@extends('layouts.app')

@section('app_content')
    @include('components.landing_navbar')
    @php
        $privacy_policy = getParsedTemplate('site_privacy_policy');
    @endphp
    <div class="p-6 lg:p-7 mt-[60px] max-w-4xl mx-auto">
        <p class="text-xl md:text-2xl font-semibold mb-4">Privacy Policy</p>
        <div class="prose !text-justify w-full overflow-scroll">
            {!! $privacy_policy !!}
        </div>
    </div>
    @include('components.web_footer')
@endsection
