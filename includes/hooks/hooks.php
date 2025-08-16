<?php
defined('ABSPATH') || exit;

use Arvand\ArvandPanel\WPAPPluginActivation;
use Arvand\ArvandPanel\WPAPTicket;

register_activation_hook(WPAP_DIR_PATH . 'arvand-panel.php', function () {
    (new WPAPPluginActivation)->activation();
});

add_action('plugins_loaded', function () {
    load_plugin_textdomain('arvand-panel', false, dirname(plugin_basename(WPAP_DIR_PATH . 'arvand-panel.php')) . '/languages');
});

// Disable wp admin bar
add_action('after_setup_theme', function () {
    if (!current_user_can('administrator') && !is_admin()) {
        $user = get_user_by('ID', get_current_user_id());

        if ($user and isset($user->roles[0]) && in_array($user->roles[0], wpap_general_options()['admin_bar_access'])) {
            show_admin_bar(false);
        }
    }
});

// Check admin area access
add_action('admin_init', function () {
    $general = wpap_general_options();

    if (!current_user_can('manage_options') and !wp_doing_ajax()) {
        $user = get_user_by('ID', get_current_user_id());

        if (in_array($user->roles[0], $general['admin_area_access'])) {
            wp_safe_redirect(home_url());
            exit;
        }
    }
});

add_filter('theme_page_templates', function ($page_templates, $theme, $post) {
    $page_templates['wpap-panel'] = __('اروند پنل 1', 'arvand-panel');
    $page_templates['wpap-panel-2'] = __('اروند پنل 2', 'arvand-panel');

    return $page_templates;
}, 10, 3);

add_filter('template_include', function ($template) {
    global $post;
    $template_slug = get_page_template_slug($post);

    if ('wpap-panel' === $template_slug) {
        $template = WPAP_TEMPLATES_PATH . 'pages/wpap-panel.php';
    } elseif ('wpap-panel-2' === $template_slug) {
        $template = WPAP_TEMPLATES_PATH . 'pages/wpap-panel-2.php';
    }

    return $template;
});

add_filter('wp_mail_from_name', function ($al_email_from) {
    return get_bloginfo('name');
});

add_filter('get_avatar', function ($avatar, $id_or_email, $size, $default, $alt, $args) {
    $profile_img_url = '';

    if (is_object($id_or_email)) {
        $user = get_user_by('email', $id_or_email->comment_author_email);

        if ($user) {
            $profile_img_url = get_user_meta($user->ID, 'wpap_profile_img', true);
        }
    } else {
        $profile_img_url = get_user_meta($id_or_email, 'wpap_profile_img', true);
    }

    if ($profile_img_url) {
        $class = array('avatar', 'avatar-' . (int)$args['size'], 'photo');

        if (!$args['found_avatar'] or $args['force_default']) {
            $class[] = 'avatar-default';
        }

        if ($args['class']) {
            if (is_array($args['class'])) {
                $class = array_merge($class, $args['class']);
            } else {
                $class[] = $args['class'];
            }
        }

        $class = esc_attr(implode(' ', $class));

        $profile_img = "<img alt='$alt' src='$profile_img_url' class='$class' style='object-fit: cover' height='$size' width='$size'/>";
    } else {
        $profile_img = $avatar;
    }

    return $profile_img;
}, 10, 6);

// Download ticket attachment
add_action('init', function () {
    if (!isset($_GET['ticket_attachment_download'], $_GET['ticket_attachment_download_nonce'])) {
        return;
    }

    if (!wp_verify_nonce($_GET['ticket_attachment_download_nonce'], 'ticket_attachment_download')) {
        return;
    }

    if (!$post = get_post((int)$_GET['ticket_attachment_download'])) {
        return;
    }

    $recipient = (int)get_post_meta($post->ID, 'wpap_ticket_recipient', true);
    $creator = (int)get_post_meta($post->ID, 'wpap_ticket_creator', true);
    $department = get_post_meta($post->ID, 'wpap_ticket_department', true);
    $user_id = get_current_user_id();
    $user_dep = WPAPTicket::userDepartment($user_id);

    if (!in_array($user_id, [$creator, $recipient]) && (!empty($user_dep) && !in_array($department, $user_dep))) {
        return;
    }

    $post = get_post_meta($post->ID, 'wpap_ticket_attachment', true);

    if (!empty($post['path'])) {
        $path = wp_upload_dir()['basedir'] . DIRECTORY_SEPARATOR . $post['path'];
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: post; filename=' . basename($post['path']));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($path));
        readfile($path);
        exit;
    }
});

// Download private message attachment
add_action('init', function () {
    if (!isset($_GET['msg_attachment_download'], $_GET['msg_attachment_download_nonce'])) {
        return;
    }

    if (!wp_verify_nonce($_GET['msg_attachment_download_nonce'], 'msg_attachment_download')) {
        return;
    }

    if (!$post = get_post((int)$_GET['msg_attachment_download'])) {
        return;
    }

    $recipient = (int)get_post_meta($post->ID, 'wpap_private_msg_recipient', true);
    $user_id = get_current_user_id();

    if (!in_array($user_id, [$post->post_author, $recipient])) {
        return;
    }

    $attachment = get_post_meta($post->ID, 'wpap_msg_attachment', true);

    if (!empty($attachment['path'])) {
        $path = wp_upload_dir()['basedir'] . DIRECTORY_SEPARATOR . $attachment['path'];
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($attachment['path']));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($path));
        readfile($path);
        exit;
    }
});

// Download uploaded file by register file input
add_action('init', function () {
    if (empty($_GET['wpap_file_field']) || empty($_GET['wpap_file'])) {
        return;
    }

    $meta_key = sanitize_text_field($_GET['wpap_file_field']);
    $user_id = (int)$_GET['wpap_file'];
    $meta = get_user_meta($user_id, $meta_key, 1);

    if (!$meta) {
        return;
    }

    $file = wp_upload_dir()['basedir'] . DIRECTORY_SEPARATOR . $meta['path'];
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename=' . basename($file));
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    readfile($file);
    exit;
});

// Delete uploaded file by register file input
add_action('init', function () {
    if (empty($_GET['wpap_file_field_delete']) || empty($_GET['wpap_file'])) {
        return;
    }

    $meta_key = sanitize_text_field($_GET['wpap_file_field_delete']);
    $user_id = (int)$_GET['wpap_file'];
    $meta = get_user_meta($user_id, $meta_key, 1);

    if ($meta && file_exists($file = wp_upload_dir()['basedir'] . DIRECTORY_SEPARATOR . $meta['path'])) {
        unlink($file);
        delete_user_meta($user_id, $meta_key, $meta);
    }

    wp_redirect(wp_get_referer());
    exit;
});