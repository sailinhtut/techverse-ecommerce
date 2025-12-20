<?php

return [
    'name' => 'Master Seller',

    'description' => 'Powerful Ecommerce system built to help businesses sell products online with ease. It offers seamless product management, secure payments, and efficient order tracking—all in one place. With its user-friendly design and smart features, Master Seller makes online selling faster, smarter, and more successful.',

    'site_primary_color' => '#132356',
    'site_primary_content_color' => '#FFFFFF',

    'phone_1' => '+959252203838',
    'phone_2' => '+959252203839',

    'contact_email' => 'contact@masterseller.com',
    'support_email' => 'support@masterseller.com',

    'address' => '123 Tech Street, Yangon, Myanmar',
    'map_location_link' => 'https://maps.app.goo.gl/JcKJNjNfRj3Vwfrs7',

    'currency_code' => "USD",

    'app_logo' => 'assets/images/app_logo.png',

    'app_logo_background' => 'assets/images/app_logo_background.png',

    'logo_link' => '/shop',

    'admin_logo_link' => '/shop',

    'cache_time' => 3600,

    'env' => env('APP_ENV', 'production'),

    'debug' => (bool) env('APP_DEBUG', false),

    'url' => env('APP_URL', 'http://localhost'),

    'timezone' => 'Asia/Bangkok',

    'locale' => env('APP_LOCALE', 'en'),

    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),

    'faker_locale' => env('APP_FAKER_LOCALE', 'en_US'),

    'cipher' => 'AES-256-CBC',

    'key' => env('APP_KEY'),

    'previous_keys' => [
        ...array_filter(
            explode(',', (string) env('APP_PREVIOUS_KEYS', ''))
        ),
    ],

    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],

    'site_privacy_policy' => '<p>Last updated: September 21, 2025</p><p><br></p><p>At <strong>@site_name</strong>, we highly value your privacy and are committed to protecting your personal information. This Privacy Policy explains how we collect, use, and safeguard your data when you interact with our website, products, and services.</p><p><br></p><p><strong>1. Information We Collect</strong></p><p> We may collect the following types of information:</p><ol><li data-list="bullet"><span class="ql-ui" contenteditable="false"></span>Personal details such as name, email address, phone number, and billing information.</li><li data-list="bullet"><span class="ql-ui" contenteditable="false"></span>Account information including login credentials and preferences.</li><li data-list="bullet"><span class="ql-ui" contenteditable="false"></span>Usage data such as pages visited, products viewed, and interactions with our services.</li><li data-list="bullet"><span class="ql-ui" contenteditable="false"></span>Technical information like IP address, browser type, and device identifiers.</li><li data-list="bullet"><span class="ql-ui" contenteditable="false"></span><br></li></ol><p><strong>2. How We Use Your Information</strong></p><p> The information we collect may be used to:</p><ol><li data-list="bullet"><span class="ql-ui" contenteditable="false"></span>Process and fulfill orders efficiently.</li><li data-list="bullet"><span class="ql-ui" contenteditable="false"></span>Enhance our products, services, and website experience.</li><li data-list="bullet"><span class="ql-ui" contenteditable="false"></span>Send important updates, promotional offers, or service-related communications.</li><li data-list="bullet"><span class="ql-ui" contenteditable="false"></span>Ensure the security and integrity of our platform.</li><li data-list="bullet"><span class="ql-ui" contenteditable="false"></span><br></li></ol><p><strong>3. Sharing of Information</strong></p><p> We do not sell or rent your personal information. We may share data with trusted third-party partners who assist in operating our website, processing payments, delivering products, or providing customer support. These partners are contractually obligated to protect your data.</p><p><br></p><p><strong>4. Data Protection</strong></p><p> We implement industry-standard security measures to protect your information from unauthorized access, disclosure, or misuse. While we strive to ensure data protection, no method of transmission over the internet is 100% secure.</p><p><br></p><p><strong>5. Cookies & Tracking</strong></p><p> Our website uses cookies and similar technologies to enhance your browsing experience, analyze traffic, and personalize content. You can manage your cookie preferences through your browser settings.</p><p><br></p><p><strong>6. Your Rights</strong></p><p> You have the right to access, correct, or delete your personal information at any time. You may also opt out of receiving promotional emails by following the unsubscribe link included in our messages.</p><p><br></p><p><strong>7. Changes to This Policy</strong></p><p> We may update this Privacy Policy periodically to reflect changes in our practices or legal requirements. Updates will be posted on this page with a revised date.</p><p><br></p><p><strong>8. Contact Us</strong></p><p> For questions or concerns regarding this Privacy Policy, please contact us at:</p><p><br></p><p><strong>@site_name</strong></p><p> Email: @site_support_email</p><p> Phone: @site_phone_1, @site_phone_2</p>',

    'site_terms_conditions' => '<p>Last updated: September 21, 2025</p><p><br></p><p>Welcome to <strong>@site_name</strong>. These Terms and Conditions govern your use of our website, products, and services. By accessing or using our platform, you agree to comply with and be bound by these terms. If you do not agree, you should discontinue using our services immediately.</p><p><br></p><p><strong>1. General Use</strong></p><p> Our website and services are provided for personal and business use. You must be at least 18 years old or have the consent of a legal guardian to create an account or make purchases.</p><p><br></p><p><strong>2. Accounts</strong></p><p> When you register an account with @site_name, you agree to provide accurate and complete information. You are responsible for maintaining the confidentiality of your account and for all activities under it. We reserve the right to suspend or terminate accounts that violate our policies.</p><p><br></p><p><strong>3. Purchases & Payments</strong></p><p> All purchases made through our store are subject to product availability and confirmation of payment details. Prices are displayed in local currency and may change without notice. We accept various payment methods, and by providing payment details, you confirm that you are authorized to use the selected method.</p><p><br></p><p><strong>4. Shipping & Returns</strong></p><p> Orders are processed and shipped within the timelines specified during checkout. Customers are responsible for providing accurate delivery information. Returns and refunds are subject to our Return Policy, which ensures fair resolution in cases of defective or incorrect products.</p><p><br></p><p><strong>5. Limitation of Liability</strong></p><p>@site_name is not liable for indirect, incidental, or consequential damages arising from the use of our website or products. We do not guarantee uninterrupted or error-free service, though we strive to maintain the highest level of reliability.</p><p><br></p><p><strong>6. Privacy</strong></p><p> We respect your privacy and handle your data responsibly. Please review our Privacy Policy to learn more about how we collect, use, and protect your personal information.</p><p><br></p><p><strong>7. Changes to Terms</strong></p><p> We may update these Terms & Conditions from time to time. Updates will be posted on this page with a revised date. Continued use of our services after changes indicates acceptance of the updated terms.</p><p><br></p><p><strong>8. Contact Us</strong></p><p> If you have any questions about these Terms & Conditions, please contact us at:</p><p><br></p><p><strong>@site_name</strong></p><p> Email: @site_support_email</p><p> Phone: @site_phone_1, @site_phone_2</p>',

    'template_usage_tooltip' => 'You can use the following placeholders in your templates: @site_name, @site_description, @site_phone_1, @site_phone_2, @site_support_email, @site_contact_email, @site_address, @site_map_location_link.',

];
