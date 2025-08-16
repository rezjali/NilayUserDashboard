<?php
defined('ABSPATH') || exit;

$styles = wpap_styles();
?>

<div id="wpap-styles">
    <form id="wpap-styles-form" class="wpap-form" method="post">
        <?php wp_nonce_field('styles_nonce', 'styles_nonce'); ?>
        <input type="hidden" name="form" value="wpap_styles"/>

        <div class="wpap-field-wrap">
            <label for="wpap-panel-z-index">
                <?php esc_html_e('z-index پنل کاربری', 'arvand-panel'); ?>
            </label>

            <input id="wpap-panel-z-index" type="number" name="panel_z_index"
                   value="<?php echo esc_attr($styles['panel_z_index']); ?>" min="1">

            <p class="description">
                <?php esc_html_e('این ویژگی زمانی اعمال میشود که گزینه "سازگاری با حالت تمام صفحه" در تنظیمان "ناحیه کاربری" فعال شده باشد.', 'arvand-panel'); ?>
            </p>
        </div>

        <div class="wpap-field-wrap">
            <label for="wpap-panel-logo-width">
                <?php esc_html_e('عرض لوگوی پنل (px)', 'arvand-panel'); ?>
            </label>

            <input id="wpap-panel-logo-width" type="number" name="panel_logo_width" value="<?php echo esc_attr($styles['panel_logo_width']); ?>" min="0">

            <p class="description">
                <?php esc_html_e('برای اینکه عرض خودکار باشد مقدار 0 را قرار دهید.', 'arvand-panel'); ?>
            </p>
        </div>

        <div class="wpap-field-wrap">
            <label for="wpap-panel-logo-height">
                <?php esc_html_e('ارتفاع لوگوی پنل (px)', 'arvand-panel'); ?>
            </label>

            <input id="wpap-panel-logo-height" type="number" name="panel_logo_height" value="<?php echo esc_attr($styles['panel_logo_height']); ?>" min="0">

            <p class="description">
                <?php esc_html_e('برای اینکه ارتفاع خودکار باشد مقدار 0 را قرار دهید.', 'arvand-panel'); ?>
            </p>
        </div>

        <div class="wpap-field-wrap">
            <label for="wpap-panel-logo-align">
                <?php esc_html_e('موقعیت لوگو پنل', 'arvand-panel'); ?>
            </label>

            <select id="wpap-panel-logo-align" name="panel_logo_align">
                <option value="right" <?php selected($styles['panel_logo_align'], 'right'); ?>>
                    <?php esc_html_e('راست', 'arvand-panel'); ?>
                </option>

                <option value="center" <?php selected($styles['panel_logo_align'], 'center'); ?>>
                    <?php esc_html_e('وسط', 'arvand-panel'); ?>
                </option>

                <option value="left" <?php selected($styles['panel_logo_align'], 'left'); ?>>
                    <?php esc_html_e('چپ', 'arvand-panel'); ?>
                </option>
            </select>

            <p class="description">
                <?php esc_html_e('محل نمایش لوگو را انتخاب کنید: چپ، وسط یا راست.', 'arvand-panel'); ?>
            </p>
        </div>

        <footer>
            <?php include WPAP_ADMIN_TEMPLATES_PATH . 'parts/button.php'; ?>
        </footer>
    </form>
</div>