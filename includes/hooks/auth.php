<?php
defined('ABSPATH') || exit;

// Redirects wp default register page to plugin form
add_action('init', function () {
    global $pagenow;
    $register = wpap_register_options();
    $pages_opt = wpap_pages_options();
    $register_page_url = get_permalink($pages_opt['register_page_id']);

    if (!$register['default_register'] || !$register_page_url) {
       return;
    }

    if ($pagenow === 'wp-login.php' && isset($_GET['action']) && $_GET['action'] === 'register') {
        wp_safe_redirect(esc_url($register_page_url));
        exit;
    }
});

// Redirects wp default login page to plugin form
add_filter('login_redirect', function ($redirect_to, $request, $user) {
    $login = wpap_login_options();
    $pages_opt = wpap_pages_options();
    $login_url = get_permalink($pages_opt['login_page_id']);

    if ($login['default_login'] && $login_url) {
        global $pagenow;

        if ('wp-login.php' === $pagenow) {
            wp_safe_redirect($login_url);
            exit;
        }

        $action = ['register', 'logout', 'lostpassword', 'resetpass'];

        if (isset($_GET['action']) && !in_array($_GET['action'], $action)) {
            wp_safe_redirect($login_url);
            exit;
        }
    }

    return $redirect_to;
}, 10, 3);

add_filter('logout_redirect', function ($redirect_to) {
    $pages_opt = wpap_pages_options();
    $after_logout_url = get_permalink($pages_opt['after_logout_page_id']);

    if ($after_logout_url) {
        return esc_url($after_logout_url);
    }

    return $redirect_to;
});

add_filter('retrieve_password_message', function ($message, $key, $user_login, $user_data) {
    add_filter('wp_mail_content_type', function () {
        return 'text/html';
    });

    $msg = __('Hello!', 'arvand-panel') . "\r\n\r\n";
    $msg .= sprintf(__('You asked us to reset your password for your account using the user name %s.', 'arvand-panel'), $user_login) . "\r\n\r\n";
    $msg .= __("If this was a mistake, or you didn't ask for a password reset, just ignore this email and nothing will happen.", 'arvand-panel') . "\r\n\r\n";
    $msg .= __('To reset your password, visit the following address:', 'arvand-panel') . "\r\n\r\n";
    $login = wpap_login_options();
    $pages_opt = wpap_pages_options();
    $reset_pass_page = get_post($pages_opt['reset_pass_page_id']);

    if ($login['default_login'] && $reset_pass_page) {
        $msg .= site_url("$reset_pass_page->post_name?action=rp&key=$key&login=" . rawurlencode($user_login), 'login') . "\r\n\r\n";
    } else {
        $msg .= site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user_login), 'login') . "\r\n\r\n";
    }

    $msg .= __('Thanks!', 'arvand-panel') . "\r\n";
    return wpap_email_template($msg);
}, 10, 4);

function wpap_action_redirect_password_reset(): void
{
    if ('GET' == $_SERVER['REQUEST_METHOD']) {
        $key = sanitize_text_field($_REQUEST['key']);
        $login = sanitize_text_field($_REQUEST['login']);
        $user = check_password_reset_key($key, $login);
        $pages_opt = wpap_pages_options();
        $login_page_url = get_permalink($pages_opt['login_page_id']);

        if (!$user || is_wp_error($user)) {
            if ($user && $user->get_error_code() === 'expired_key') {
                wp_redirect(esc_url("$login_page_url/?login=expiredkey"));
            } else {
                wp_redirect(esc_url("$login_page_url/?login=invalidkey"));
            }

            exit;
        }

        $reset_pass_page_url = get_permalink($pages_opt['reset_pass_page_id']);
        $redirect_url = esc_url($reset_pass_page_url);
        $redirect_url = add_query_arg('key', $key, $redirect_url);
        $redirect_url = add_query_arg('login', $login, $redirect_url);
        wp_redirect($redirect_url);
        exit;
    }
}
add_action('login_form_rp', 'wpap_action_redirect_password_reset');
add_action('login_form_resetpass', 'wpap_action_redirect_password_reset');