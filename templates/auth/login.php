<?php
defined('ABSPATH') || exit;

$register_opt = wpap_register_options();
$pages_opt = wpap_pages_options();
?>

<div class="wpap-form-wrap">
    <?php
    do_action('wpap_before_login_forms');

    if ($login['enable_def_login']) {
        require WPAP_TEMPLATES_PATH . 'auth/login-form.php';
        require WPAP_TEMPLATES_PATH . 'auth/send-activation-link.php';
        require WPAP_TEMPLATES_PATH . 'auth/force-add-mobile.php';
    } else {
        wpap_print_notice(__('Login disabled.', 'arvand-panel'), 'info', false);
    }
    ?>

    <?php if ($login['enable_def_login'] || $register_opt['enable_def_reg'] || $login['enable_sms_register_login']): ?>
        <div id="wpap-form-links">
            <?php if ($register_opt['enable_def_reg']): ?>
                <a href="<?php echo get_permalink($pages_opt['register_page_id']); ?>">
                    <?php esc_html_e('ثبت نام', 'arvand-panel'); ?>
                </a>
            <?php endif; ?>

            <?php if ($login['enable_sms_register_login']): ?>
                <a href="<?php echo get_permalink($pages_opt['sms_register_login_page_id']); ?>">
                    <?php esc_html_e('ورود/ثبت نام با شماره همراه', 'arvand-panel'); ?>
                </a>
            <?php endif; ?>

            <?php if ($login['enable_def_login']): ?>
                <a href="<?php echo get_permalink($pages_opt['lost_pass_page_id']); ?>">
                    <?php esc_html_e('فراموشی رمز عبور', 'arvand-panel'); ?>
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
