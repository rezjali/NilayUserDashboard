<?php
defined('ABSPATH') || exit;

global $current_user;
$avatar = get_avatar($current_user->ID);
$panel_opt = wpap_panel_options();
$pages_opt = wpap_pages_options();
?>

<?php do_action('wpap_before_header'); ?>

<div id="wpap-panel-header">
    <a id="wpap-header-show-sidebar" href="#" title="<?php esc_attr_e('Show Menu', 'arvand-panel'); ?>">
        <i class='ri-menu-3-fill'></i>
    </a>

    <?php do_action('wpap_header'); ?>

    <?php if (!empty($shortcode_opt['panel_header'])): ?>
        <div class="wpap-panel-header-shortcode">
            <?php echo wp_kses_post(do_shortcode($shortcode_opt['panel_header'])); ?>
        </div>
    <?php endif; ?>

    <?php if ($avatar): ?>
        <div id="wpap-header-user-info">
            <?php if (wpap_is_valid_section('user_edit')): ?>
                <a href="<?php echo esc_url(wpap_get_page_url_by_name('user_edit')); ?>"
                   id="wpap-user-avatar">
                    <?php echo $avatar; ?>
                </a>
            <?php else: ?>
                <div id="wpap-user-avatar"><?php echo $avatar; ?></div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <a id="wpap-header-show-notice-wrap" href="#" title="<?php esc_attr_e('notifications', 'arvand-panel'); ?>">
        <i class="ri-notification-2-line"></i>

        <?php
        $count = 0;
        $count += \Arvand\ArvandPanel\WPAPNotification::getUnseenCount($current_user);
        $count += \Arvand\ArvandPanel\WPAPMessage::unseenCount();
        if ($count) {
            echo '<span class="wpap-unread-count">' . esc_html($count) . '</span>';
        }
        ?>
    </a>

    <a id="wpap-logout-link"
       href="<?php echo esc_url(wp_logout_url(get_permalink($pages_opt['after_logout_page_id']))); ?>"
       title="<?php esc_html_e('خروج', 'arvand-panel'); ?>">
        <i class="ri-logout-circle-line"></i>
    </a>
</div>

<?php do_action('wpap_after_header'); ?>