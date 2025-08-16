<?php
defined('ABSPATH') || exit;

$opt = wpap_account_menu_options();
?>

<form class="wpap-form" method="post">
    <?php wp_nonce_field('account_menu_nonce', 'account_menu_nonce'); ?>
    <input type="hidden" name="form" value="wpap_account_menu"/>

    <div class="wpap-field-wrap">
        <label for="wpap-non-logged-in-btn">
            <?php esc_html_e('Non logged in user btn type', 'arvand-panel'); ?>
        </label>

        <select id="wpap-non-logged-in-btn" class="regular-text" name="non_logged_in_btn">
            <option value="signup_login" <?php selected($opt['non_logged_in_btn'] === 'signup_login'); ?>>
                <?php esc_html_e('Signup or Login', 'arvand-panel'); ?>
            </option>

            <option value="sms_signup_login" <?php selected($opt['non_logged_in_btn'] === 'sms_signup_login'); ?>>
                <?php esc_html_e('SMS signup or login', 'arvand-panel'); ?>
            </option>
        </select>
    </div>

    <div class="wpap-field-wrap">
        <label for="wpap-logged-in-btn">
            <?php esc_html_e('logged in user btn type', 'arvand-panel'); ?>
        </label>

        <select id="wpap-logged-in-btn" class="regular-text" name="logged_in_btn">
            <option value="avatar" <?php selected($opt['logged_in_btn'] === 'avatar'); ?>>
                <?php esc_html_e('Avatar', 'arvand-panel'); ?>
            </option>

            <option value="icon_text" <?php selected($opt['logged_in_btn'] === 'icon_text'); ?>>
                <?php esc_html_e('Icon and text', 'arvand-panel'); ?>
            </option>
        </select>
    </div>

    <div class="wpap-field-wrap">
        <label for="wpap-logged-in-btn-icon">
            <?php esc_html_e('Logged in user btn icon class', 'arvand-panel'); ?>
        </label>

        <input id="wpap-logged-in-btn-icon" type="text" name="logged_in_btn_icon"
               value="<?php echo esc_attr($opt['logged_in_btn_icon']); ?>"/>
    </div>

    <div class="wpap-field-wrap">
        <label for="wpap-logged-in-btn-text">
            <?php esc_html_e('Logged in user btn text', 'arvand-panel'); ?>
        </label>

        <input id="wpap-logged-in-btn-text" type="text" name="logged_in_btn_text"
               value="<?php echo esc_attr($opt['logged_in_btn_text']); ?>"/>
    </div>

    <div class="wpap-field-wrap">
        <label for="wpap-account-menus">
            <?php esc_html_e('Menus', 'arvand-panel'); ?>
        </label>

        <select id="wpap-account-menus" name="menus[]" multiple="multiple" size="15">
            <?php
            $menu_db = new \Arvand\ArvandPanel\DB\WPAPMenuDB();

            foreach ($menu_db->getAccountMenus() as $menu): ?>
                <option value="<?php echo esc_attr($menu->menu_name); ?>" <?php selected(in_array($menu->menu_name, $opt['menus'])); ?>>
                    <?php echo esc_html($menu->menu_title); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <footer>
        <?php require WPAP_ADMIN_TEMPLATES_PATH . 'parts/button.php'; ?>
    </footer>
</form>