<?php
return [
    'user_roles' => [
        [
            'name' => 'user',
            'display_name' => 'User',
            'description' => 'Standard User Role',
            'is_company_member' => false,
            'permissions' => json_encode([])
        ],
        [
            'name' => 'staff',
            'display_name' => 'Staff',
            'description' => 'Office Staff Role',
            'is_company_member' => true,
            'permissions' => json_encode([
                'view_dashboard',
                'view_store_management',
                'manage_branches',
                'manage_media_images',
                'view_product_management',
                'manage_products',
                'manage_categories',
                'manage_brands',
                'manage_reviews',
                'manage_coupons',
                'view_order_management',
                'manage_orders',
                'view_shipping_management',
                'manage_shipping_classes',
                'manage_shipping_zones',
                'manage_shipping_methods',
                'manage_shipping_rates',
                'view_payment_management',
                'manage_invoices',
                'manage_payments',
                'manage_transactions',
                'manage_storage',
                'manage_contact_message',
                'manage_articles',
                'manage_faqs',
                'manage_product_inventory',
                'manage_language_setting',
            ])
        ],
        [
            'name' => 'admin',
            'display_name' => 'Admin',
            'description' => 'Administrator Role',
            'is_company_member' => true,
            'permissions' => json_encode([
                'view_dashboard',
                'view_user_management',
                'manage_users',
                'manage_roles',
                'manage_permissions',
                'view_store_management',
                'manage_branches',
                'manage_media_images',
                'view_product_management',
                'manage_products',
                'manage_categories',
                'manage_brands',
                'manage_reviews',
                'manage_coupons',
                'view_order_management',
                'manage_orders',
                'view_shipping_management',
                'manage_shipping_classes',
                'manage_shipping_zones',
                'manage_shipping_methods',
                'manage_shipping_rates',
                'view_tax_management',
                'manage_tax_classes',
                'manage_tax_zones',
                'manage_tax_rates',
                'view_payment_management',
                'manage_invoices',
                'manage_payments',
                'manage_transactions',
                'manage_payment_methods',
                'manage_storage',
                'manage_site_setting',
                'manage_theme_setting',
                'manage_legal_setting',
                'manage_contact_message',
                'manage_articles',
                'manage_faqs',
                'manage_product_inventory',
                'manage_language_setting',
            ])
        ],
    ],
    'user_permission_types' => [
        ['name' => 'view_dashboard', 'display_name' => 'View Dashboard'],
        ['name' => 'view_user_management', 'display_name' => 'View User Management'],
        ['name' => 'manage_users', 'display_name' => 'Manage Users'],
        ['name' => 'manage_roles', 'display_name' => 'Manage Roles'],
        ['name' => 'manage_permissions', 'display_name' => 'Manage Permissions'],
        ['name' => 'view_store_management', 'display_name' => 'View Store Management'],
        ['name' => 'manage_branches', 'display_name' => 'Manage Branches'],
        ['name' => 'manage_media_images', 'display_name' => 'Manage Media Images'],
        ['name' => 'view_product_management', 'display_name' => 'View Product Management'],
        ['name' => 'manage_products', 'display_name' => 'Manage Products'],
        ['name' => 'manage_categories', 'display_name' => 'Manage Categories'],
        ['name' => 'manage_brands', 'display_name' => 'Manage Brands'],
        ['name' => 'manage_reviews', 'display_name' => 'Manage Reviews'],
        ['name' => 'manage_coupons', 'display_name' => 'Manage Coupons'],
        ['name' => 'view_order_management', 'display_name' => 'View Order Management'],
        ['name' => 'manage_orders', 'display_name' => 'Manage Orders'],
        ['name' => 'view_shipping_management', 'display_name' => 'View Shipping Management'],
        ['name' => 'manage_shipping_classes', 'display_name' => 'Manage Shipping Classes'],
        ['name' => 'manage_shipping_zones', 'display_name' => 'Manage Shipping Zones'],
        ['name' => 'manage_shipping_methods', 'display_name' => 'Manage Shipping Methods'],
        ['name' => 'manage_shipping_rates', 'display_name' => 'Manage Shipping Rates'],
        ['name' => 'view_tax_management', 'display_name' => 'View Tax Management'],
        ['name' => 'manage_tax_classes', 'display_name' => 'Manage Tax Classes'],
        ['name' => 'manage_tax_zones', 'display_name' => 'Manage Tax Zones'],
        ['name' => 'manage_tax_rates', 'display_name' => 'Manage Tax Rates'],
        ['name' => 'view_payment_management', 'display_name' => 'View Payment Management'],
        ['name' => 'manage_invoices', 'display_name' => 'Manage Invoices'],
        ['name' => 'manage_payments', 'display_name' => 'Manage Payments'],
        ['name' => 'manage_transactions', 'display_name' => 'Manage Transactions'],
        ['name' => 'manage_payment_methods', 'display_name' => 'Manage Payment Methods'],
        ['name' => 'manage_storage', 'display_name' => 'Manage Storage'],
        ['name' => 'manage_site_setting', 'display_name' => 'Manage Site Setting'],
        ['name' => 'manage_theme_setting', 'display_name' => 'Manage Theme Setting'],
        ['name' => 'manage_legal_setting', 'display_name' => 'Manage Legal Setting'],
        ['name' => 'manage_contact_message', 'display_name' => 'Manage Contact Message'],
        ['name' => 'manage_articles', 'display_name' => 'Manage Blog Articles'],
        ['name' => 'manage_faqs', 'display_name' => 'Manage FAQs'],
        ['name' => 'manage_product_inventory', 'display_name' => 'Manage Product Inventory'],
        ['name' => 'manage_language_setting', 'display_name' => 'Manage Language Setting'],

    ],
    'frequent_questions' => [

        [
            'question' => 'What is your store about?',
            'answer' => 'We are an online platform offering high-quality products with a focus on reliability, convenience, and customer satisfaction. Our goal is to provide a smooth and secure shopping experience.',
            'is_active' => true,
            'sort_order' => 1,
        ],

        [
            'question' => 'How do I place an order?',
            'answer' => 'Browse our products, add items to your cart, and proceed to checkout. Follow the on-screen instructions to complete your purchase securely.',
            'is_active' => true,
            'sort_order' => 2,
        ],

        [
            'question' => 'Do I need an account to place an order?',
            'answer' => 'You can browse products without an account, but creating an account allows you to track orders, manage addresses, and enjoy a faster checkout experience.',
            'is_active' => true,
            'sort_order' => 3,
        ],

        [
            'question' => 'What payment methods do you accept?',
            'answer' => 'We accept major credit and debit cards, online banking, and other secure payment methods supported on our platform. Available options may vary by location.',
            'is_active' => true,
            'sort_order' => 4,
        ],

        [
            'question' => 'Is my payment information secure?',
            'answer' => 'Yes. All payments are processed using encrypted and secure payment gateways. We do not store your payment details on our servers.',
            'is_active' => true,
            'sort_order' => 5,
        ],

        [
            'question' => 'How long does delivery take?',
            'answer' => 'Delivery times depend on your location and the shipping method selected during checkout. Estimated delivery times will be shown before order confirmation.',
            'is_active' => true,
            'sort_order' => 6,
        ],

        [
            'question' => 'How can I track my order?',
            'answer' => 'Once your order is shipped, you will receive a confirmation email with tracking details. You can also track your order from your account dashboard.',
            'is_active' => true,
            'sort_order' => 7,
        ],

        [
            'question' => 'Can I change or cancel my order after placing it?',
            'answer' => 'Orders can be modified or cancelled before they are shipped. Please contact our support team as soon as possible for assistance.',
            'is_active' => true,
            'sort_order' => 8,
        ],

        [
            'question' => 'What is your return policy?',
            'answer' => 'We accept returns for eligible products within the specified return period, provided the items are unused and in their original packaging.',
            'is_active' => true,
            'sort_order' => 9,
        ],

        [
            'question' => 'How do I request a refund?',
            'answer' => 'Refund requests can be submitted through your account or by contacting customer support. Approved refunds will be processed back to the original payment method.',
            'is_active' => true,
            'sort_order' => 10,
        ],

        [
            'question' => 'Do you offer customer support?',
            'answer' => 'Yes. Our support team is available to assist you with orders, payments, and general inquiries through our contact page or support email.',
            'is_active' => true,
            'sort_order' => 11,
        ],

        [
            'question' => 'How can I contact customer support?',
            'answer' => 'You can reach us via email, phone, or the contact form available on our website. Our support hours are listed on the contact page.',
            'is_active' => true,
            'sort_order' => 12,
        ],

        [
            'question' => 'Are my personal details protected?',
            'answer' => 'Absolutely. We value your privacy and handle your personal data in accordance with our privacy policy and applicable data protection laws.',
            'is_active' => true,
            'sort_order' => 13,
        ],

        [
            'question' => 'Why didn’t I receive a confirmation email?',
            'answer' => 'Please check your spam or junk folder. If you still haven’t received it, ensure your email address is correct or contact our support team.',
            'is_active' => true,
            'sort_order' => 14,
        ],

        [
            'question' => 'Can I use multiple discount codes?',
            'answer' => 'Only one discount code can be applied per order unless otherwise stated.',
            'is_active' => true,
            'sort_order' => 15,
        ],

    ],
    'app_settings' => [
        ['key' => 'site_name', 'value' => config('app.name')],
        ['key' => 'site_description', 'value' => config('app.description')],

        ['key' => 'site_primary_color', 'value' => config('app.site_primary_color')],
        ['key' => 'site_primary_content_color', 'value' => config('app.site_primary_content_color')],

        ['key' => 'site_phone_1', 'value' => config('app.phone_1')],
        ['key' => 'site_phone_2', 'value' => config('app.phone_2')],

        ['key' => 'site_contact_email', 'value' => config('app.contact_email')],
        ['key' => 'site_support_email', 'value' => config('app.support_email')],

        ['key' => 'site_address', 'value' => config('app.address')],
        ['key' => 'site_map_location_link', 'value' => config('app.map_location_link')],

        ['key' => 'site_privacy_policy', 'value' => config('app.site_privacy_policy')],

        ['key' => 'site_terms_conditions', 'value' => config('app.site_terms_conditions')],


        ['key' => 'site_currency', 'value' => config('app.currency_code')]
    ],
];
