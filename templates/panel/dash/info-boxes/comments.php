<?php
defined('ABSPATH') || exit;

if (!wpap_is_valid_section('comments')) {
    return;
}

global $current_user;
?>

<a href="<?php echo esc_url(wpap_get_page_url_by_name('comments')); ?>">
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

        <span>
            <?php
            $comments = get_comments(['user_id' => $current_user->ID, 'fields' => 'ids']);
            $comments_count = count($comments);
            if ($comments_count) {
                echo sprintf(esc_html__('%d comments.', 'arvand-panel'), $comments_count);
            } else {
                esc_html_e('There is no comment.', 'arvand-panel');
            }
            ?>
        </span>
    </div>
</a>