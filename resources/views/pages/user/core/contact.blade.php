@extends('layouts.app')

@section('app_content')
    @php
        $site_name = getParsedTemplate('site_name');
    @endphp
    @include('components.landing_navbar')
    <div class="p-6 lg:p-7 mt-[60px] max-w-4xl mx-auto">
        <p class="text-xl md:text-2xl font-semibold text-gray-800 mb-4">Contact Us</p>
        <p class="text-gray-600 leading-relaxed mb-6">
            At <span class="font-semibold text-gray-900">{{ $site_name }}</span>,
            we value communication and are here to assist you with any inquiries.
            Whether it’s product details, order support, or partnership opportunities,
            feel free to reach out. Our team will respond promptly to ensure your
            experience with us is seamless and professional.
        </p>

        <p class="text-lg font-semibold text-gray-800 mb-3">Our Location</p>
        <div class="rounded-xl overflow-hidden shadow-md mb-6">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3151.835434509333!2d144.95373631531576!3d-37.81627937975165!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMzfCsDQ5JzAwLjYiUyAxNDTCsDU3JzE0LjAiRQ!5e0!3m2!1sen!2s!4v1614643331674!5m2!1sen!2s"
                width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy">
            </iframe>
        </div>

        <p class="text-lg font-semibold text-gray-800 mb-3">Get in Touch</p>
        <form method="POST" action="" class="space-y-3">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Your Name</label>
                <input type="text" name="name" required
                    class="w-full rounded-md border border-gray-300 px-2 py-1.5 text-sm focus:border-green-500 focus:ring focus:ring-green-200 text-gray-700" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                <input type="email" name="email" required
                    class="w-full rounded-md border border-gray-300 px-2 py-1.5 text-sm focus:border-green-500 focus:ring focus:ring-green-200 text-gray-700" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                <textarea name="message" rows="4" required
                    class="w-full rounded-md border border-gray-300 px-2 py-1.5 text-sm focus:border-green-500 focus:ring focus:ring-green-200 text-gray-700"></textarea>
            </div>
            <div class="pt-2">
                <button type="submit" class="w-full md:w-fit btn btn-primary">
                    Send Message
                </button>
            </div>
        </form>
    </div>
    @include('components.web_footer')
@endsection
