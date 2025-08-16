<?php
defined('ABSPATH') || exit;

$admin_url = admin_url('admin.php?page=arvand-panel');

return [
    [
        'name' => 'general',
        'url' => add_query_arg('section', 'general', $admin_url),
        'icon' => 'settings-line',
        'label' => __('تنظیمات متفرقه', 'arvand-panel'),
    ],
    [
        'name' => 'register',
        'url' => add_query_arg('section', 'register', $admin_url),
        'icon' => 'user-add-line',
        'label' => __('ثبت نام', 'arvand-panel'),
    ],
    [
        'name' => 'login',
        'url' => add_query_arg('section', 'login', $admin_url),
        'icon' => 'login-circle-line',
        'label' => __('ورود', 'arvand-panel'),
    ],
    [
        'name' => 'google',
        'url' => add_query_arg('section', 'google', $admin_url),
        'icon' => 'google-line',
        'label' => __('گوگل', 'arvand-panel'),
    ],
    [
        'name' => 'account-menu',
        'url' => add_query_arg('section', 'account-menu', $admin_url),
        'icon' => 'account-box-line',
        'label' => __('منوی حساب کاربری', 'arvand-panel'),
    ],
    [
        'name' => 'panel',
        'url' => add_query_arg('section', 'panel', $admin_url),
        'icon' => 'file-user-line',
        'label' => __('ناحیه کاربری', 'arvand-panel'),
    ],
    [
        'icon' => 'dashboard-line',
        'label' => __('پیشخوان', 'arvand-panel'),
        'submenus' => [
            [
                'name' => 'dashboard',
                'url' => add_query_arg('section', 'dashboard', $admin_url),
                'label' => __('عمومی', 'arvand-panel'),
            ],
            [
                'name' => 'dash-boxes',
                'url' => add_query_arg('section', 'dash-boxes', $admin_url),
                'label' => __('باکس ها', 'arvand-panel'),
            ],
        ]
    ],
    [
        'icon' => 'coupon-line',
        'label' => __('تیکت', 'arvand-panel'),
        'submenus' => [
            [
                'name' => 'ticket',
                'url' => add_query_arg('section', 'ticket', $admin_url),
                'label' => __('عمومی', 'arvand-panel'),
            ],
            [
                'name' => 'ticket-department',
                'url' => add_query_arg('section', 'ticket-department', $admin_url),
                'label' => __('دپارتمان ها', 'arvand-panel'),
            ],
            [
                'name' => 'add-supporter',
                'url' => add_query_arg('section', 'add-supporter', $admin_url),
                'label' => __('افزودن پشتیبان', 'arvand-panel'),
            ],
            [
                'name' => 'supporters',
                'url' => add_query_arg('section', 'supporters', $admin_url),
                'label' => __('پشتیبان ها', 'arvand-panel'),
            ],
        ]
    ],
    [
        'name' => 'wallet',
        'url' => add_query_arg('section', 'wallet', $admin_url),
        'icon' => 'wallet-3-line',
        'label' => __('کیف پول', 'arvand-panel'),
    ],
    [
        'name' => 'sms-providers',
        'url' => add_query_arg('section', 'sms-providers', $admin_url),
        'icon' => 'message-3-line',
        'label' => __('سامانه های پیامکی', 'arvand-panel'),
    ],
    [
        'name' => 'pages',
        'url' => add_query_arg('section', 'pages', $admin_url),
        'icon' => 'article-line',
        'label' => __('برگه ها', 'arvand-panel'),
    ],
    [
        'name' => 'shortcode',
        'url' => add_query_arg('section', 'shortcode', $admin_url),
        'icon' => 'brackets-line',
        'label' => __('کدهای کوتاه', 'arvand-panel'),
    ],
    [
        'name' => 'email',
        'url' => add_query_arg('section', 'email', $admin_url),
        'icon' => 'mail-line',
        'label' => __('ایمیل', 'arvand-panel'),
    ],
    [
        'icon' => 'palette-line',
        'label' => __('سبک ها', 'arvand-panel'),
        'submenus' => [
            [
                'name' => 'styles',
                'url' => add_query_arg('section', 'styles', $admin_url),
                'label' => __('متفرقه', 'arvand-panel'),
            ],
            [
                'name' => 'colors',
                'url' => add_query_arg('section', 'colors', $admin_url),
                'label' => __('رنگ ها', 'arvand-panel'),
            ],
        ]
    ],
    [
        'name' => 'roles',
        'url' => add_query_arg('section', 'roles', $admin_url),
        'icon' => 'shield-user-line',
        'label' => __('نقش ها', 'arvand-panel'),
    ],
];