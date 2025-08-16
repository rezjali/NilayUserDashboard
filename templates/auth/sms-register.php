<?php defined('ABSPATH') || exit; ?>

<form id="wpap-sms-register-form" class="wpap-auth-form" method="post">
    <div class="wpap-fields">
        <?php wp_nonce_field('sms_register_verify_nonce', 'sms_register_verify_nonce'); ?>

        <?php if ($attributes['redirect_url'] === 'current'): ?>
            <input type="text" name="redirect_to" value="" hidden="hidden"/>
        <?php elseif (!empty($attributes['redirect_url'])): ?>
            <input type="text" name="redirect_to" value="<?php echo esc_attr($attributes['redirect_url']); ?>"
                   hidden="hidden"/>
        <?php elseif ($redirect_page_id > 0): ?>
            <input type="text" name="redirect_to" value="<?php echo get_permalink($redirect_page_id); ?>" hidden="hidden"/>
        <?php else: ?>
            <input type="text" name="redirect_to"
                   value="<?php echo get_permalink($pages_opt['after_sms_register_login_page_id']); ?>" hidden="hidden"/>
        <?php endif; ?>

        <label class="wpap-field-wrap">
            <span class="wpap-field-label">
                <?php esc_html_e('Enter verification code', 'arvand-panel'); ?>
            </span>

            <input type="text" name="verification_code" />
        </label>

        <?php do_action('wpap_top_sms_register_verify_form'); ?>

        <?php if ($register['sms_reg_password']): ?>
            <?php $pass_opt = \Arvand\ArvandPanel\Form\WPAPFieldSettings::password(); ?>

            <label id="wpap-password-inputs-wrap" class="wpap-field-wrap">
                <span class="wpap-field-label">
                    <?php esc_html_e('Password', 'arvand-panel') ?>
                </span>

                <input type='password' name="user_pass" />

                <?php if (!empty($settings['description'])): ?>
                    <span class='wpap-input-info'><?php esc_html_e($settings['description']); ?></span>
                <?php endif; ?>
            </label>
        <?php endif; ?>

        <?php do_action('wpap_bottom_sms_register_verify_form'); ?>

        <?php if ($register['enable_agree']): ?>
            <label class="wpap-field-wrap">
                <span id="wpap-agree" class="wpap-checkbox-wrap">
                    <label for="wpap-sms-reg-agree">
                        <input id="wpap-sms-reg-agree" type="checkbox" name="sms_reg_agree"/>
                        <span class="wpap-checkbox"></span>

                        <span id="wpap-agree-text">
                            <?php echo wp_kses($register['agree_text'], 'post'); ?>
                        </span>
                    </label>
                </span>
            </label>
        <?php endif; ?>

        <footer>
            <button class="wpap-btn-1" type="submit">
                <span class="wpap-btn-text"><?php esc_html_e('Register', 'arvand-panel'); ?></span>
                <div class="wpap-loading"></div>
            </button>
        </footer>

        <div id="wpap-form-buttons">
            <a href="" id="wpap-change-phone-btn">
                <i class="ri-edit-line"></i>
                <span><?php esc_html_e('Change number', 'arvand-panel'); ?></span>
            </a>
        </div>
    </div>
</form>