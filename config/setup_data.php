<?php
return [
    'user_roles' => [
        [
            'name' => 'user',
            'display_name' => 'User',
            'description' => 'Standard User Role',
            'permissions' => json_encode([])
        ],
        [
            'name' => 'staff',
            'display_name' => 'Staff',
            'description' => 'Office Staff Role',
            'permissions' => json_encode([
                'manage_products',
                'manage_orders',
                'manage_users',
                'manage_discounts'
            ])
        ],
        [
            'name' => 'admin',
            'display_name' => 'Admin',
            'description' => 'Administrator Role',
            'permissions' => json_encode([
                'view_users',
                'view_dashboard',
                'manage_products',
                'manage_orders',
                'manage_users',
                'manage_discounts'
            ])
        ],
    ],
    'user_permission_types' => [
        ['name' => 'manage_products', 'display_name' => 'Manage Products'],
        ['name' => 'manage_orders', 'display_name' => 'Manage Orders'],
        ['name' => 'view_reports', 'display_name' => 'View Reports'],
        ['name' => 'manage_users', 'display_name' => 'Manage Users'],
        ['name' => 'manage_discounts', 'display_name' => 'Manage Discounts'],
        ['name' => 'view_dashboard', 'display_name' => 'View Dashboard'],
    ]
];
