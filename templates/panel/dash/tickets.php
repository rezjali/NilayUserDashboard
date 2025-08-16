<?php
defined('ABSPATH') || exit;

if (!in_array('tickets', $dash['dash_widgets'])) {
    return;
}

global $current_user;
$ticket_opt = wpap_ticket_options();
$ticket_status = $ticket_opt['ticket_status'];
$user_department = \Arvand\ArvandPanel\WPAPTicket::userDepartment($current_user->ID);

$user_query = [
    'relation' => 'OR',
    ['key' => 'wpap_ticket_recipient', 'value' => $current_user->ID],
    ['key' => 'wpap_ticket_creator', 'value' => $current_user->ID],
];

if (!empty($user_department)) {
    $user_query[] = [
        'key' => 'wpap_ticket_department',
        'value' => $user_department,
        'compare' => 'IN'
    ];
}

$posts = get_posts([
    'post_type' => 'wpap_ticket',
    'orderby' => 'post_modified',
    'post_parent' => 0,
    'meta_query' => $user_query
]);
?>

<div id="wpap-latest-tickets" class="wpap-list">
    <header>
        <h2><?php esc_html_e("تیکت\xE2\x80\x8Cهای اخیر", 'arvand-panel'); ?></h2>

        <a class="wpap-list-show-all" href="<?php echo esc_url(wpap_get_page_url_by_name('tickets')); ?>">
            <?php esc_html_e('مشاهده همه', 'arvand-panel'); ?>
        </a>
    </header>

    <div class="wpap-list-wrap">
        <?php if (!empty($posts)): ?>
            <?php foreach ($posts as $post): ?>
                <?php $status = get_post_meta($post->ID, 'wpap_ticket_status', true); ?>

                <a class="wpap-list-item" href="<?php echo esc_url(wpap_panel_url('tickets/' . ($post->post_parent ?: $post->ID))); ?>">
                    <?php
                    $avatar = get_avatar(
                        $post->post_author,
                        '36',
                        '',
                        get_the_author_meta('display_name', $post->post_author)
                    );
                    if ($avatar) {
                        $color = 'var(--wpap-color-2)';

                        if (in_array($status, ['closed', 'solved'])) {
                            $color = 'var(--wpap-success-color)';
                        }

                        if ($status === 'open') {
                            $color = 'var(--wpap-error-color)';
                        }

                        if (in_array($status, $ticket_status['name'])) {
                            $key = array_search($status, $ticket_status['name']);
                            $color = $ticket_status['color'][$key];
                        }

                        printf(
                            '<div style="border-color: %s" class="wpap-latest-tickets-avatar">%s</div>',
                            esc_attr($color),
                            $avatar
                        );
                    }
                    ?>

                    <div class="wpap-list-item-title">
                        <?php printf('<h2>%s</h2>', wp_trim_words(esc_html($post->post_title), 10)); ?>

                        <span class="wpap-list-item-subtitle">
                            <?php
                            echo esc_html(get_the_author_meta('display_name', $post->post_author));

                            if ($status) {
                                echo ' - ' . esc_html(wpap_ticket_status_name($status));
                            }
                            ?>
                        </span>
                    </div>

                    <time>
                        <?php
                        echo human_time_diff(
                            get_post_modified_time('U', '', $post->ID),
                            current_time('timestamp')
                        );

                        esc_html_e(' قبل', 'arvand-panel');
                        ?>
                    </time>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="wpap-list-notfound wpap-list-item">
                <?php esc_html_e('تیکتی وجود ندارد.', 'arvand-panel'); ?>
            </div>
        <?php endif; ?>
    </div>
</div>