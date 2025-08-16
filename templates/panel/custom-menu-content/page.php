<?php defined('ABSPATH') || exit; ?>

<div id="wpap-post">
    <?php if (!empty($post->post_title)): ?>
        <h2 id="wpap-post-title">
            <?php echo get_the_title($post->ID); ?>
        </h2>
    <?php endif; ?>

    <div class="wpap-post-content">
        <?php echo do_shortcode(get_the_content('', '', $post->ID)); ?>
    </div>
</div>
