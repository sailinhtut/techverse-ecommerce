@extends('layouts.app')

@section('app_content')
    @include('components.landing_navbar')
    <div class="p-6 lg:p-7 mt-[60px] max-w-4xl mx-auto">
        <p class="text-xl md:text-2xl font-semibold mb-4">Privacy Policy</p>
        <p class="text-sm text-gray-600 mb-6">Last updated: September 21, 2025</p>

        <p class="mb-4">
            At <strong>Tech Verse Computer Store</strong>, we value your privacy and are committed to protecting your
            personal information. This Privacy Policy explains how we collect, use, and safeguard your data when you
            interact with our website, services, and products.
        </p>

        <p class="text-lg font-semibold mt-6 mb-2">1. Information We Collect</p>
        <p class="mb-4">
            We may collect the following types of information:
        </p>
        <ul class="list-disc ml-6 mb-4">
            <li>Personal details such as name, email address, phone number, and billing information.</li>
            <li>Account information, including login credentials and preferences.</li>
            <li>Usage data such as pages visited, products viewed, and interactions with our services.</li>
            <li>Technical information such as IP address, browser type, and device identifiers.</li>
        </ul>

        <p class="text-lg font-semibold mt-6 mb-2">2. How We Use Your Information</p>
        <p class="mb-4">
            The information we collect may be used for:
        </p>
        <ul class="list-disc ml-6 mb-4">
            <li>Processing and fulfilling orders.</li>
            <li>Improving our products, services, and website experience.</li>
            <li>Sending important updates, promotional offers, or service-related communications.</li>
            <li>Ensuring the security and integrity of our platform.</li>
        </ul>

        <p class="text-lg font-semibold mt-6 mb-2">3. Sharing of Information</p>
        <p class="mb-4">
            We do not sell or rent your personal information. However, we may share data with trusted third parties
            who assist us in operating our website, processing payments, delivering products, or providing customer
            support. These partners are contractually obligated to protect your data.
        </p>

        <p class="text-lg font-semibold mt-6 mb-2">4. Data Protection</p>
        <p class="mb-4">
            We implement security measures to protect your information from unauthorized access, disclosure,
            or misuse. While we strive to ensure data protection, no method of transmission over the internet
            is 100% secure.
        </p>

        <p class="text-lg font-semibold mt-6 mb-2">5. Cookies & Tracking</p>
        <p class="mb-4">
            Our website uses cookies and similar technologies to improve your browsing experience, analyze traffic,
            and personalize content. You can manage your cookie preferences through your browser settings.
        </p>

        <p class="text-lg font-semibold mt-6 mb-2">6. Your Rights</p>
        <p class="mb-4">
            You have the right to access, correct, or delete your personal information at any time. You may also opt
            out of receiving promotional emails by following the unsubscribe link included in our messages.
        </p>

        <p class="text-lg font-semibold mt-6 mb-2">7. Changes to This Policy</p>
        <p class="mb-4">
            We may update this Privacy Policy periodically to reflect changes in our practices or for legal reasons.
            Updates will be posted on this page with a revised date.
        </p>

        <p class="text-lg font-semibold mt-6 mb-2">8. Contact Us</p>
        <p>
            For questions or concerns regarding this Privacy Policy, please contact us at:
        </p>
        <p class="mt-2">
            <strong>Tech Verse Computer Store</strong><br>
            Email: privacy@techverse.com<br>
            Phone: +95 (252) 203-838
        </p>
    </div>
    @include('components.web_footer')
@endsection
