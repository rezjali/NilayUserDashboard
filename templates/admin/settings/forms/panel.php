<?php
defined('ABSPATH') || exit;

$panel = wpap_panel_options();
?>

<form id="wpap-panel-section" class="wpap-settings-section wpap-form" method="post">
    <?php wp_nonce_field('panel_nonce', 'panel_nonce'); ?>
    <input type="hidden" name="form" value="wpap_panel"/>

    <div class="wpap-field-wrap">
        <label for="wpap-enable-fullscreen-compatibility">
            <?php esc_html_e('Fullscreen compatibility', 'arvand-panel'); ?>
        </label>

        <div>
            <span class="wpap-checkbox-wrap">
                <label>
                    <input id="wpap-enable-fullscreen-compatibility" name="fullscreen_compatibility"
                           type="checkbox" <?php checked($panel['fullscreen_compatibility']); ?>/>
                    <span class="wpap-checkbox"></span>
                </label>
            </span>

            <p class="description">
                <?php esc_html_e('Enabling this option does not make the screen full screen but compatible with full screen mode. For example, the height of the panel is adjusted more precisely.', 'arvand-panel'); ?>
            </p>
        </div>
    </div>

    <div class="wpap-field-wrap">
        <label for="wpap-display-top-sidebar">
            <?php esc_html_e('Show top panel section', 'arvand-panel'); ?>
        </label>

        <span class="wpap-checkbox-wrap">
            <label>
                <input id="wpap-display-top-sidebar" name="display_top_sidebar"
                       type="checkbox" <?php checked($panel['display_top_sidebar']); ?>/>
                <span class="wpap-checkbox"></span>
            </label>
        </span>
    </div>

    <div class="wpap-field-wrap">
        <label id="wpap-logo-label"><?php esc_html_e('Panel header logo', 'arvand-panel'); ?></label>

        <div id="wpap-panel-logo">
            <input id="wpap-logo" type="hidden" name="logo_url" value="<?php echo esc_attr($panel['logo_url']); ?>"/>

            <?php if (!empty($panel['logo_url'])): ?>
                <img id="wpap-logo-preview" src="<?php echo esc_attr($panel['logo_url']); ?>" height="100"/>
            <?php endif; ?>

            <button id="wpap-upload-logo-btn" class="wpap-btn-2" type="button">
                <i class="ri-upload-line"></i>
                <?php esc_html_e('Upload', 'arvand-panel'); ?>
            </button>

            <button id="wpap-delete-logo-btn"
                    class="wpap-btn-2" <?php echo empty($panel['logo_url']) ? 'style="display:none"' : ''; ?>
                    type="button">
                <i class="ri-delete-bin-7-line"></i>
                <?php esc_html_e('Delete', 'arvand-panel'); ?>
            </button>
        </div>
    </div>

    <div class="wpap-field-wrap">
        <label for="wpap-enable-upload-avatar"><?php esc_html_e('Enable upload avatar', 'arvand-panel'); ?></label>

        <span class="wpap-checkbox-wrap">
            <label>
                <input id="wpap-enable-upload-avatar" name="enable_upload_avatar"
                       type="checkbox" <?php checked($panel['upload_avatar']); ?>/>
                <span class="wpap-checkbox"></span>
            </label>
        </span>
    </div>

    <div class="wpap-field-wrap">
        <label for="wpap-avatar-size"><?php esc_html_e('Avatar image size in kilobytes', 'arvand-panel'); ?></label>
        <input id="wpap-avatar-size" class="small-text" type="number" name="avatar_size"
               value="<?php echo esc_attr($panel['avatar_size']); ?>" min="100"/>
    </div>

    <div class="wpap-field-wrap">
        <label for="wpap-notice-per-page"><?php esc_html_e('Notifications per page', 'arvand-panel'); ?></label>
        <input id="wpap-notice-per-page" class="small-text" type="number" name="notifications_per_page" step="1" min="1"
               value="<?php echo esc_attr($panel['notifications_per_page']); ?>"/>
    </div>

    <div class="wpap-field-wrap">
        <label for="wpap-comments-per-page"><?php esc_html_e('Comments per page', 'arvand-panel'); ?></label>
        <input id="wpap-comments-per-page" class="small-text" type="number" name="comments_per_page" min="1"
               value="<?php echo esc_attr($panel['comments_per_page']); ?>"/>
    </div>

    <footer>
        <?php require WPAP_ADMIN_TEMPLATES_PATH . 'parts/button.php'; ?>
    </footer>
</form>