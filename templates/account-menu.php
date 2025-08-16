<?php
defined('ABSPATH') || exit;

$colors = wpap_colors();
$opt = wpap_account_menu_options();
$pages_opt = wpap_pages_options();
?>

<style id="wpap-profile-menu-colors">
    #wpap-profile-menu {
        --wpap-color-1: <?php echo esc_html($colors['am_color_1']); ?>;
        --wpap-color-2: <?php echo esc_html($colors['am_color_2']); ?>;
        --wpap-bg-color-1: <?php echo esc_html($colors['am_bg_color_1']); ?>;
        --wpap-bg-color-2: <?php echo esc_html($colors['am_bg_color_2']); ?>;
        --wpap-text-color-1: <?php echo esc_html($colors['am_text_color_1']); ?>;
        --wpap-text-color-2: <?php echo esc_html($colors['am_text_color_2']); ?>;
        --wpap-text-color-3: <?php echo esc_html($colors['am_text_color_3']); ?>;
        --wpap-border-color-1: <?php echo esc_html($colors['am_border_color_1']); ?>;
        --wpap-border-color-2: <?php echo esc_html($colors['am_border_color_2']); ?>;
    }
</style>

<div id="wpap-profile-menu">
    <?php if (!is_user_logged_in()):
        $login_page_url = get_permalink($pages_opt['login_page_id']);
        $sms_reg_login_page_url = get_permalink($pages_opt['sms_register_login_page_id']);
        $register_page_url = get_permalink($pages_opt['register_page_id']); ?>

        <?php if ('sms_signup_login' === $opt['non_logged_in_btn']): ?>
            <div id="wpap-register-login">
                <a id="wpap-profile-menu-login" href="<?php echo $sms_reg_login_page_url; ?>">
                    <?php esc_html_e('Login or Signup', 'arvand-panel'); ?>
                </a>
            </div>
        <?php else: ?>
            <div id="wpap-register-login">
                <a id="wpap-profile-menu-login" href="<?php echo $login_page_url; ?>">
                    <?php esc_html_e('Login', 'arvand-panel'); ?>
                </a>

                <span> | </span>

                <a id="wpap-profile-menu-register" href="<?php echo $register_page_url; ?>">
                    <?php esc_html_e('Register', 'arvand-panel'); ?>
                </a>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <?php
        global $current_user;
        $notice_count = \Arvand\ArvandPanel\WPAPNotification::getUnseenCount($current_user);
        $msg_count = \Arvand\ArvandPanel\WPAPMessage::unseenCount();
        $count = $notice_count + $msg_count;
        ?>

        <?php if ($opt['logged_in_btn'] === 'icon_text'): ?>
            <button class="wpap-profile-menu-btn">
                <i class='<?php echo esc_attr($opt['logged_in_btn_icon']); ?>'></i>

                <span>
                    <?php echo apply_filters('wpap_account_menu_logged_in_btn_text', esc_html($opt['logged_in_btn_text']), $current_user); ?>
                </span>

                <?php
                if ($count > 0) {
                    echo '<span class="wpap-unread-count">' . esc_html($count) . '</span>';
                }
                ?>
            </button>
        <?php else: ?>
            <?php
            echo get_avatar($current_user->ID, '', '', '', ['class' => ['wpap-profile-menu-btn']]);

            if ($count > 0) {
                echo '<span class="wpap-unread-count">' . esc_html($count) . '</span>';
            }
            ?>
        <?php endif; ?>

        <div id="profile-menu-wrap">
            <a href="<?php echo esc_url(wpap_panel_url()); ?>">
                <i class="ri-user-3-line"></i>

                <?php
                echo apply_filters(
                    'wpap_account_menu_wrap_user_display_name',
                    "<span>$current_user->display_name</span>", $current_user
                );
                ?>
            </a>

            <div>
                <?php
                $after_logout_page_url = get_permalink($pages_opt['after_logout_page_id']);
                $menu_db = new \Arvand\ArvandPanel\DB\WPAPMenuDB();
                $menus = $menu_db->getAccountMenus((array) $opt['menus']);

                apply_filters('wpap_account_menu_links', $menus, $current_user);
                ?>

                <?php foreach ($menus as $menu): ?>
                    <?php
                    if (!wpap_is_valid_section($menu->menu_name)) {
                        continue;
                    }
                    ?>

                    <a href="<?php echo esc_url(wpap_get_page_url_by_name($menu->menu_name)); ?>">
                        <?php
                        $icon = \Arvand\ArvandPanel\WPAPMenu::icon($menu);

                        if ($icon['image_id'] && $icon_image = wp_get_attachment_image($icon['image_id'])) {
                            printf('<i>%s</i>', $icon_image);
                        } else {
                            printf(
                                '<i %s class="%s"></i>',
                                1 == $icon['color_type'] ? 'style="color: ' . $icon['color'] . '"' : '',
                                $icon['classes']
                            );
                        }

                        printf('<span>%s</span>', esc_html($menu->menu_title));

                        if ('notifications' === $menu->menu_name && $notice_count > 0) {
                            printf('<span  class="wpap-unread-count">%s</span>', esc_html($notice_count));
                        }

                        if ('private-msg' === $menu->menu_name && $msg_count > 0) {
                            printf('<span  class="wpap-unread-count">%s</span>', esc_html($msg_count));
                        }
                        ?>
                    </a>
                <?php endforeach; ?>

                <?php $logout_url = $after_logout_page_url ? wp_logout_url($after_logout_page_url) : wp_logout_url(); ?>

                <a id="wpap-logout-link" href="<?php echo esc_url($logout_url); ?>">
                    <i class="ri-logout-circle-line"></i>
                    <span><?php esc_html_e('Logout', 'arvand-panel'); ?></span>
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>