<?php

use Arvand\ArvandPanel\Form\WPAPFieldSettings;
use Arvand\ArvandPanel\WPAPFile;

defined( 'ABSPATH' ) || exit;

add_action('user_edit_form_tag', function () {
    echo ' enctype="multipart/form-data"';
});

add_action('edit_user_profile', 'wpap_wallet_admin_user_field');
add_action('show_user_profile', 'wpap_wallet_admin_user_field');
function wpap_wallet_admin_user_field($user)
{
    if (!current_user_can('manage_woocommerce')) {
        return;
    }
    ?>
    <h2><?php esc_html_e('شارژ کیف پول کاربر', 'arvand-panel'); ?></h2>

    <table class="form-table">
        <tr>
            <th>
                <label><?php esc_html_e('مبلغ فعلی کیف پول', 'arvand-panel'); ?></label>
            </th>

            <td>
                <strong>
                    <?php
                    echo wp_kses_post(
                        wc_price(wpap_wallet_get_balance($user->ID))
                    );
                    ?>
                </strong>
            </td>
        </tr>

        <tr>
            <th>
                <label for="wpap_wallet_amount">
                    <?php esc_html_e('مبلغ شارژ (تومان)', 'arvand-panel'); ?>
                </label>
            </th>

            <td>
                <input type="number" min="0" name="wpap_wallet_amount" id="wpap_wallet_amount" class="regular-text" placeholder="مثلاً 50000" />
                <p class="description"><?php esc_html_e('برای افزایش موجودی کیف پول کاربر، مبلغ را وارد کنید.', 'arvand-panel'); ?></p>
            </td>
        </tr>
    </table>
    <?php
}

add_action('edit_user_profile_update', 'wpap_wallet_admin_user_save');
add_action('personal_options_update', 'wpap_wallet_admin_user_save');
function wpap_wallet_admin_user_save($user_id)
{
    if (!current_user_can('manage_woocommerce')) {
        return;
    }

    if (empty($_POST['wpap_wallet_amount'])) {
        return;
    }

    $amount = floatval($_POST['wpap_wallet_amount']);
    if ($amount <= 0) {
        return;
    }

    wpap_wallet_insert_transaction(
        $user_id,
        $amount,
        'credit',
        __('شارژ کیف پول توسط مدیر', 'arvand-panel')
    );
}

function wpap_action_user_status_field($user): void
{
    if ($user->ID !== get_current_user_id()): ?>
        <h2><?php esc_html_e('وضعیت حساب کاربر', 'arvand-panel'); ?></h2>

        <table class="form-table">
            <?php wp_nonce_field('wpap_user_status_field_nonce', 'wpap_user_status_field_nonce'); ?>

            <tr>
                <th><label for="wpap-user-status"><?php _e('Activate user panel', 'arvand-panel'); ?></label></th>

                <td>
                    <?php $status = get_user_meta($user->ID, 'wpap_user_status', true); ?>
                    <input id="wpap-user-status" type="checkbox" name="wpap_user_status" <?php checked($status); ?>/>
                    <?php $register = wpap_register_options();

                    if ($register['enable_admin_approval']): ?>
                        <p class="description">
                            <?php esc_html_e("If you enable this option, an account confirmation notification email will be sent to the user's email.", 'arvand-panel'); ?>
                        </p>
                    <?php endif; ?>
                </td>
            </tr>
        </table>
    <?php endif;
}
add_action('show_user_profile', 'wpap_action_user_status_field');
add_action('edit_user_profile', 'wpap_action_user_status_field');

function wpap_action_user_status_field_save($user_id): void
{
    if (empty($_POST['wpap_user_status_field_nonce']) || !wp_verify_nonce($_POST['wpap_user_status_field_nonce'], 'wpap_user_status_field_nonce')) {
        return;
    }

    if (empty($_POST['wpap_user_status'])) {
        return;
    }

    if (!current_user_can('edit_user', $user_id)) {
        return;
    }

    update_user_meta($user_id, 'wpap_user_status', 1);
    $register = wpap_register_options();

    if ($register['enable_admin_approval']) {
        $user = get_user_by('ID', $user_id);
        $headers = ['Content-Type: text/html; charset=UTF-8'];
        $search = ['[first_name]', '[last_name]', '[user_login]', '[user_email]', '[site_name]', '[site_url]'];

        $replace = [
            $user->first_name,
            $user->last_name,
            $user->user_login,
            $user->user_email,
            get_bloginfo('name'),
            '<a href="' . esc_url(site_url()) . '">' . site_url() . '</a>'
        ];

        $email_opt = wpap_email_options();
        $email_subject = sanitize_text_field($email_opt['admin_approval_email_subject']);
        $change = str_replace($search, $replace, $email_opt['admin_approval_email']);
        wp_mail($user->user_email, $email_subject, wpap_email_template($change), $headers);
    }
}
add_action('personal_options_update', 'wpap_action_user_status_field_save');
add_action('edit_user_profile_update', 'wpap_action_user_status_field_save');

