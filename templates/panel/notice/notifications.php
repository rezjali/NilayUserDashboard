<?php
defined('ABSPATH') || exit;

global $current_user;

if (!wpap_is_demo() && isset($_POST['wpap_seen_notices'])) {
    \Arvand\ArvandPanel\WPAPNotification::setSeenDate($current_user->ID);
}

$panel_page = absint($_GET['panel-page'] ?? 1);
$user_notice_ids = \Arvand\ArvandPanel\WPAPNotification::getUserNoticeIDs($current_user);
$panel_opt = wpap_panel_options();
$limit = absint($panel_opt['notifications_per_page']);
$offset = ($panel_page * $limit) - $limit;

$query = new WP_Query([
    'post_type' => 'wpap_notifications',
    'post__in' => empty($user_notice_ids) ? [0] : $user_notice_ids,
    'posts_per_page' => $limit,
    'paged' => $panel_page,
    'offset' => $offset,
]);
?>

<div id="wpap-notices">
    <form class="wpap-mb-30" method="post">
        <button class="wpap-btn-1" name="wpap_seen_notices">
            <?php esc_html_e('تغییر همه به خوانده شده', 'arvand-panel'); ?>
        </button>
    </form>

    <div class="wpap-table-wrap">
        <table>
            <thead>
                <tr>
                    <th><?php esc_html_e('فرستنده', 'arvand-panel'); ?></th>
                    <th><?php esc_html_e('عنوان', 'arvand-panel'); ?></th>
                    <th><?php esc_html_e('تاریخ', 'arvand-panel'); ?></th>
                    <th></th>
                </tr>
            </thead>

            <tbody>
                <?php if ($query->have_posts()): ?>
                    <?php $seen_date = \Arvand\ArvandPanel\WPAPNotification::getSeenDate($current_user->ID); ?>

                    <?php while ($query->have_posts()): ?>
                        <?php $query->the_post(); ?>

                        <tr class="<?php echo !$seen_date || get_post_timestamp() > $seen_date ? 'wpap-notice-unseen' : ''?>">
                            <td class="wpap-table-row-sender">
                                <div>
                                    <div class="wpap-table-row-avatar">
                                        <?php
                                        echo get_avatar(
                                            $query->post->post_author,
                                            '',
                                            '',
                                            get_the_author_meta('display_name')
                                        );
                                        ?>
                                    </div>

                                    <span class="wpap-sender-name">
                                        <?php echo esc_html(get_the_author_meta('display_name')); ?>
                                    </span>
                                </div>
                            </td>

                            <td class="wpap-table-row-title">
                                <a href="<?php echo esc_url(wpap_panel_url('notifications/' . get_the_ID())); ?>">
                                    <h2>
                                        <?php
                                        echo wp_trim_words(
                                            esc_html(wp_strip_all_tags(get_the_title() ?: get_the_content())),
                                            12
                                        );
                                        ?>
                                    </h2>
                                </a>
                            </td>

                            <td>
                                <?php
                                echo human_time_diff(
                                    get_post_modified_time('U', '', get_the_ID()),
                                    current_time('timestamp')
                                );

                                esc_html_e(' قبل', 'arvand-panel');
                                ?>
                            </td>

                            <td class="wpap-table-row-actions">
                                <div>
                                    <a class="wpap-btn-1" href="<?php echo esc_url(wpap_panel_url('notifications/' . get_the_ID())); ?>">
                                        <?php esc_html_e('نمایش', 'arvand-panel'); ?>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">
                            <?php esc_html_e('اعلانی وجود ندارد.', 'arvand-panel'); ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php wpap_pagination($query->max_num_pages); ?>
</div>