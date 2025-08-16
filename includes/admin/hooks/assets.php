<?php
defined('ABSPATH') || exit;

$pages = [
    'wpap-tickets',
    'arvand-panel',
    'wpap-panel-menus',
    'wpap-register-fields',
    'wpap-private-message',
    'wpap-info'
];

$current_page = $_GET['page'] ?? '';

add_action('admin_enqueue_scripts', function () use ($current_page, $pages) {
    $section = $_GET['section'] ?? '';

    if (in_array($current_page, $pages)) {
        wp_enqueue_style(
            'wpap_admin_styles',
            WPAP_ASSETS_URL . 'admin/css/main.css',
            [],
            current_time('timestamp')
        );

        wp_enqueue_style(
            'wpap_remix_icons',
            WPAP_ASSETS_URL . 'icons/remixicon.css',
            [],
            current_time('timestamp')
        );

        // ------ TEMP ------ //
        wp_enqueue_style(
            'wpap_icons',
            WPAP_ASSETS_URL . 'icons/bootstrap-icons.min.css',
            [],
            current_time('timestamp')
        );

        wp_enqueue_script(
            'wpap_main',
            WPAP_ASSETS_URL . 'admin/js/main.js',
            ['jquery'],
            current_time('timestamp'),
            true
        );

        wp_localize_script(
            'wpap_main',
            'wpapMain', [
                'ticketStatusPlaceholder' => __('Enter status name.', 'arvand-panel'),
                'ticketDepPlaceholder' => __('Enter department name.', 'arvand-panel'),
                'deleteText' => __('Delete', 'arvand-panel'),
            ]
        );

        wp_enqueue_script(
            'wpap_ajax',
            WPAP_ASSETS_URL . 'admin/js/ajax.js',
            ['jquery'],
            current_time('timestamp'),
            true
        );

        wp_localize_script(
            'wpap_ajax',
            'WPAPAjax',
            [
                'ajaxurl' => admin_url('admin-ajax.php'),
                'notfoundSupporterMessage' => __('There is no supporter.', 'arvand-panel'),
                'delTicketConfirm' => __('Are you sure to delete this ticket?', 'arvand-panel'),
                'resetStylesConfirm' => __('Are you sure to reset all styles?', 'arvand-panel'),
                'delRoleConfirm' => __('Are you sure to delete? new role for users who had the role deleted is: ', 'arvand-panel'),
                'newRoleError' => __('Please select another new role. this role will be deleted.', 'arvand-panel'),
                'newRoleText' => __(' role is set for users who had previously deleted roles.', 'arvand-panel'),
                'nothingRole' => __('There is no role.', 'arvand-panel'),
                'deleteMenuMessage' => __('Aru you sure to delete?', 'arvand-panel')
            ]
        );

        wp_enqueue_script('jquery-ui-sortable');

        if ('panel' === $section) {
            wp_enqueue_media();
            wp_enqueue_script('wpap_media', WPAP_ASSETS_URL . 'admin/js/media.js', ['jquery'], current_time('timestamp'), true);
        }

        if ('styles' === $section || 'wpap-panel-menus' === $current_page) {
            wp_enqueue_style('wp-color-picker');
            wp_enqueue_script('wp-color-picker');
        }

        if ('colors' === $section) {
            wp_enqueue_style(
                'wpap_coloris_css',
                WPAP_ASSETS_URL . 'admin/css/coloris.css',
                [],
                current_time('timestamp'),
            );

            wp_enqueue_script(
                'wpap_coloris_js',
                WPAP_ASSETS_URL . 'admin/js/coloris.js',
                [],
                current_time('timestamp'),
            );
        }
    }

    if ('wpap-panel-menus' === $current_page && in_array($section, ['new', 'edit'])) {
        wp_enqueue_media();
    }
});

// ------ TEMP ------ //
add_action('admin_head', function () use ($current_page, $pages) {
    if (in_array($current_page, $pages)) {
        echo '<link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet"/>';
    }
});