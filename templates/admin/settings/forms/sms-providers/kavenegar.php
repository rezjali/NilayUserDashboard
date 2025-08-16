<?php
defined('ABSPATH') || exit;

$opt = wpap_sms_provider_options('kavenegar');
?>

<form id="wpap-kavenegar" class="wpap-form wpap-sms-settings-form" method="post"
      style="<?php echo $sms['provider'] === 'kavenegar' ? 'display: block' : ''; ?>">
    <input type="hidden" name="sms_nonce" value="<?php echo wp_create_nonce('sms_nonce'); ?>"/>
    <input type="hidden" name="form" value="wpap_kavenegar"/>
    <input type="hidden" name="provider_name" value="kavenegar"/>

    <div class="wpap-field-wrap">
        <label><?php esc_html_e('API key', 'arvand-panel'); ?></label>
        <input type="text" name="api_key" value="<?php echo esc_attr($opt['api_key']); ?>"/>
    </div>

    <div class="wpap-field-wrap">
        <label><?php esc_html_e('From', 'arvand-panel'); ?></label>

        <div>
            <input type="text" name="from" value="<?php echo esc_attr($opt['from']); ?>"/>
            <p class="description"><?php esc_html_e('Your sms service number.', 'arvand-panel'); ?></p>
        </div>
    </div>

    <div class="wpap-field-wrap">
        <label><?php esc_html_e('Pattern code', 'arvand-panel'); ?></label>

        <div>
            <input type="text" name="pattern_code" value="<?php echo esc_attr($opt['pattern_code']); ?>"/>

            <p class="description">
                <?php esc_html_e('جهت ارسال کد اعتبارسنجی ورود و ثبت نام. طبق پترنی باشد که در پنل کاوه نگار تعریف کرده اید.', 'arvand-panel'); ?>
            </p>
        </div>
    </div>

    <div class="wpap-field-wrap">
        <label><?php esc_html_e('مثال متن پیامک پترن', 'arvand-panel'); ?></label>

        <div>
            <input type="text" value="<?php esc_attr_e('تشکر از عضویت در اروند وردپرس، کد تایید: token%', 'arvand-panel'); ?>"
                   readonly>

            <p class="description">
                <span><?php esc_html_e('کد اعتبارسنجی: ', 'arvand-panel'); ?></span>
                <code>%token</code>
            </p>
        </div>
    </div>

    <footer>
        <?php require WPAP_ADMIN_TEMPLATES_PATH . 'parts/button.php'; ?>
    </footer>
</form>