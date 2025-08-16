<?php

namespace Arvand\ArvandPanel;

defined('ABSPATH') || exit;

class WPAPMessage
{
    public static function count(int $recipient_id = 0, int $sender_id = 0, int $parent = 0): int
    {
        $args = [
            'post_type' => 'wpap_private_message',
            'post_status' => 'publish',
            'numberposts' => -1,
            'post_parent' => $parent,
            'fields' => 'ids'
        ];

        if ($recipient_id > 0) {
            $args['meta_key'] = 'wpap_private_msg_recipient';
            $args['meta_value'] = $recipient_id;
        }

        if ($sender_id > 0) {
            $args['author'] = $sender_id;
        }

        return count(get_posts($args));
    }

    public static function unseenCount(int $parent = 0): int
    {
        $args = [
            'post_type' => 'wpap_private_message',
            'post_status' => 'publish',
            'numberposts' => -1,
            'fields' => 'ids',
            'meta_query' => [
                ['key' => 'wpap_seen', 'value' => 0],
                ['key' => 'wpap_private_msg_recipient', 'value' => get_current_user_id()],
            ]
        ];

        if ($parent > 1) {
            $args['post_parent'] = $parent;
        }

        return count(get_posts($args));
    }

    public static function adminUnseenCount(int $user_id = null, int $parent = 0, int $author = 0): int
    {
        $args = [
            'post_type' => 'wpap_private_message',
            'numberposts' => -1,
            'post_status' => 'publish',
            'fields' => 'ids',

        ];

        $args['meta_query'] = [
            ['key' => 'wpap_admin_seen', 'value' => 0],
        ];

        if ($user_id) {
            $args['meta_query'][] = ['key' => 'wpap_private_msg_recipient', 'value' => $user_id];
        }

        if ($parent > 1) {
            $args['post_parent'] = $parent;
        }

        if ($author > 1) {
            $args['author'] = $author;
        }

        return count(get_posts($args));
    }

    public static function readCount(int $user_id): int
    {
        return count(get_posts([
            'post_type' => 'wpap_private_message',
            'post_status' => 'publish',
            'meta_query' => [
                ['key' => 'wpap_private_msg_recipient', 'value' => $user_id],
                ['key' => 'wpap_msg_read', 'compare' => 'NOT EXISTS']
            ],
            'numberposts' => -1,
            'fields' => 'ids'
        ]));
    }
}