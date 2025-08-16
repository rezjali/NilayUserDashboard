<?php
defined('ABSPATH') || exit;

$general = wpap_general_options();
?>

<form id="wpap-general-section" class="wpap-settings-section wpap-form" method="post">
    <?php wp_nonce_field('general_nonce', 'general_nonce'); ?>
    <input type="hidden" name="form" value="wpap_general"/>

    <div class="wpap-field-wrap">
        <label for="wpap-admin-bar-access">
            <?php esc_html_e('Disable admin bar for', 'arvand-panel'); ?>
        </label>

        <select id="wpap-admin-bar-access" name="admin_bar_access[]" multiple>
            <?php $roles = get_editable_roles(); ?>

            <?php foreach ($roles as $role => $details): ?>
                <option value="<?php esc_attr_e($role); ?>" <?php selected(in_array($role, $general['admin_bar_access'])); ?>>
                    <?php echo esc_html($details['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="wpap-field-wrap">
        <label for="wpap-admin-area-access">
            <?php esc_html_e('Disable wp admin for', 'arvand-panel'); ?>
        </label>

        <select id="wpap-admin-area-access" name="admin_area_access[]" multiple>
            <?php foreach ($roles as $role => $details): ?>
                <option value="<?php esc_attr_e($role); ?>" <?php selected(in_array($role, $general['admin_area_access'])); ?>>
                    <?php echo esc_html($details['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="wpap-field-wrap">
        <label for="wpap-msg-attachment-size">
            <?php esc_html_e('Maximum private message attachment size in kilobytes', 'arvand-panel'); ?>
        </label>

        <input id="wpap-msg-attachment-size" type="number" name="private_msg_attachment_size"
               value="<?php echo esc_attr($general['private_msg_attachment_size']); ?>" min="100"/>
    </div>

    <div class="wpap-field-wrap">
        <label>
            <input id="wpap-delete-plugin-data" type="checkbox" name="add_to_list" value="1" <?php checked($general['add_to_list']); ?>/>
            <?php esc_html_e('فعالسازی امکان "افزودن محصولات به لیست علاقه مندی"', 'arvand-panel'); ?>
        </label>

        <p class="description">
            <?php esc_html_e('این امکان ویژه محصولات افزونه ووکامرس می باشد. با فعالسازی این گزینه، امکان افزودن محصولات به لیست علاقه مندی بوجود می آید و این لیست در پنل کاربری و منوی "لیست علاقه مندی" قابل مشاهده خواهد بود.', 'arvand-panel'); ?>
        </p>
    </div>

    <div class="wpap-field-wrap">
        <label>
            <input id="wpap-delete-plugin-data" type="checkbox" name="add_to_list_btn_display" value="1" <?php checked($general['add_to_list_btn_display']); ?>/>
            <?php esc_html_e('نمایش دکمه "افزودن به لیست علاقه مندی"', 'arvand-panel'); ?>
        </label>

        <p class="description">
            <?php esc_html_e(' با فعالسازی این گزینه، دکمه افزودن به لیست علاقه مندی در صفحه جزئیات محصول نمایش داده خواهد شد. همچنین میتوانید به جای فعالسازی این گزینه، از کد کوتاه [wpap_bookmark_btn] در قسمت دلخواه صفحه جزئیات محصول استفاده کنید. برای استفاده از این دکمه، گزینه بالا (فعالسازی امکان "افزودن محصولات به لیست علاقه مندی") باید فعال باشد.', 'arvand-panel'); ?>
        </p>
    </div>

    <div class="wpap-field-wrap">
        <label>
            <input id="wpap-delete-plugin-data" type="checkbox" name="delete_plugin_data" value="1" <?php checked($general['delete_plugin_data']); ?>/>
            <?php esc_html_e('Delete plugin data during uninstallation', 'arvand-panel'); ?>
        </label>
    </div>

    <footer>
        <?php require WPAP_ADMIN_TEMPLATES_PATH . 'parts/button.php'; ?>
    </footer>
</form>