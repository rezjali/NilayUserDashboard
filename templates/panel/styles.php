<?php
defined('ABSPATH') || exit;

$styles = wpap_styles();
$colors = wpap_colors();
$panel_opt = wpap_panel_options();
$pages_opt = wpap_pages_options();

require_once WPAP_TEMPLATES_PATH . 'general-styles.php';
?>

<style>
    <?php
    if ($panel_opt['fullscreen_compatibility'] && wpap_is_panel_page()) {
        echo 'body { overflow-y: hidden !important }';
    }
    ?>

    #wpap-user-panel[data-theme="light"] {
        --wpap-color-1: <?php echo esc_html($colors['panel_color_1']); ?>;
        --wpap-color-2: <?php echo esc_html($colors['panel_color_2']); ?>;
        --wpap-bg-color-1: <?php echo esc_html($colors['panel_bg_color_1']); ?>;
        --wpap-bg-color-2: <?php echo esc_html($colors['panel_bg_color_2']); ?>;
        --wpap-text-color-1: <?php echo esc_html($colors['panel_text_color_1']); ?>;
        --wpap-text-color-2: <?php echo esc_html($colors['panel_text_color_2']); ?>;
        --wpap-text-color-3: <?php echo esc_html($colors['panel_text_color_3']); ?>;
        --wpap-border-color-1: <?php echo esc_html($colors['panel_border_color_1']); ?>;
        --wpap-border-color-2: <?php echo esc_html($colors['panel_border_color_2']); ?>;
    }

    #wpap-user-panel[data-theme="dark"] {
        --wpap-color-1: <?php echo esc_html($colors['panel_dark_color_1']); ?>;
        --wpap-color-2: <?php echo esc_html($colors['panel_dark_color_2']); ?>;
        --wpap-bg-color-1: <?php echo esc_html($colors['panel_dark_bg_color_1']); ?>;
        --wpap-bg-color-2: <?php echo esc_html($colors['panel_dark_bg_color_2']); ?>;
        --wpap-text-color-1: <?php echo esc_html($colors['panel_dark_text_color_1']); ?>;
        --wpap-text-color-2: <?php echo esc_html($colors['panel_dark_text_color_2']); ?>;
        --wpap-text-color-3: <?php echo esc_html($colors['panel_dark_text_color_3']); ?>;
        --wpap-border-color-1: <?php echo esc_html($colors['panel_dark_border_color_1']); ?>;
        --wpap-border-color-2: <?php echo esc_html($colors['panel_dark_border_color_2']); ?>;
    }

    #wpap-user-panel {
        <?php if ($panel_opt['fullscreen_compatibility']): ?>
            position: fixed;
            top: <?php echo is_admin_bar_showing() ? '32px' : '0'; ?>;
            right: 0;
            left: 0;
            bottom: 0;
            z-index: <?php echo esc_html((int) $styles['panel_z_index']); ?>;
        <?php else: ?>
            min-height: 800px;
        <?php endif; ?>
    }

    @media screen and (max-width: 782px) {
        #wpap-user-panel {
            top: <?php echo is_admin_bar_showing() ? '46px' : '0'; ?>;
        }
    }
</style>