<?php defined('ABSPATH') || exit; ?>

<form id="wpap-sms-login-form" class="wpap-auth-form" method="post">
    <div class="wpap-fields">
        <?php wp_nonce_field('sms_login_verify_nonce', 'sms_login_verify_nonce'); ?>

        <?php do_action('wpap_top_sms_login_verify_form'); ?>

        <?php if ($attributes['redirect_url'] === 'current'): ?>
            <input type="text" name="redirect_to" value="" hidden="hidden"/>
        <?php elseif (!empty($attributes['redirect_url'])): ?>
            <input type="text" name="redirect_to" value="<?php echo esc_attr($attributes['redirect_url']); ?>"
                   hidden="hidden"/>
        <?php elseif ($redirect_page_id > 0): ?>
            <input type="text" name="redirect_to" value="<?php echo get_permalink($redirect_page_id); ?>"
                   hidden="hidden"/>
        <?php else: ?>
            <input type="text" name="redirect_to"
                   value="<?php echo get_permalink($pages_opt['after_sms_register_login_page_id']); ?>"
                   hidden="hidden"/>
        <?php endif; ?>

        <label class="wpap-field-wrap">
            <span class="wpap-field-label">
                <?php esc_html_e('Enter verification code', 'arvand-panel'); ?>
            </span>

            <input type="text" name="verification_code" />
        </label>

        <footer>
            <button class="wpap-btn-1" type="submit">
                <span class="wpap-btn-text"><?php esc_html_e('Login', 'arvand-panel'); ?></span>
                <div class="wpap-loading"></div>
            </button>
        </footer>

        <div id="wpap-form-buttons">
            <a href="" id="wpap-change-phone-btn">
                <i class="ri-edit-line"></i>
                <span><?php esc_html_e('Change number', 'arvand-panel'); ?></span>
            </a>
        </div>

        <?php do_action('wpap_bottom_sms_login_verify_form'); ?>
    </div>
</form>

<?php if ($login['enable_def_login']): ?>
    <div id="wpap-form-links">
        <a href="<?php echo get_permalink($pages_opt['login_page_id']); ?>">
            <?php esc_html_e('ورود با نام کاربری/ایمیل', 'arvand-panel'); ?>
        </a>
    </div>
<?php endif; ?>