<?php
defined('ABSPATH') || exit;

global $current_user;
$panel_page = absint($_GET['panel-page'] ?? 1);
$panel_opt = wpap_panel_options();
$limit = absint($panel_opt['comments_per_page']);
$offset = ($panel_page * $limit) - $limit;

$comments = get_comments([
    'user_id' => $current_user->ID,
    'number' => $limit,
    'offset' => $offset,
]);

$total_comments = ceil(count(get_comments(['user_id' => $current_user->ID])) / $limit);
?>

<div id="wpap-comments">
    <div class="wpap-table-wrap">
        <table>
            <thead>
                <tr>
                    <th><?php esc_html_e('دیدگاه', 'arvand-panel'); ?></th>
                    <th><?php esc_html_e('صفحه دیدگاه', 'arvand-panel'); ?></th>
                    <th><?php esc_html_e('وضعیت', 'arvand-panel'); ?></th>
                    <th><?php esc_html_e('زمان ارسال', 'arvand-panel'); ?></th>
                    <th></th>
                </tr>
            </thead>

            <tbody>
                <?php if (count($comments) > 0): ?>
                    <?php foreach ($comments as $comment): ?>
                        <tr>
                            <td><?php echo wp_trim_words(esc_html($comment->comment_content), 16); ?></td>

                            <td class="wpap-table-row-title">
                                <?php
                                $post_link = get_preview_post_link($comment->comment_post_ID);
                                if ($post_link) {
                                    printf('<a href="%s" target="_blank">%s</a>',
                                        esc_url($post_link),
                                        esc_html(wp_trim_words(get_the_title($comment->comment_post_ID), 12) ?? __('مشاهدۀ مطلب', 'arvand-panel'))
                                    );
                                } else {
                                    echo '---';
                                }
                                ?>
                            </td>

                            <td>
                                <?php
                                if ($comment->comment_approved) {
                                    echo '<span class="wpap-badge-success">' . esc_html__('Approved', 'arvand-panel') . '</span>';
                                } else {
                                    echo '<span class="wpap-badge-warning">' . esc_html__('Unapproved', 'arvand-panel') . '</span>';
                                }
                                ?>
                            </td>

                            <td>
                                <?php
                                printf(
                                    esc_html__('%s ساعت %s', 'arvand-panel'),
                                    get_comment_date('', $comment->comment_ID),
                                    get_comment_time('', false, true, $comment->comment_ID)
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                printf('<a class="wpap-btn-1" href="%s" target="_blank">%s</a>',
                                    esc_url($post_link),
                                    esc_html__('مشاهده', 'arvand-panel')
                                );
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">
                            <?php wpap_print_notice(__('There is no comment.', 'arvand-panel'), 'info', false); ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php wpap_pagination($total_comments); ?>
</div>