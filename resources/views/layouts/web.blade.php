@extends('layouts.app') @section('app_content')
    

    <div>

        @yield('web_content')


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
