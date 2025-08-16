<?php defined('ABSPATH') || exit; ?>

<?php
printf(
    '<div class="wpap-content-text wpap-post-content">%s</div>',
    do_shortcode(wpautop($text))
);
?>
