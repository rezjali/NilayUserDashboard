<?php
defined('ABSPATH') || exit;

/**
 * @var WP_Post $notice
 */
?>

<div id="wpap-notice-page" class="wpap-page">
    <header>
        <div>
            <h2><?php echo esc_html($notice->post_title); ?></h2>

            <span>
                <i class="ri-user-6-line"></i>
                <?php echo esc_html(get_the_author_meta('display_name', $notice->post_author)); ?>
            </span>
        </div>

        <time><?php echo esc_html(get_the_date('', $notice)); ?></time>
    </header>

    <div class="wpap-post-content">
        <?php echo wp_kses_post($notice->post_content)?>
    </div>
</div>