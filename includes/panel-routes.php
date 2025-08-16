<?php
defined( 'ABSPATH' ) || exit;

use Arvand\ArvandPanel\PanelControllers\WPAPPanelController;
use Arvand\ArvandPanel\Services\WPAPRouter;

$menus = wpap_get_cached_menus();

set_query_var('wpap_panel_global_data', [
    'menus' => $menus,
]);

$route = new WPAPRouter;

$route->add('dash', function () {
    wpap_view('dash.dash');
});

$route->add('comments', function () {
    wpap_view('comments');
});

$route->add('notifications', function () {
    wpap_view('notice.notifications');
});

$route->add(['notifications', 'notifications/{id}'], [WPAPPanelController::class, 'notifications']);

$route->add('user_edit', function () {
    wpap_view('user-edit');
});

$route->add('new_ticket', function () {
    wpap_view('ticket.create');
});

$route->add('tickets', function () {
    wpap_view('ticket.list.list');
});

$route->add(['tickets', 'tickets/{id}'], [WPAPPanelController::class, 'singleTicket']);

$route->add('private_msg', function () {
    wpap_view('message.list');
});

$route->add(['private_msg', 'private-msg/{id}'], [WPAPPanelController::class, 'singleMessage']);

$route->add('change_email', function () {
    wpap_view('auth.change-email');
});

$route->add('mobile', function () {
    wpap_view('auth/mobile');
});

$route->add('change_password', function () {
    wpap_view('auth.change-password');
});

$route->add('wallet_topup', function () {
    wpap_view('shop.wallet.top-up');
});

$route->add('wallet_transactions', function () {
    wpap_view('shop.wallet.transactions');
});

$route->add('wc_orders', function () {
    wpap_view('shop.orders');
});

$route->add(['wc_orders', 'orders/{id}'], function ($id) {
    wpap_view('shop.orders', compact('id'));
});

$route->add('wc_downloads', function () {
    wpap_view('shop.downloads');
});

$route->add('bookmarked', function () {
    wpap_view('shop.bookmarked');
});

$route->add('visited_products', function () {
    wpap_view('shop.visited-products');
});

$route->add('wc_addresses', function () {
    wpap_view('shop.addresses');
});

$route->add(['wc_addresses', 'addresses/{address_type}'], function ($address_type) {
    wpap_view('shop.addresses', compact('address_type'));
});

$route->add('wc_edit_account', function () {
    wpap_view('shop.edit-account');
});

// Custom menus routes
foreach ($menus as $menu) {
    if ('shortcode' === $menu->menu_type) {
        $route->add($menu->menu_name, function () use ($menu) {
            wpap_view('custom-menu-content.shortcode', ['shortcode' => $menu->menu_content]);
        });
    }

    if ('page' === $menu->menu_type) {
        $route->add($menu->menu_name, function () use ($menu) {
            $post = get_post(absint($menu->menu_post_id));
            if (!$post) {
                wp_redirect(wpap_get_page_url_by_name('dash'));
            }

            wpap_view('custom-menu-content.page', ['post' => $post]);
        });
    }

    if ('text' === $menu->menu_type) {
        $route->add($menu->menu_name, function () use ($menu) {
            wpap_view('custom-menu-content/text', ['text' => $menu->menu_content]);
        });
    }
}

$route->dispatch();