function wpap_action_custom_register_fields($user): void
{
    ?>
    <h2><?php esc_html_e('مشخصات کاربر', 'arvand-panel'); ?></h2>

    <table class="form-table">
        <?php
        wp_nonce_field('wpap_reg_custom_fields_nonce', 'wpap_reg_custom_fields_nonce');
        $fields = WPAPFieldSettings::get();

        foreach ($fields as $field) {
            $field_class = 'Arvand\ArvandPanel\Form\Fields\wpap_field_' . $field['field_name'];
            if (!class_exists($field_class)) {
                continue;
            }

            $field_class = new $field_class;

            if ('user_meta' !== $field_class->type) {
                continue;
            }

            $meta = get_user_meta($user->ID, sanitize_text_field($field['meta_key']), 1);

            if (method_exists($field_class, 'adminOutput')) {
                $field_class->adminOutput($field, $meta, $user->ID);
            } else {
                $field_class->output($field, $meta, $user->ID);
            }
        }

        $register = wpap_register_options();

        if ($register['enable_agree']):
            $agree = get_user_meta($user->ID, 'wpap_agree_to_terms', true); ?>

            <tr>
                <th><label for="wpap-agree"><?php esc_html_e('Agree to terms', 'arvand-panel') ?></label></th>

                <td>
                    <input id="wpap-agree" type="checkbox" name="wpap_agree" <?php checked($agree); ?>/>
                    <?php echo wp_kses($register['agree_text'], 'post'); ?>
                </td>
            </tr>
        <?php endif; ?>
    </table>
    <?php
}
add_action('show_user_profile', 'wpap_action_custom_register_fields');
add_action('edit_user_profile', 'wpap_action_custom_register_fields');

function wpap_action_custom_register_fields_save($user_id): void
{
    if (empty($_POST['wpap_reg_custom_fields_nonce']) || !wp_verify_nonce($_POST['wpap_reg_custom_fields_nonce'], 'wpap_reg_custom_fields_nonce')) {
        return;
    }

    if (!current_user_can('edit_user', $user_id)) {
        return;
    }

    $fields = WPAPFieldSettings::get();

    foreach ($fields as $field) {
        $name = $field['field_name'];
        $field_class = 'Arvand\ArvandPanel\Form\Fields\wpap_field_' . $name;

        if (!class_exists($field_class)) {
            continue;
        }

        $field_class = new $field_class;

        if ('user_meta' !== $field_class->type) {
            continue;
        }

        if (method_exists($field_class, 'adminValidation')) {
            if (!call_user_func([$field_class, 'adminValidation'], $field)) {
                continue;
            }
        }

        $meta_key = sanitize_text_field($field['meta_key']);

        if ('file' !== $field['attrs']['type']) {
            if (method_exists($field_class, 'value')) {
                update_user_meta($user_id, $meta_key, $field_class->value($field));
            } else {
                update_user_meta($user_id, $meta_key, sanitize_text_field($_POST[$field['attrs']['name']] ?? ''));
            }
        }

        if ('file' === $field['attrs']['type'] && !empty($_FILES[$field['attrs']['name']]['tmp_name'])) {
            $prev_file = get_user_meta($user_id, $field['meta_key'], 1);

            if ($prev_file && file_exists($file = wp_upload_dir()['basedir'] . DIRECTORY_SEPARATOR . $prev_file['path'])) {
                unlink($file);
            }

            $upload = WPAPFile::upload($_FILES[$field['attrs']['name']], 'user');

            if (false !== $upload) {
                update_user_meta($user_id, $field['meta_key'], $upload);
            }
        }
    }

    $register = wpap_register_options();

    if ($register['enable_agree']) {
        update_user_meta($user_id, 'wpap_agree_to_terms', isset($_POST['wpap_agree']));
    }
}
add_action('personal_options_update', 'wpap_action_custom_register_fields_save');
add_action('edit_user_profile_update', 'wpap_action_custom_register_fields_save');

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
        $class = ['avatar', 'avatar-' . (int)$args['size'], 'photo'];

        if (!$args['found_avatar'] || $args['force_default']) {
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

// Delete user avatar image when user deleted
add_action('delete_user', function (int $user_id): void {
    $path = get_user_meta($user_id, 'wpap_profile_img_path', true);
    wp_delete_file($path);
});

// Delete user private messages and attachments when user deleted
add_action('delete_user', function (int $user_id): void {
    // delete private messages
    $posts = get_posts(['post_type' => 'wpap_private_message', 'author' => $user_id, 'numberposts' => -1]);

    if (count($posts)) {
        foreach ($posts as $post) {
            $attachment = get_post_meta($post->ID, 'wpap_msg_attachment', true);
            $wp_upload_dir = wp_get_upload_dir();

            if (!empty($attachment['path'])) {
                wp_delete_file($wp_upload_dir['basedir'] . '/' . $attachment['path']);
            }

            wp_delete_post($post->ID, true);
        }
    }

    // delete user files
    $fields = WPAPFieldSettings::get();

    foreach ($fields as $field) {
        if ('file' === $field['attrs']['type']) {
            if ($meta = get_user_meta($user_id, $field['meta_key'], 1)) {
                unlink(wp_upload_dir()['basedir'] . '/' . $meta['path']);
            }
        }
    }
});