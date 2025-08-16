<?php
defined('ABSPATH') || exit;

global $current_user;
$theme = get_user_meta($current_user->ID, 'wpap_panel_theme', true);
$current_route = wpap_current_route();

require WPAP_TEMPLATES_PATH . 'panel/styles.php';
?>

<div id="wpap-user-panel" data-theme="<?php echo esc_attr('dark' === $theme ? 'dark' : 'light'); ?>">
    <?php
    require WPAP_TEMPLATES_PATH . 'panel/sidebar.php';
    require WPAP_TEMPLATES_PATH . 'panel/header/notice.php';
    ?>

    <div>
        <?php require WPAP_TEMPLATES_PATH . 'panel/header/header.php'; ?>

        <div id="wpap-content">
            <?php
            if ('notifications' !== wpap_current_route()) {
                echo \Arvand\ArvandPanel\WPAPNotification::importantNotices($current_route, $current_user);
            }

            wpap_display_view();
            ?>
        </div>
    </div>
</div>