<?php
defined('ABSPATH') || exit;

use Arvand\ArvandPanel\WPAPTicket;

add_action('wp_ajax_wpap_delete_ticket', function () {
    if (isset($_POST['ticket'])) {
        WPAPTicket::delete((int)$_POST['ticket']);

        wp_send_json('success');
    }

    wp_send_json('error');
});

add_action('wp_ajax_wpap_delete_supporter', function () {
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'del_supporter_nonce')) {
        wp_send_json('error');
    }

    if (!isset($_POST['user'])) {
        wp_send_json('error');
    }

    delete_user_meta((int)$_POST['user'], 'wpap_user_ticket_department');
    wp_send_json('success');
});

add_action('admin_init', function () {
    if (empty($_POST['wpap_ticket_delete']) || empty($_POST['wpap_ticket_delete_nonce'])) {
        return;
    }

    if (!wp_verify_nonce($_POST['wpap_ticket_delete_nonce'], 'wpap_ticket_delete')) {
        return;
    }

    $post = get_post(absint($_POST['wpap_ticket_delete']));
    WPAPTicket::delete($post);

    if ($post->post_parent) {
        wp_redirect(add_query_arg(['section' => 'single', 'ticket' => $post->post_parent], admin_url('admin.php?page=wpap-tickets')));
    } else {
        wp_redirect(admin_url('admin.php?page=wpap-tickets'));
    }

    exit;
});