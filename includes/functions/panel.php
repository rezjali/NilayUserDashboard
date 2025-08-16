<?php
defined('ABSPATH') || exit;

use Arvand\ArvandPanel\WPAPMessage;
use Arvand\ArvandPanel\WPAPNotification;

function wpap_panel_url($path = null): string
{
    if (is_object($path)) {
        $path = $path->route;
    }

    return esc_url(get_permalink(wpap_pages_options()['panel_page_id']) . $path);
}

function wpap_get_page_url_by_name(string $name, $path = ''): string
{
    $menu = wpap_get_cached_menus($name);
    return $menu ? wpap_panel_url($menu->route . ($path ? "/$path" : '')) : '';
}

function wpap_get_panel_page()
{
    $panel_page = get_post(wpap_pages_options()['panel_page_id']);
    return $panel_page ?? null;
}

function wpap_is_panel_page(): bool
{
    return is_page(wpap_pages_options()['panel_page_id']);
}

function wpap_get_global_data(string $field)
{
    $globals = get_query_var('wpap_panel_global_data');
    return $globals[$field] ?? null;
}

function wpap_current_route(): ?string
{
    return get_query_var('wpap_current_route');
}

function wpap_get_cached_menus(string $name = null)
{
    $menus = get_option('wpap_menus_cache') ?? [];

    if (!$menus) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'wpap_menus';

        $result = $wpdb->get_results("SELECT * FROM $table_name ORDER BY `menu_order` ASC");
        if ($result) {
            $new_menus = [];

            foreach ($result as $menu) {
                $menu->menu_icon = json_decode($menu->menu_icon, true);
                $menu->menu_access = maybe_unserialize($menu->menu_access);
                $new_menus[$menu->menu_name] = $menu;
            }

            update_option('wpap_menus_cache', $new_menus);
        }

        $menus = $new_menus ?? [];
    }

    return $name ? ($menus[$name] ?? null) : $menus;
}

function wpap_panel_menus_html($menus, $parent = null): ?string
{
    if (!is_array($menus)) {
        return null;
    }

    global $current_user;
    $current_route = wpap_current_route();
    $output = "";
    $parent_id = $parent ? $parent->menu_id : "0";
    $pages_opt = wpap_pages_options();

    foreach ($menus as $menu) {
        if ($menu->menu_parent != $parent_id || !wpap_is_valid_section($menu->menu_name)) {
            continue;
        }

        $output .= "<li>";

        // Menu url
        $url = wpap_panel_url($menu->route);
        if ('home' === $menu->menu_name) {
            $url = home_url();
        }
        if ('logout' === $menu->menu_name) {
            $url = wp_logout_url(get_permalink($pages_opt['after_logout_page_id']));
        }
        if ('link' === $menu->menu_type) {
            $url = $menu->menu_content;
        }

        // Menu icon
        $icon_data = $menu->menu_icon;
        $icon = sprintf(
            '<i %s class="wpap-menu-icon %s %s"></i>',
            1 == $icon_data['color_type'] ? 'style="color: ' . esc_attr($icon_data['color']) . ' !important"' : '',
            esc_attr($icon_data['classes']),
            1 == $icon_data['color_type'] ? 'wpap-icon-custom-color' : ''
        );
        $icon_image = wp_get_attachment_image(
            $icon_data['image_id'] ?? -1,
            'thumbnail',
            false,
            ['style' => 'width: 20px; height: 20px; object-fit: contain']
        );
        if ($icon_image) {
            $icon = '<i class="wpap-menu-icon-img">' . $icon_image . '</i>';
        }

        // Notice icon
        $notice_icon = '';
        if (('notifications' === $menu->menu_name && WPAPNotification::getUnseenCount($current_user))
            || ('private_msg' === $menu->menu_name && WPAPMessage::unseenCount())
        ) {
            $notice_icon = '<i class="wpap-notice-icon bi bi-bell-fill"></i>';
        }

        $submenu = wpap_panel_menus_html($menus, $menu);
        $has_submenu = !empty($submenu);
        $is_active = isset($menus[$current_route]) && $menus[$current_route]->menu_id == $menu->menu_id;
        $is_submenu_opened = isset($menus[$current_route]) && $menus[$current_route]->menu_parent == $menu->menu_id;

        $output .= sprintf(
            '<a href="%s" class="%s %s %s" %s>%s<span>%s</span>%s%s</a>',
            esc_url($url),
            $has_submenu ? 'wpap-has-children' : '',
            $is_active ? 'wpap-active' : '',
            $is_submenu_opened ? 'wpap-submenu-opened wpap-open' : '',
            'link' === $menu->menu_type ? 'target="_blank"' : '',
            $parent ? '' : $icon,
            esc_html($menu->menu_title),
            $notice_icon,
            $has_submenu ? '<i class="ri-arrow-down-s-line"></i>' : ''
        );

        if ($has_submenu) {
            $output .= $submenu;
        }

        $output .= "</li>";
    }

    $is_submenu_opened = $parent && isset($menus[$current_route]) && $menus[$current_route]->menu_parent == $parent->menu_id;

    return $output ? sprintf(
        '<ul style="display: %s" class="%s">%s</ul>',
        $is_submenu_opened ? 'block' : ($parent ? 'none' : 'block'),
        $parent ? 'wpap-submenu' : 'wpap-menu',
        $output
    ) : '';
}

function wpap_is_valid_section(string $name = null): bool
{
    $menus = wpap_get_cached_menus();

    $menu = $name ? ($menus[$name] ?? null) : ($menus[wpap_current_route()] ?? null);
    if (!$menu) {
        return false;
    }

    // Check menu display status
    if ('hide' === $menu->menu_display) {
        return false;
    }

    // Get menu parent
    $parent = null;
    foreach ($menus as $menu2) {
        if ($menu2->menu_id == $menu->menu_parent) {
            $parent = $menu2;
            break;
        }
    }

    // Check parent display status
    if ($parent && 'hide' === $parent->menu_display) {
        return false;
    }

    // Hide mobile menu if mobile registration disabled
    if ('mobile' === $menu->menu_name && !wpap_login_options()['enable_sms_register_login']) {
        return false;
    }

    global $current_user;

    // Check menu user access
    $roles = array_values($current_user->roles);
    if (is_array($menu->menu_access) && empty(array_intersect($roles, $menu->menu_access))) {
        return false;
    }

    // Check parent menu user access
    if ($parent
        && is_array($parent->menu_access)
        && empty(array_intersect($roles, $parent->menu_access))
    ) {
        return false;
    }

    return true;
}

function wpap_view(string $view, array $data = [])
{
    set_query_var('wpap_panel_content_data', [
        'view' => str_replace('.', '/', $view),
        'data' => $data
    ]);

    return null;
}

function wpap_display_view(): void
{
    $content_data = get_query_var('wpap_panel_content_data');

    if (isset($content_data['view'])) {
        extract($content_data['data'] ?? []);
        require WPAP_TEMPLATES_PATH . '/panel/' . $content_data['view'] . '.php';
    }
}

function wpap_pagination($total): void
{
    $page = absint($_GET['panel-page'] ?? 1);
    ?>
    <div class="wpap-pagination">
        <?php
        echo paginate_links([
            'format' => '?panel-page=%#%',
            'current' => max(1, $page),
            'total' => $total
        ]);
        ?>
    </div>
    <?php
}