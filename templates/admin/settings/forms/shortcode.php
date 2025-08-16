<?php
defined('ABSPATH') || exit;

$shortcode = wpap_shortcode_options();
?>

<form id="wpap-shortcode-section" class="wpap-settings-section wpap-form" method="post">
    <?php wp_nonce_field('shortcode_nonce', 'shortcode_nonce'); ?>
    <input type="hidden" name="form" value="wpap_shortcode"/>

    <div class="wpap-field-wrap">
        <label for="wpap-before-sidebar-nav">
            <?php esc_html_e('Add shortcode before sidebar menu', 'arvand-panel'); ?>
        </label>

        <div>
            <?php
            $args = [
                'wpautop' => true,
                'media_buttons' => false,
                'textarea_name' => 'before_sidebar_nav',
                'textarea_rows' => 8,
                'quicktags' => false,
                'tinymce' => [
                    'toolbar1' => 'bold,italic,underline,separator,alignleft,aligncenter,alignright,separator,link,unlink,undo,redo',
                    'toolbar2' => '',
                    'toolbar3' => '',
                ],
            ];

            wp_editor(wp_kses($shortcode['before_sidebar_nav'], 'post'), 'wpap-before-sidebar-nav', $args);
            ?>

            <p class="description">
                <?php esc_html_e('You can add text or shortcodes before sidebar menu.', 'arvand-panel'); ?>
            </p>
        </div>
    </div>

    <div class="wpap-field-wrap">
        <label for="wpap-after-sidebar-nav">
            <?php esc_html_e('Add shortcode after sidebar menu', 'arvand-panel'); ?>
        </label>

        <div>
            <?php
            $args = [
                'wpautop' => true,
                'media_buttons' => false,
                'textarea_name' => 'after_sidebar_nav',
                'textarea_rows' => 8,
                'quicktags' => false,
                'tinymce' => [
                    'toolbar1' => 'bold,italic,underline,separator,alignleft,aligncenter,alignright,separator,link,unlink,undo,redo',
                    'toolbar2' => '',
                    'toolbar3' => '',
                ],
            ];

            wp_editor(wp_kses($shortcode['after_sidebar_nav'], 'post'), 'wpap-after-sidebar-nav', $args);
            ?>

            <p class="description">
                <?php esc_html_e('You can add text or shortcodes after sidebar menu.', 'arvand-panel'); ?>
            </p>
        </div>
    </div>

    <div class="wpap-field-wrap">
        <label for="wpap-panel-header-shortcode">
            <?php esc_html_e('Add shortcode in panel header', 'arvand-panel'); ?>
        </label>

        <div>
            <?php
            $args = [
                'wpautop' => true,
                'media_buttons' => false,
                'textarea_name' => 'panel_header',
                'textarea_rows' => 8,
                'quicktags' => false,
                'tinymce' => [
                    'toolbar1' => 'bold,italic,underline,separator,alignleft,aligncenter,alignright,separator,link,unlink,undo,redo',
                    'toolbar2' => '',
                    'toolbar3' => '',
                ],
            ];

            wp_editor(wp_kses($shortcode['panel_header'], 'post'), 'wpap-panel-header-shortcode', $args);
            ?>

            <p class="description">
                <?php esc_html_e('You can add text or shortcodes in panel header.', 'arvand-panel'); ?>
            </p>
        </div>
    </div>

    <div class="wpap-field-wrap">
        <label for="wpap-after-logo-shortcode">
            <?php esc_html_e('Add shortcode after header logo', 'arvand-panel'); ?>
        </label>

        <div>
            <?php
            $args = [
                'wpautop' => true,
                'media_buttons' => false,
                'textarea_name' => 'after_logo',
                'textarea_rows' => 8,
                'quicktags' => false,
                'tinymce' => [
                    'toolbar1' => 'bold,italic,underline,separator,alignleft,aligncenter,alignright,separator,link,unlink,undo,redo',
                    'toolbar2' => '',
                    'toolbar3' => '',
                ],
            ];

            wp_editor(wp_kses($shortcode['after_logo'], 'post'), 'wpap-after-logo-shortcode', $args);
            ?>

            <p class="description">
                <?php esc_html_e('You can add text or shortcodes after header logo.', 'arvand-panel'); ?>
            </p>
        </div>
    </div>

    <div class="wpap-field-wrap">
        <label for="wpap-dash-top-shortcode">
            <?php esc_html_e('Add shortcode in top of dashboard', 'arvand-panel'); ?>
        </label>

        <div>
            <?php
            $args = [
                'wpautop' => true,
                'media_buttons' => false,
                'textarea_name' => 'dash_top_shortcode',
                'textarea_rows' => 8,
                'quicktags' => false,
                'tinymce' => [
                    'toolbar1' => 'bold,italic,underline,separator,alignleft,aligncenter,alignright,separator,link,unlink,undo,redo',
                    'toolbar2' => '',
                    'toolbar3' => '',
                ],
            ];

            wp_editor(esc_html($shortcode['dash_top_shortcode']), 'wpap-dash-top-shortcode', $args);
            ?>

            <p class="description">
                <?php esc_html_e('You can add text or shortcodes in top of dashboard', 'arvand-panel'); ?>
            </p>
        </div>
    </div>

    <div class="wpap-field-wrap">
        <label for="wpap-before-dash-info-boxes">
            <?php esc_html_e('Add shortcode before info boxes', 'arvand-panel'); ?>
        </label>

        <div>
            <?php
            $args = [
                'wpautop' => true,
                'media_buttons' => false,
                'textarea_name' => 'before_dash_info_boxes',
                'textarea_rows' => 8,
                'quicktags' => false,
                'tinymce' => [
                    'toolbar1' => 'bold,italic,underline,separator,alignleft,aligncenter,alignright,separator,link,unlink,undo,redo',
                    'toolbar2' => '',
                    'toolbar3' => '',
                ],
            ];

            wp_editor(wp_kses($shortcode['before_dash_info_boxes'], 'post'), 'wpap-before-dash-info-boxes', $args);
            ?>

            <p class="description">
                <?php esc_html_e('You can add text or shortcodes before dashboard info boxes.', 'arvand-panel'); ?>
            </p>
        </div>
    </div>

    <div class="wpap-field-wrap">
        <label for="wpap-after-dash-info-boxes">
            <?php esc_html_e('Add shortcode after info boxes', 'arvand-panel'); ?>
        </label>

        <div>
            <?php
            $args = [
                'wpautop' => true,
                'media_buttons' => false,
                'textarea_name' => 'after_dash_info_boxes',
                'textarea_rows' => 8,
                'quicktags' => false,
                'tinymce' => [
                    'toolbar1' => 'bold,italic,underline,separator,alignleft,aligncenter,alignright,separator,link,unlink,undo,redo',
                    'toolbar2' => '',
                    'toolbar3' => '',
                ],
            ];

            wp_editor(wp_kses($shortcode['after_dash_info_boxes'], 'post'), 'wpap-after-dash-info-boxes', $args);
            ?>

            <p class="description">
                <?php esc_html_e('You can add text or shortcodes after dashboard info boxes.', 'arvand-panel'); ?>
            </p>
        </div>
    </div>

    <div class="wpap-field-wrap">
        <label for="wpap-dash-bottom-shortcode">
            <?php esc_html_e('Add shortcode in bottom of dashboard', 'arvand-panel'); ?>
        </label>

        <div>
            <?php
            $args = [
                'wpautop' => true,
                'media_buttons' => false,
                'textarea_name' => 'dash_bottom_shortcode',
                'textarea_rows' => 8,
                'quicktags' => false,
                'tinymce' => [
                    'toolbar1' => 'bold,italic,underline,separator,alignleft,aligncenter,alignright,separator,link,unlink,undo,redo',
                    'toolbar2' => '',
                    'toolbar3' => '',
                ],
            ];

            wp_editor(esc_html($shortcode['dash_bottom_shortcode']), 'wpap-dash-bottom-shortcode', $args);
            ?>

            <p class="description">
                <?php esc_html_e('You can add text or shortcodes in bottom of dashboard', 'arvand-panel'); ?>
            </p>
        </div>
    </div>

    <footer>
        <?php require WPAP_ADMIN_TEMPLATES_PATH . 'parts/button.php'; ?>
    </footer>
</form>