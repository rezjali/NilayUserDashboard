<?php
defined('ABSPATH') || exit;

global $current_user;
?>

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
        echo esc_html(
            date_i18n(get_option('date_format'), strtotime($current_user->user_registered))
        );
        ?>
    </span>
</div>