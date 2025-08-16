<?php
defined('ABSPATH') || exit;

$opt = wpap_sms_provider_options('sms_ir');
?>

<form id="wpap-sms_ir" class="wpap-form wpap-sms-settings-form" method="post"
      style="<?php echo $sms['provider'] === 'sms_ir' ? 'display: block' : ''; ?>">
    <input type="hidden" name="sms_nonce" value="<?php echo wp_create_nonce('sms_nonce'); ?>"/>
    <input type="hidden" name="form" value="wpap_sms_ir"/>
    <input type="hidden" name="provider_name" value="sms_ir"/>

    <div class="wpap-field-wrap">
        <label><?php esc_html_e('API key', 'arvand-panel'); ?></label>
        <input type="text" name="api_key" value="<?php echo esc_attr($opt['api_key']); ?>"/>
    </div>

    <div class="wpap-field-wrap">
        <label><?php esc_html_e('Secret key', 'arvand-panel'); ?></label>
        <input class="regular-text" type="text" name="secret_key" value="<?php echo esc_attr($opt['secret_key']); ?>"/>
    </div>

    <div class="wpap-field-wrap">
        <label><?php esc_html_e('From', 'arvand-panel'); ?></label>

        <div>
            <input type="text" name="from" value="<?php echo esc_attr($opt['from']); ?>"/>
            <p class="description"><?php esc_html_e('Your sms service number', 'arvand-panel'); ?></p>
        </div>
    </div>

    <div class="wpap-field-wrap">
        <label><?php esc_html_e('Template id', 'arvand-panel'); ?></label>

        <div>
            <input type="text" name="template_id" value="<?php echo esc_attr($opt['template_id']); ?>"/>
            <p class="description"><?php esc_html_e('جهت ارسال کد اعتبارسنجی ورود و ثبت نام. طبق شناسه قالبی باشد که در پنل sms.ir تعریف کرده اید.', 'arvand-panel'); ?></p>
        </div>
    </div>

    <div class="wpap-field-wrap">
        <label><?php esc_html_e('مثال متن پیامک پترن', 'arvand-panel'); ?></label>

        <div>
            <input type="text" value="<?php esc_attr_e('تشکر از عضویت در اروند وردپرس، کد شما: [code]', 'arvand-panel'); ?>"
                   readonly>
            <p class="description"><?php esc_html_e('جهت ارسال کد اعتبارسنجی ورود و ثبت نام. در منوی برنامه نویسات و قسمت "قالب های ماژول ارسال سریع" پنل sms.ir، قالب خود را بسازید و از متغیر زیر استفاده کنید.', 'arvand-panel'); ?></p>

            <p class="description">
                <span><?php esc_html_e('کد اعتبارسنجی: ', 'arvand-panel'); ?></span>
                <code>[code]</code>
            </p>
        </div>
    </div>

    <footer>
        <?php require WPAP_ADMIN_TEMPLATES_PATH . 'parts/button.php'; ?>
    </footer>
</form>