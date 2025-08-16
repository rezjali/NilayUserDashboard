<?php
defined('ABSPATH') || exit;

use Arvand\ArvandPanel\Form\WPAPFieldSettings;
use Arvand\ArvandPanel\WPAPFile;

add_filter('plugin_action_links_' . plugin_basename(WPAP_DIR_PATH . 'arvand-panel.php'), function ($links) {
    $links[] = '<a href="admin.php?page=arvand-panel">' . __('Settings') . '</a>';
    return $links;
});

add_filter('display_post_states', function ($post_states, $post) {
    $opt_pages = wpap_pages_options();

    $ids = [
        $opt_pages['register_page_id'] => __('ثبت نام-اروند پنل', 'arvand-panel'),
        $opt_pages['login_page_id'] => __('ورود با ایمیل/نام کاربری-اروند پنل', 'arvand-panel'),
        $opt_pages['sms_register_login_page_id'] => __('ثبت نام/ورود با شماره همراه-اروند پنل', 'arvand-panel'),
        $opt_pages['lost_pass_page_id'] => __('فراموشی رمز عبور-اروند پنل', 'arvand-panel'),
        $opt_pages['reset_pass_page_id'] => __('ثبت رمز عبور جدید-اروند پنل', 'arvand-panel'),
        $opt_pages['panel_page_id'] => __('پنل کاربری-اروند پنل', 'arvand-panel')
    ];

    if (isset($ids[$post->ID])) {
        $post_states["wpap_page_$post->ID"] = $ids[$post->ID];
    }

    return $post_states;
}, 10, 2);

add_action('admin_notices', function () {
    if (wpap_pages_options()['panel_page_id'] == -1) {
        $text = esc_html__('Please select the arvand panel "user panel" page.', 'arvand-panel');

        $text .= sprintf(
            '<a href="%s">%s</a>',
            admin_url('?page=arvand-panel&section=pages'),
            esc_html__('Select the user panel page.', 'arvand-panel')
        );
        ?>
        <div class="notice notice-warning is-dismissible">
            <p><?php echo $text; ?></p>
        </div>
        <?php
    }
});