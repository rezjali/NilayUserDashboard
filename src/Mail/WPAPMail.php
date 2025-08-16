<?php

namespace Arvand\ArvandPanel\Mail;

defined('ABSPATH') || exit;

class WPAPMail
{
    public static function registerMail($email, $user, $subject, $content, $activation = false)
    {
        $headers = ['Content-Type: text/html; charset=UTF-8'];
        $search = ['[first_name]', '[last_name]', '[user_login]', '[user_email]', '[site_name]', '[site_url]', '[activation_link]'];
        $site_link = sprintf('<a href="%s">%s</a>', site_url(), site_url());
        $replace = [$user->first_name, $user->last_name, $user->user_login, $user->user_email, get_bloginfo('name'), $site_link];

        if ($activation) {
            $salt = wp_generate_password(20);
            $key = sha1($salt . sanitize_email($email) . uniqid(time(), true));
            $update = wp_update_user(['ID' => $user->ID, 'user_activation_key' => $key]);

            if (!is_wp_error($update)) {
                $pages = wpap_pages_options();
                $login_url = get_permalink($pages['login_page_id']) ?: wp_login_url();

                $activation_link = sprintf(
                    '<a href="%s">%s</a>',
                    $login_url . '?email=' . $email . '&key=' . $key,
                    __('activation link', 'arvand-panel')
                );

                $replace[] = apply_filters('wpap_email_activation_link', $activation_link, $email, $user, $content);
            }
        }

        wp_mail($email, $subject, wpap_email_template(str_replace($search, $replace, $content)), $headers);
    }
}