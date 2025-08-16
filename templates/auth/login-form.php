<?php
defined('ABSPATH') || exit;

$google_opt = wpap_google_options();

if ($login['enable_sms_register_login'] || Arvand\ArvandPanel\Form\WPAPFieldSettings::get('mobile')) {
    $user_login_label = __('Username, email or mobile number', 'arvand-panel');
} else {
    $user_login_label = __('Username or email', 'arvand-panel');
}
?>

<form id="wpap-login-form" class="wpap-auth-form" action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" method="post">
    <div class="wpap-fields">
        <?php do_action('wpap_top_login_form'); ?>

        <?php
        if (
            !$register_opt['enable_admin_approval']
            && $register_opt['register_activation']
            && !empty($_GET['email'])
            && is_email($_GET['email'])
            && !empty($_GET['key'])
        ) {
            $user = get_user_by('email', sanitize_email($_GET['email']));

            if ($user && $user->user_activation_key === $_GET['key']) {
                $status = get_user_meta($user->ID, 'wpap_user_status', true);

                if (!$status) {
                    $update = update_user_meta($user->ID, 'wpap_user_status', 1);

                    if ($update) {
                        wpap_print_notice(esc_html__('Your account has been activated. You can log in now.', 'arvand-panel'), 'success');
                    }
                }
            } else {
                wpap_print_notice(esc_html__('Invalid Request.', 'arvand-panel'), 'error');
            }
        }

        if (isset($_GET['login']) && $_GET['login'] === 'invalid_email') {
            wpap_print_notice(esc_html__('Invalid Email.', 'arvand-panel'), 'error');
        }

        if (isset($_GET['login']) && $_GET['login'] === 'invalidkey') {
            wpap_print_notice(esc_html__('Invalid code.', 'arvand-panel'), 'error');
        }

        if (isset($_GET['checkemail']) && $_GET['checkemail'] === 'confirm') {
            wpap_print_notice(esc_html__('Password recovery link has been sent to your email. Please check your email and click on the link.', 'arvand-panel'), 'success');
        }

        if (isset($_GET['login']) && $_GET['login'] === 'expiredkey') {
            wpap_print_notice(esc_html__('Password recovery link has expired.', 'arvand-panel'), 'error');
        }

        if (isset($_GET['password']) && $_GET['password'] === 'changed') {
            wpap_print_notice(esc_html__('log in now.', 'arvand-panel'), 'success');
        }

        wp_nonce_field('wpap_login', 'wpap_login_nonce')
        ?>

        <label class="wpap-field-wrap">
            <span class="wpap-field-label">
                <?php echo esc_html($user_login_label); ?>
            </span>

            <input type="text" name="user_login" />
        </label>

        <label class="wpap-field-wrap">
            <span class="wpap-field-label">
                <?php esc_html_e('رمز عبور', 'arvand-panel'); ?>
            </span>

            <input type="password" name="user_pass" autocomplete="off"/>
        </label>

        <label class="wpap-field-wrap">
            <span class="wpap-checkbox-wrap">
                <label for="wpap-remember">
                    <input id="wpap-remember" name="remember" type="checkbox"/>
                    <span class="wpap-checkbox"></span>
                    <?php esc_html_e('Remember', 'arvand-panel'); ?>
                </label>
            </span>
        </label>

        <?php do_action('wpap_bottom_login_form'); ?>

        <?php if ($google_opt['enable_recaptcha'] && !empty($google_opt['recaptcha_site_key'])): ?>
            <script src="https://www.google.com/recaptcha/api.js?hl=fa" async defer></script>
            <div class="g-recaptcha" data-sitekey="<?php esc_attr_e($google_opt['recaptcha_site_key']); ?>"></div>
        <?php endif; ?>

        <footer>
            <button class="wpap-btn-1" type="submit">
                <span class="wpap-btn-text"><?php esc_html_e('Login', 'arvand-panel'); ?></span>
                <div class="wpap-loading"></div>
            </button>
        </footer>
    </div>
</form>
