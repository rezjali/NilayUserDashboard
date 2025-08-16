<?php

namespace Arvand\ArvandPanel;

defined('ABSPATH') || exit;

class WPAPTicket
{
    public static function count($status = '', $department = []): int
    {
        $args = [
            'post_type' => 'wpap_ticket',
            'publish' => true,
            'post_parent' => 0,
            'fields' => 'ids',
            'numberposts' => -1
        ];

        if (!empty($status)) {
            $meta_query[] = ['key' => 'wpap_ticket_status', 'value' => $status];
        }

        $user_query = [];

        if (!is_admin()) {
            $user_query = [
                'relation' => 'OR',
                ['key' => 'wpap_ticket_recipient', 'value' => get_current_user_id()],
                ['key' => 'wpap_ticket_creator', 'value' => get_current_user_id()],
            ];
        }

        if (!empty($department)) {
            $user_query[] = ['key' => 'wpap_ticket_department', 'value' => $department, 'compare' => 'IN'];
        }

        $meta_query[] = $user_query;

        if (!empty($meta_query)) {
            $args['meta_query'] = $meta_query;
        }

        return count(get_posts($args));
    }

    public static function delete($post): void
    {
        if (!is_object($post)) {
            $post = get_post(absint($post));
        }

        if (!$post) {
            return;
        }

        if (0 == $post->post_parent) {
            $posts = get_posts([
                'post_type' => 'wpap_ticket',
                'post_parent' => $post->ID,
                'fields' => 'ids',
                'numberposts' => -1
            ]);

            if (!empty($posts)) {
                foreach ($posts as $child_post_id) {
                    $attachment = get_post_meta($child_post_id, 'wpap_ticket_attachment', true);
                    if ($attachment) {
                        WPAPFile::delete($attachment['path']);
                    }

                    wp_delete_post($child_post_id, true);
                }
            }
        }

        $attachment = get_post_meta($post->ID, 'wpap_ticket_attachment', true);
        if ($attachment) {
            WPAPFile::delete($attachment['path']);
        }

        wp_delete_post($post->ID, true);
    }

    public static function userDepartment($user_id)
    {
        return maybe_unserialize(get_user_meta($user_id, 'wpap_user_ticket_department', 1));
    }
}