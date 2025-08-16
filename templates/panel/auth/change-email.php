<?php defined('ABSPATH') || exit; ?>

<div class="wpap-form-wrap">
    <form id="wpap-email-send-code-form" method="post">
        <header>
            <h2><?php esc_html_e('تغییر یا ثبت ایمیل', 'arvand-panel'); ?></h2>
        </header>

        <div>
            <?php wp_nonce_field('send_email_verification_code_nonce', 'send_email_verification_code_nonce'); ?>

            <div class="wpap-field-wrap">
                <label class="wpap-field-label" for="wpap-enail-field">
                    <?php esc_html_e('ایمیل را وارد کنید', 'arvand-panel'); ?>
                </label>

                <input id="wpap-enail-field" type="email" name="email" placeholder="<?php esc_attr_e('New email', 'arvand-panel'); ?>"/>
            </div>

            <footer>
                <button class="wpap-btn-1" type="submit">
                    <span class="wpap-btn-text"><?php esc_html_e('Send Code', 'arvand-panel'); ?></span>
                    <div class="wpap-loading"></div>
                </button>
            </footer>
        </div>
    </form>

    <form id="wpap-email-verify-form" method="post">
        <header>
            <h2><?php esc_html_e('کد تایید ایمیل را وارد کنید', 'arvand-panel'); ?></h2>
        </header>

        <div>
            <?php wp_nonce_field('verify_email_nonce', 'verify_email_nonce'); ?>

            <div class="wpap-field-wrap">
                <label class="wpap-field-label" for="wpap-verification-code-field">
                    <?php esc_html_e('کد تایید', 'arvand-panel'); ?>
                </label>

                <input id="wpap-verification-code-field" type="text"
                       name="verification_code"
                       placeholder="<?php esc_attr_e('Enter verification code', 'arvand-panel'); ?>"/>
            </div>

            <footer>
                <button class="wpap-btn-1" type="submit">
                    <span class="wpap-btn-text"><?php esc_html_e('Verify Email', 'arvand-panel'); ?></span>
                    <div class="wpap-loading"></div>
                </button>
            </footer>
        </div>
    </form>
</div>