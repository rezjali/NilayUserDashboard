<?php
defined('ABSPATH') || exit;

$pages_opt = wpap_pages_options();
$register_page_url = get_permalink($pages_opt['register_page_id']);
$login_page_url = get_permalink($pages_opt['login_page_id']);
$lost_pass_page_url = get_permalink($pages_opt['lost_pass_page_id']);
?>

<div id="wpap-form-links">
    <?php
    if (in_array($page, ['register', 'lost-pass'])) {
        echo "<a href='$login_page_url'>" . esc_html__('ورود به حساب', 'arvand-panel') . "</a>";
    }

    if (in_array($page, ['login', 'lost-pass'])) {
        echo "<a href='$register_page_url'>" . esc_html__('ثبت نام', 'arvand-panel') . "</a>";
    }

    if ($page === 'login' and $login['enable_def_login']) {
        echo "<a href='$lost_pass_page_url'>" . esc_html__('بازیابی رمز عبور', 'arvand-panel') . "</a>";
    }
    ?>
</div>