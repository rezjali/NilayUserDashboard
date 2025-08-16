<?php defined('ABSPATH') || exit; ?>

<?php if (!empty($box['link'])) printf('<a href="%s">', esc_url($box['link'])); ?>
    <div class="wpap-dash-info-box">
        <?php
        printf(
            '<i style="background-color: %s; color: %s;" class="%s"></i>',
            esc_attr($box['icon_bg']),
            esc_attr($box['icon_color']),
            esc_attr($box['icon'])
        );
        ?>

        <h3><?php esc_html_e($box['title']); ?></h3>

        <?php if ($box['content_type'] === 'text'): ?>
            <span id="wpap-<?php echo esc_attr($box['name']); ?>">
                <?php esc_html_e($box['content']); ?>
            </span>
        <?php elseif ($box['content_type'] === 'shortcode'): ?>
            <div>
                <?php echo wp_kses_post(do_shortcode($box['content'])); ?>
            </div>
        <?php endif; ?>
    </div>
<?php if (!empty($box['link'])) echo '</a>'; ?>