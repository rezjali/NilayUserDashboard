<?php
defined('ABSPATH') || exit;

use Arvand\ArvandPanel\SMS\WPAPSMS;

function wpap_admin_notice(string $message, $type = 'info'): void
{
    printf(
        '<div class="wpap-message wpap-message-%s"><i class="%s"></i><strong>%s</strong></div>',
        $type,
        'success' === $type ? 'ri-checkbox-circle-line' : 'ri-close-circle-line',
        $message
    );
}

function wpap_get_settings_current_section(): string
{
    $section = isset($_GET['section']) ? sanitize_key($_GET['section']) : 'general';

    return file_exists(WPAP_ADMIN_TEMPLATES_PATH . "settings/forms/$section.php")
        ? $section
        : 'general';
}

function wpap_settings_menu(array $menus, int $level = 0): bool
{
    $active_section = wpap_get_settings_current_section();
    $has_active = false;
    ?>
    <ul class="wpap-menu <?php echo $level ? 'wpap-submenu' : 'wpap-main-menu'; ?>">
        <?php foreach ($menus as $menu): ?>
            <?php
            $is_active = isset($menu['name']) && $menu['name'] === $active_section;
            $submenu_html = '';
            $child_active = false;

            $has_sub = !empty($menu['submenus']);
            if ($has_sub) {
                ob_start();
                $child_active = wpap_settings_menu($menu['submenus'], $level + 1);
                $submenu_html = ob_get_clean();
            }

            $is_open = $is_active || $child_active;
            if ($is_open) {
                $has_active = true;
            }
            ?>

            <li class="wpap-menu-item <?php echo $has_sub ? 'wpap-has-sub' : ''; ?> <?php echo $is_open ? 'wpap-open' : ''; ?>">
                <a class="<?php echo $is_active ? 'wpap-active' : ''; ?>"
                   href="<?php echo $has_sub ? '#' : esc_url($menu['url'] ?? ''); ?>"
                   style="padding-right: <?php echo max(15, $level * 15); ?>px;">
                    <span><?php echo esc_html($menu['label']); ?></span>

                    <?php if ($has_sub): ?>
                        <i class="ri-arrow-up-s-line wpap-submenu-toggle"></i>
                    <?php endif; ?>

                    <?php if (!$level): ?>
                        <i class="ri-<?php echo esc_attr($menu['icon']); ?>"></i>
                    <?php endif; ?>
                </a>

                <?php echo $submenu_html; ?>
            </li>
        <?php endforeach; ?>
    </ul>
    <?php
    return $has_active;
}

function wpap_gateway_settings_save(): string
{
    update_option('wpap_sms', ['provider' => sanitize_text_field($_POST['provider_name'])]);
    return WPAPSMS::supportedGateways()[$_POST['provider_name']];
}