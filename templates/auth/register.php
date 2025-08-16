<?php
defined('ABSPATH') || exit;

$login_opt = wpap_login_options();
$pages_opt = wpap_pages_options();
?>

<div class="wpap-form-wrap">
    <?php
    if ($register['enable_def_reg']) {
        require WPAP_TEMPLATES_PATH . 'auth/register-form.php';
    }
    ?>

    <?php if ($login_opt['enable_def_login'] || $login_opt['enable_sms_register_login']): ?>
        <div id="wpap-form-links">
            <?php if ($login_opt['enable_def_login']): ?>
                <a href="<?php echo get_permalink($pages_opt['login_page_id']); ?>">
                    <?php esc_html_e('ورود با نام کاربری', 'arvand-panel'); ?>
                </a>
            <?php endif; ?>

            <?php if ($login_opt['enable_sms_register_login']): ?>
                <a href="<?php echo get_permalink($pages_opt['sms_register_login_page_id']); ?>">
                    <?php esc_html_e('ورود/ثبت نام با شماره همراه', 'arvand-panel'); ?>
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
