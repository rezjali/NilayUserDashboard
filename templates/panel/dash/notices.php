<?php
defined( 'ABSPATH' ) || exit;

if (!in_array('notices', $dash['dash_widgets'])) {
    return;
}

global $current_user;
$user_notice_ids = \Arvand\ArvandPanel\WPAPNotification::getUserNoticeIDs($current_user);

$notices = get_posts([
    'post_type' => 'wpap_notifications',
    'post__in' => empty($user_notice_ids) ? [0] : $user_notice_ids,
    'numberposts' => 5,
]);
?>

<div id="wpap-latest-notices" class="wpap-list">
    <header>
        <h2><?php esc_html_e("اعلانات اخیر", 'arvand-panel'); ?></h2>

        <a class="wpap-list-show-all" href="<?php echo esc_url(wpap_get_page_url_by_name('notifications')); ?>">
            <?php esc_html_e('مشاهده همه', 'arvand-panel'); ?>
        </a>
    </header>

    <div class="wpap-list-wrap">
        <?php if (!empty($notices)): ?>
            <?php foreach ($notices as $notice): ?>
                <a class="wpap-list-item"
                   href="<?php echo esc_url(wpap_panel_url('notifications/' . $notice->ID)); ?>">
                    <?php
                    $avatar = get_avatar(
                        $notice->post_author,
                        '40',
                        '',
                        get_the_author_meta('display_name', $notice->post_author)
                    );

                    printf('<div class="wpap-latest-notices-avatar">%s</div>', $avatar);
                    ?>

                    <div class="wpap-list-item-title">
                        <?php
                        printf(
                            '<h2>%s</h2>',
                            esc_html(
                                wp_trim_words(wp_strip_all_tags($notice->post_title ?: $notice->post_content), 10)
                            )
                        );

                        printf(
                            '<span class="wpap-list-item-subtitle">%s</span>',
                            esc_html(get_the_author_meta('display_name', $notice->post_author))
                        );
                        ?>
                    </div>

                    <time>
                        <?php
                        echo human_time_diff(
                            get_post_modified_time('U', '', $notice->ID),
                            current_time('timestamp')
                        );

                        esc_html_e(' قبل', 'arvand-panel');
                        ?>
                    </time>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="wpap-list-notfound wpap-list-item">
                <?php esc_html_e('اعلانی وجود ندارد.', 'arvand-panel'); ?>
            </div>
        <?php endif; ?>
    </div>
</div>
