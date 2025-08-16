<?php

namespace Arvand\ArvandPanel;

defined('ABSPATH') || exit;

class WPAPNotification
{
    public static function getUnseenCount($user = null): int
    {
        $user_id = get_current_user_id();
        $last_read = get_user_meta($user_id, 'wpap_notice_seen_date', true);
        $count = 0;

        $ids = get_posts([
            'post_type' => 'wpap_notifications',
            'publish' => true,
            'fields' => 'ids',
            'numberposts' => -1
        ]);

        foreach ($ids as $id) {
            if ($user && !self::isRecipient($user, $id)) {
                continue;
            }

            if (!$last_read || get_post_timestamp($id) > $last_read) {
                $count++;
            }
        }

        return $count;
    }

    public static function getSeenDate($user_id)
    {
        return get_user_meta($user_id, 'wpap_notice_seen_date', 1);
    }

    public static function getUserNoticeIDs($user): array
    {
        $ids = get_posts([
            'post_type' => 'wpap_notifications',
            'numberposts' => -1,
            'fields' => 'ids'
        ]);

        if (empty($ids)) {
            return [];
        }

        $user_notice_ids = [];
        foreach ($ids as $id) {
            if (self::isRecipient($user, $id)) {
                $user_notice_ids[] = $id;
            }
        }

        return $user_notice_ids;
    }

    public static function setSeenDate($user_id): void
    {
        $notices = get_posts([
            'post_type' => 'wpap_notifications',
            'numberposts' => -1,
            'fields' => 'ids'
        ]);

        if ($notices) {
            update_user_meta($user_id, 'wpap_notice_seen_date', get_post_timestamp($notices[0]));
        }
    }

    public static function importantNotices($menu_name = null, $current_user = null): string
    {
        $posts = get_posts([
            'post_type' => 'wpap_notifications',
            'post_status' => 'publish',
            'meta_query' => [
                ['key' => 'wpap_important_notice', 'value' => 1],
                [
                    'relation' => 'OR',
                    ['key' => 'wpap_important_notice_place', 'value' => 'all'],
                    ['key' => 'wpap_important_notice_place', 'value' => sanitize_title($menu_name ?? '')]
                ]
            ],
            'numberposts' => -1
        ]);

        ob_start();

        if (count($posts) > 0):
            $class = (empty($_GET['section']) || 'dash' === $_GET['section']) ? 'class=wpap-dashboard-important-notice' : '';
            ?>
            <div id="wpap-important-notice" <?php echo esc_attr($class); ?>>
                <?php foreach ($posts as $post):
                    if (!self::isRecipient($current_user, $post->ID)) {
                        continue;
                    }

                    $type = get_post_meta($post->ID, 'wpap_important_notice_type', true);

                    $view_link = sprintf('<a class="wpap-notice-view-details-link" href="%s">%s</a>',
                        esc_url(wpap_panel_url('notifications/' . $post->ID)),
                        esc_html__('مشاهده جزئیات', 'arvand-panel')
                    );
                    ?>

                    <div class="wpap-msg wpap-<?php echo esc_attr($type); ?>-msg">
                        <?php
                        switch ($type) {
                            case 'error';
                                $icon = 'ri-close-circle-line';
                                break;
                            case 'success';
                                $icon = 'ri-checkbox-circle-line';
                                break;
                            case 'warning';
                                $icon = 'ri-alarm-warning-line';
                                break;
                            default:
                                $icon = 'ri-information-line';
                        }
                        ?>

                        <i class="<?php echo esc_attr($icon); ?>"></i>

                        <?php if (empty($post->post_title)): ?>
                            <span class="wpap-msg-text">
                                <?php echo wp_trim_words(wp_strip_all_tags($post->post_content), 20); ?>
                            </span>

                            <?php echo $view_link; ?>
                        <?php else: ?>
                            <div>
                                <h3><?php echo esc_html($post->post_title); ?></h3>
                                <span><?php echo wp_trim_words(wp_strip_all_tags($post->post_content), 40); ?></span>
                                <?php echo $view_link; ?>
                            </div>
                        <?php endif; ?>

                        <i class="ri-close-large-line wpap-dismiss-msg"></i>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif;

        return ob_get_clean();
    }

    public static function isRecipient($current_user, $notice_id): bool
    {
        $recipient_type = get_post_meta($notice_id, 'wpap_notice_recipient_type', 1);

        if ('all' !== $recipient_type) {
            $notice_roles = get_post_meta($notice_id, 'wpap_important_notice_roles', 1);
            if ('roles' === $recipient_type && empty(array_intersect(array_values($current_user->roles), $notice_roles))) {
                return false;
            }

            $notice_user_id = get_post_meta($notice_id, 'wpap_important_notice_user_id', 1);
            if ('user' === $recipient_type && $current_user->ID != $notice_user_id) {
                return false;
            }
        }

        return true;
    }
}