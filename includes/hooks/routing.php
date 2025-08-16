<?php
defined('ABSPATH') || exit;

// Check if panel page slug changed
add_action('post_updated', function ($post_id, $post_after, $post_before) {
    if ($post_id !== wpap_pages_options()['panel_page_id']) {
        return;
    }

    if ($post_after->post_name !== $post_before->post_name) {
        update_option('wpap_panel_slug_changed', true);
    }
}, 10, 3);

// Add panel rewrite rule
add_action('init', function () {
    $panel_page = wpap_get_panel_page();
    if (!$panel_page) {
        return;
    }

    add_rewrite_rule(
        sprintf('^%s/(.*?)/?$', $panel_page->post_name),
        sprintf('index.php?pagename=%s&wpap-endpoint=$matches[1]', $panel_page->post_name),
        'top'
    );

    if (get_option('wpap_panel_slug_changed')) {
        flush_rewrite_rules();
        delete_option('wpap_panel_slug_changed');
    }
});

// Add plugin panel endpoint
add_filter('query_vars', function ($vars) {
    $vars[] = 'wpap-endpoint';
    return $vars;
});

// Panel routes
add_action('template_include', function ($template) {
    if (!wpap_is_panel_page()) {
        return $template;
    }

    require WPAP_INC_PATH . 'panel-routes.php';

    return $template;
});