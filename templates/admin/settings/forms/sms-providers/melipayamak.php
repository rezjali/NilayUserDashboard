<?php
defined('ABSPATH') || exit;

$opt = wpap_sms_provider_options('melipayamak');
?>

<form id="wpap-melipayamak" class="wpap-form wpap-sms-settings-form" method="post"
      style="<?php echo $sms['provider'] === 'melipayamak' ? 'display: block' : ''; ?>">
    <input type="hidden" name="sms_nonce" value="<?php echo wp_create_nonce('sms_nonce'); ?>"/>
    <input type="hidden" name="form" value="wpap_melipayamak"/>
    <input type="hidden" name="provider_name" value="melipayamak"/>

    <div class="wpap-field-wrap">
        <label><?php esc_html_e('Your sms provider username', 'arvand-panel'); ?></label>
        <input type="text" name="username" value="<?php echo esc_attr($opt['username']); ?>"/>
    </div>

    <div class="wpap-field-wrap">
        <label><?php esc_html_e('Your sms provider password', 'arvand-panel'); ?></label>
        <input type="password" name="password" value="<?php echo esc_attr($opt['password']); ?>"/>
    </div>

    <div class="wpap-field-wrap">
        <label><?php esc_html_e('From', 'arvand-panel'); ?></label>

        <div>
            <input type="text" name="from" value="<?php echo esc_attr($opt['from']); ?>"/>
            <p class="description"><?php esc_html_e('شماره خط خدماتی (برای پیامک ساده)', 'arvand-panel'); ?></p>
        </div>
    </div>

    <div class="wpap-field-wrap">
        <label><?php esc_html_e('Pattern code', 'arvand-panel'); ?></label>

        <div>
            <input type="text" name="pattern_code" value="<?php echo esc_attr($opt['pattern_code']); ?>"/>
            <p class="description"><?php esc_html_e('جهت ارسال کد اعتبارسنجی ورود و ثبت نام. طبق متن الگویی باشد که در پنل ملی پیامک تعریف کرده اید.', 'arvand-panel'); ?></p>
        </div>
    </div>

    <div class="wpap-field-wrap">
        <label><?php esc_html_e('مثال متن پیامک پترن', 'arvand-panel'); ?></label>

        <div>
            <input type="text"
                   value="<?php esc_attr_e('کاربر گرامی کد تأیید شما: {0} می‌باشد. https://arvandwp.ir', 'arvand-panel'); ?>"
                   readonly>
            <p class="description"><?php esc_html_e('جهت ارسال کد اعتبارسنجی ورود و ثبت نام. در قسمت "ابزار ویژه" سپس "وب سرویس خدماتی" در پنل ملی پیامک، متن الگوی خود را بسازید و از متغیر زیر استفاده کنید.', 'arvand-panel'); ?></p>

            <p class="description">
                <span><?php esc_html_e('کد اعتبارسنجی: ', 'arvand-panel'); ?></span>
                <code>{0}</code>
            </p>
        </div>
    </div>

    <footer>
        <?php require WPAP_ADMIN_TEMPLATES_PATH . 'parts/button.php'; ?>
    </footer>
</form>