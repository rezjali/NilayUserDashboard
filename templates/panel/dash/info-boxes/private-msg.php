<?php
defined('ABSPATH') || exit;

if (!wpap_is_valid_section('private_msg')) {
    return;
}

$unread_msg_count = \Arvand\ArvandPanel\WPAPMessage::unseenCount();
?>

<a href="<?php echo esc_url(wpap_get_page_url_by_name('private_msg')); ?>">
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
            if ($unread_msg_count) {
                echo sprintf(esc_html__('%d messages.', 'arvand-panel'), $unread_msg_count);
            } else {
                esc_html_e('There is no message.', 'arvand-panel');
            }
            ?>
        </span>
    </div>
</a>