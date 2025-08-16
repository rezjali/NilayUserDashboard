<?php $colors = wpap_colors(); ?>

<style id="wpap-colors">
    :root {
        /* Miscellaneous colors */
        --wpap-color-1: #f2f8ff;
        --wpap-color-2: #0062f5;
        --wpap-bg-color-1: #ffffff;
        --wpap-bg-color-2: #f7f7f7;
        --wpap-text-color-1: #303030;
        --wpap-text-color-2: #707070;
        --wpap-text-color-3: #ffffff;
        --wpap-border-color-1: #e6e6e6;
        --wpap-border-color-2: #ffffff;
        --wpap-error-color: #ff006c;
        --wpap-warning-color: #f6743e;
        --wpap-success-color: #4eceaa;
        --wpap-color-white: #ffffff;
        --wpap-color-dark: #2b2b2b;
        --wpap-color-blue: #0062f5;
        --wpap-color-orange: #f6743e;
        --wpap-color-green: #3ba286;
        --wpap-color-red: #f10069;
        --wpap-color-yellow: #efca00;
    }

    .wpap-auth {
        --wpap-color-1: <?php echo esc_html($colors['auth_color_1']); ?>;
        --wpap-color-2: <?php echo esc_html($colors['auth_color_2']); ?>;
        --wpap-bg-color-1: <?php echo esc_html($colors['auth_bg_color_1']); ?>;
        --wpap-bg-color-2: <?php echo esc_html($colors['auth_bg_color_2']); ?>;
        --wpap-text-color-1: <?php echo esc_html($colors['auth_text_color_1']); ?>;
        --wpap-text-color-2: <?php echo esc_html($colors['auth_text_color_2']); ?>;
        --wpap-text-color-3: <?php echo esc_html($colors['auth_text_color_3']); ?>;
        --wpap-border-color-1: <?php echo esc_html($colors['auth_border_color_1']); ?>;
        --wpap-border-color-2: <?php echo esc_html($colors['auth_border_color_2']); ?>;
    }
</style>