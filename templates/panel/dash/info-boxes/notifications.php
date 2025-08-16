<?php
defined('ABSPATH') || exit;

if (!wpap_is_valid_section('notifications')) {
    return;
}

global $current_user;
?>

<a href="<?php echo esc_url(wpap_get_page_url_by_name('notifications')); ?>">
    <div class="wpap-dash-info-box">
        <?php
        printf(
            '<i style="background-color: %s; color: %s;" class="%s"></i>',
            esc_attr($box['icon_bg']),
            esc_attr($box['icon_color']),
            esc_attr($box['icon'])
        );
        ?>

        <h3><?php esc_html_e($box['title']); ?></h3>

        <span>
            <?php
            if ($count = \Arvand\ArvandPanel\WPAPNotification::getUnseenCount($current_user)) {
                echo sprintf(esc_html__('%d notice(s) from admin.', 'arvand-panel'), $count);
            } else {
                esc_html_e('There is no new notice.', 'arvand-panel');
            }
            ?>
        </span>
    </div>
</a>