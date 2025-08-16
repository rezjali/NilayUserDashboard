<?php defined('ABSPATH') || exit; ?>

<div class="wpap-content-shortcode">
    <?php echo wp_kses_post(do_shortcode(wp_unslash($shortcode))); ?>
</div>