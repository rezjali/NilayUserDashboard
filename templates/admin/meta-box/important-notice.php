<?php
defined('ABSPATH') || exit;

wp_nonce_field('important_notice_nonce', 'important_notice_nonce');
?>

<p>
    <span class="dashicons dashicons-info-outline"></span>

    <span>
        <?php esc_html_e('Important notice can be displayed in dashboard or all panel pages.', 'arvand-panel'); ?>
    </span>
</p>

<p>
    <label>
        <?php $important_notice = get_post_meta($post->ID, 'wpap_important_notice', true); ?>
        <input type="checkbox" name="wpap_important_notice" <?php checked($important_notice > 0); ?>/>
        <span><?php esc_html_e('Important notice?', 'arvand-panel'); ?></span>
    </label>
</p>

<p>
    <label for="wpap-important-notice-display-plcae" style="display: block">
        <?php esc_html_e('Where to show it?', 'arvand-panel'); ?>
    </label>

    <select style="width: 100%; margin-top: 5px" id="wpap-important-notice-display-plcae" name="important_notice_display_place">
        <?php $place = get_post_meta($post->ID, 'wpap_important_notice_place', 1); ?>

        <option value="all" <?php selected($place === 'all'); ?>>
            <?php esc_html_e('All panel pages', 'arvand-panel'); ?>
        </option>

        <?php
        $menu = new \Arvand\ArvandPanel\DB\WPAPMenuDB();

        foreach ($menu->getAccountMenus() as $menu) {
            printf(
                '<option value="%s" %s>%s</option>',
                esc_attr($menu->menu_name),
                selected($place === $menu->menu_name),
                esc_html($menu->menu_title)
            );
        }
        ?>
    </select>
</p>

<p>
    <label for="wpap-important-notice-type" style="display: block">
        <?php esc_html_e('Notice type', 'arvand-panel'); ?>
    </label>

    <select style="width: 100%; margin-top: 5px" id="wpap-important-notice-type" name="important_notice_type">
        <?php $place = get_post_meta($post->ID, 'wpap_important_notice_type', true); ?>

        <option value="info" <?php selected($place === 'info'); ?>>
            <?php esc_html_e('Info', 'arvand-panel'); ?>
        </option>

        <option value="error" <?php selected($place === 'error'); ?>>
            <?php esc_html_e('Error', 'arvand-panel'); ?>
        </option>

        <option value="success" <?php selected($place === 'success'); ?>>
            <?php esc_html_e('Success', 'arvand-panel'); ?>
        </option>

        <option value="warning" <?php selected($place === 'warning'); ?>>
            <?php esc_html_e('Warning', 'arvand-panel'); ?>
        </option>
    </select>
</p>

<p>
    <label for="wpap-notice-recipient-type" style="display: block">
        <?php esc_html_e('برای چه کسانی نمایش داده شود:', 'arvand-panel'); ?>
    </label>

    <select style="width: 100%; margin-top: 5px" id="wpap-notice-recipient-type" name="notice_recipient_type">
        <?php $recipient_type = get_post_meta($post->ID, 'wpap_notice_recipient_type', 1); ?>

        <option value="all" <?php selected($recipient_type === 'all'); ?>>
            <?php esc_html_e('همه کاربران', 'arvand-panel'); ?>
        </option>

        <option value="roles" <?php selected($recipient_type === 'roles'); ?>>
            <?php esc_html_e('نقش(های) کاربری خاص', 'arvand-panel'); ?>
        </option>

        <option value="user" <?php selected($recipient_type === 'user'); ?>>
            <?php esc_html_e('کاربر خاص', 'arvand-panel'); ?>
        </option>
    </select>
</p>

<p style="<?php echo 'roles' !== $recipient_type ? 'display: none;' : ''; ?>" id="wpap-notice-roles" class="wpap-recipient-input">
    <label for="wpap-notice-roles-select" style="display: block">
        <?php esc_html_e('نقش های کاربری', 'arvand-panel'); ?>
    </label>

    <select style="width: 100%; height: 200px; margin-top: 5px;" id="wpap-notice-roles-select" name="important_notice_roles[]" multiple>
        <?php
        $roles = get_post_meta($post->ID, 'wpap_important_notice_roles', 1);
        $role_names = wp_roles()->get_names();

        foreach ($role_names as $key => $name) {
            printf(
                '<option value="%s" %s>%s</option>',
                esc_attr($key),
                selected(in_array($key, (array)$roles), true, false),
                esc_html($name)
            );
        }
        ?>
    </select>

    <small style="margin-top: 5px; display: block;">
         <?php esc_html_e('برای انتخاب بیش از یک مورد کلید ctrl کیبورد را نگه دارید و کلیک کنید.', 'arvand-panel'); ?>
    </small>
</p>

<p style="<?php echo 'user' !== $recipient_type ? 'display: none;' : ''; ?>" id="wpap-notice-user" class="wpap-recipient-input">
    <label for="wpap-notice-user-input" style="display: block">
        <?php esc_html_e('شناسه/نام کاربری/ایمیل/شماره همراه کاربر', 'arvand-panel'); ?>
    </label>

    <input
        style="width: 100%; margin-top: 5px;"
        id="wpap-notice-user-input" type="text"
        name="important_notice_user"
        value="<?php echo esc_attr(get_post_meta($post->ID, 'wpap_important_notice_user', 1)); ?>"
    />

    <?php
    $user_id = get_post_meta($post->ID, 'wpap_important_notice_user_id', 1);

    if ($user_id) {
        $user = get_user_by('id', absint($user_id));

        if ($user) {
            printf(
                '<span style="margin-top: 5px; display: block;"><strong>%s</strong>%s</span>',
                esc_html__('کاربر: ', 'arvand-panel'),
                esc_html($user->display_name)
            );
        } else {
            printf(
                '<span style="margin-top: 5px; display: block; color: red;">%s</span>',
                esc_html__('همچین کاربری وجود ندارد.', 'arvand-panel')
            );
        }
    }
    ?>
</p>

<script>
    jQuery(document).ready(function ($) {
        $('#wpap-notice-recipient-type').on('change', function () {
            $('.wpap-recipient-input').hide();
            $('#wpap-notice-' + $(this).val()).fadeIn(200);
        });
    });
</script>