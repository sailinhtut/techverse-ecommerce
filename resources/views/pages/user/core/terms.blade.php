@extends('layouts.app')

@section('app_content')
    @include('components.landing_navbar')
    <div class="p-6 lg:p-7 mt-[60px] max-w-4xl mx-auto">
        <p class="text-xl md:text-2xl font-semibold mb-4">Terms & Conditions</p>
        <p class="text-sm text-gray-600 mb-6">Last updated: September 21, 2025</p>

        <p class="mb-4">
            Welcome to <strong>Tech Verse Computer Store</strong>. These Terms and Conditions govern your use of our
            website, products, and services. By accessing or using our platform, you agree to comply with and be bound
            by these terms. If you do not agree, you should discontinue using our services immediately.
        </p>

        <p class="text-lg font-semibold mt-6 mb-2">1. General Use</p>
        <p class="mb-4">
            Our website and services are provided for personal and business use. You must be at least 18 years old
            or have the consent of a legal guardian to create an account or make purchases.
        </p>

        <p class="text-lg font-semibold mt-6 mb-2">2. Accounts</p>
        <p class="mb-4">
            When you register an account with Tech Verse Computer Store, you agree to provide accurate and complete
            information. You are responsible for maintaining the confidentiality of your account and for all activities
            under it. We reserve the right to suspend or terminate accounts that violate our policies.
        </p>

        <p class="text-lg font-semibold mt-6 mb-2">3. Purchases & Payments</p>
        <p class="mb-4">
            All purchases made through our store are subject to product availability and confirmation of payment details.
            Prices are displayed in local currency and may change without notice. We accept various payment methods, and
            by providing payment details, you confirm that you are authorized to use the selected method.
        </p>

        <p class="text-lg font-semibold mt-6 mb-2">4. Shipping & Returns</p>
        <p class="mb-4">
            Orders are processed and shipped within the timelines specified during checkout. Customers are responsible
            for providing accurate delivery information. Returns and refunds are subject to our Return Policy, which
            ensures fair resolution in cases of defective or incorrect products.
        </p>

        <p class="text-lg font-semibold mt-6 mb-2">5. Limitation of Liability</p>
        <p class="mb-4">
            Tech Verse Computer Store is not liable for indirect, incidental, or consequential damages arising from
            the use of our website or products. We do not guarantee uninterrupted or error-free service, though we
            strive to maintain the highest level of reliability.
        </p>

        <p class="text-lg font-semibold mt-6 mb-2">6. Privacy</p>
        <p class="mb-4">
            We respect your privacy and handle your data responsibly. Please review our
            <a href="{{ route('privacy.get') }}" class="text-blue-600 hover:underline">Privacy Policy</a>
            to learn more about how we collect, use, and protect your personal information.
        </p>

        <p class="text-lg font-semibold mt-6 mb-2">7. Changes to Terms</p>
        <p class="mb-4">
            We may update these Terms & Conditions from time to time. Updates will be posted on this page with a revised
            date. Continued use of our services after changes indicates acceptance of the updated terms.
        </p>

        <p class="text-lg font-semibold mt-6 mb-2">8. Contact Us</p>
        <p>
            If you have any questions about these Terms & Conditions, please contact us at:
        </p>
        <p class="mt-2">
            <strong>Tech Verse Computer Store</strong><br>
            Email: support@techverse.com<br>
            Phone: +95 (252) 203-838
        </p>
    </div>
    @include('components.web_footer')
@endsection
