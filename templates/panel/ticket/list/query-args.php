<?php
defined( 'ABSPATH' ) || exit;

$offset = ($panel_page * $limit) - $limit;

$args = [
    'post_type' => 'wpap_ticket',
    'post_status' => 'publish',
    'post_parent' => 0,
    'posts_per_page' => $limit,
    'paged' => $panel_page,
    'offset' => $offset,
    'order' => 'desc',
    'fields' => 'ids'
];

$user_query = [
    'relation' => 'OR',
    ['key' => 'wpap_ticket_recipient', 'value' => $current_user->ID],
    ['key' => 'wpap_ticket_creator', 'value' => $current_user->ID],
];

if (!empty($user_dep)) {
    $user_query[] = [
        'key' => 'wpap_ticket_department',
        'value' => $user_dep,
        'compare' => 'IN'
    ];
}

$args['meta_query'][] = $user_query;

if (!empty($_POST['ticket_number'])) {
    $args['post__in'] = [(int)$_POST['ticket_number']];
}

if (!empty($_POST['ticket_title'])) {
    $args['s'] = sanitize_text_field($_POST['ticket_title']);
}

if (!empty($_GET['ticket-status'])) {
    $status = sanitize_text_field($_GET['ticket-status']);
} elseif (!empty($_POST['ticket_status'])) {
    $status = sanitize_text_field($_POST['ticket_status']);
}

if (!empty($status)) {
    $args['meta_query'][] = ['key' => 'wpap_ticket_status', 'value' => $status];
}

if (!empty($_POST['ticket_author_user_login'])) {
    $user = get_user_by('login', sanitize_user($_POST['ticket_author_user_login']));
    if ($user) {
        $args['author'] = $user->ID;
    }
}

return $args;