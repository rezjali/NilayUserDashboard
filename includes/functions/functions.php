<?php
defined('ABSPATH') || exit;

function wpap_template(string $name, array $data = []): void
{
    extract($data);
    include_once WPAP_DIR_PATH . '/templates/' . str_replace('.', '/', $name) . '.php';
}

function wpap_is_demo(): bool
{
    global $current_user;
    return $current_user->user_login === WPAP_DEMO;
}

function wpap_sanitize_array_fields($data, $allowed_empty = true): array
{
    $fields = [];

    if (is_array($data)) {
        for ($i = 0; $i < count($data); $i++) {
            if (!$allowed_empty && empty($data[$i])) {
                continue;
            }

            $fields[] = sanitize_text_field($data[$i]);
        }
    }

    return $fields;
}

function wpap_en_num($input): string
{
    $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
    $english = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

    return str_replace($persian, $english, $input);
}

function wpap_email_template($content): string
{
    ob_start(); ?>

    <div style="
        background-color: #fff;
        max-width: 600px;
        margin: auto;
        padding: 20px;
        <?php echo is_rtl() ? 'direction: rtl;' : ''; ?>
        font-family: Tahoma, Arial, serif;
        font-size: 16px;
        border: 1px solid #e6e6e6;
        border-radius: 5px;
    ">
        <?php echo wpautop($content); ?>
    </div>

    <?php return ob_get_clean();
}

function wpap_print_notice($text, string $type, bool $dismissible = true, string $margin = '0'): void
{
    switch ($type) {
        case 'error':
            $icon = 'ri-checkbox-circle-line';
            break;
        case 'success':
            $icon = 'ri-close-circle-line';
            break;
        default:
            $icon = 'ri-information-line';
    }

    ob_start();
    ?>
    <div class="wpap-msg wpap-<?php echo esc_attr($type); ?>-msg" <?php echo "style='margin: $margin;'"; ?>>
        <i class="<?php echo esc_attr($icon); ?>"></i>

        <?php if (!is_array($text)): ?>
            <span class="wpap-msg-text"><?php echo wp_kses_post($text); ?></span>
        <?php else: ?>
            <ul>
                <?php
                foreach ($text as $message) {
                    echo '<li>' . wp_kses_post($message) . '</li>';
                }
                ?>
            </ul>
        <?php endif; ?>

        <?php if ($dismissible): ?>
            <i class="ri-close-large-line wpap-dismiss-msg"></i>
        <?php endif; ?>
    </div>
    <?php
    echo ob_get_clean();
}

function wpap_recaptcha_validate($secret): bool
{
    $captcha = $_POST['g-recaptcha-response'];
    $private_key = sanitize_text_field($secret);
    $url = 'https://www.google.com/recaptcha/api/siteverify';

    $data = [
        'secret' => $private_key,
        'response' => $captcha,
        'remoteip' => $_SERVER['REMOTE_ADDR']
    ];

    $curl_config = [
        CURLOPT_URL => $url,
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POSTFIELDS => $data
    ];

    $init = curl_init();
    curl_setopt_array($init, $curl_config);
    $response = curl_exec($init);
    curl_close($init);
    $response_data = json_decode($response);
    return $response_data->success;
}

function wpap_register_fields_save($fields): void
{
    $user = [];
    $user_meta = [];
    $files = [];

    foreach ($fields as $field) {
        if (!in_array($field['display'], ['register', 'both'])) {
            continue;
        }

        $name = $field['field_name'];
        $field_class = 'Arvand\ArvandPanel\Form\Fields\wpap_field_' . $name;

        if (!class_exists($field_class)) {
            continue;
        }

        $field_class = new $field_class;
        $attr_name = $field['attrs']['name'];

        if ('users' === $field_class->type) {
            if (method_exists($field_class, 'value')) {
                $user[$attr_name] = $field_class->value();
            } else {
                $user[$attr_name] = sanitize_text_field($_POST[$attr_name]);
            }
        }

        if ('user_meta' === $field_class->type) {
            if ('file' === $field['attrs']['type']) {
                if (empty($_FILES[$attr_name]['tmp_name'])) {
                    continue;
                }

                $files[] = ['file' => $_FILES[$attr_name], 'meta_key' => $field['meta_key']];
            } else {
                if (method_exists($field_class, 'value')) {
                    $user_meta[$field['meta_key']] = $field_class->value($field);
                } else {
                    $user_meta[sanitize_text_field($field['meta_key'])] = sanitize_text_field($_POST[$attr_name] ?? '');
                }
            }
        }
    }

    $register = wpap_register_options();
    $user_id = wp_insert_user($user + ['meta_input' => $user_meta]);

    if (is_wp_error($user_id)) {
        wp_send_json_error(__('There is a problem with registration.', 'arvand-panel'));
    }

    if (!empty($files)) {
        foreach ($files as $item) {
            $upload = \Arvand\ArvandPanel\WPAPFile::upload($item['file'], 'user');

            if (false !== $upload) {
                add_user_meta($user_id, sanitize_text_field($item['meta_key']), $upload);
            }
        }
    }

    if ($register['enable_agree'] && isset($_POST['agree'])) {
        add_user_meta($user_id, 'wpap_agree_to_terms', 1);
    }

    add_user_meta($user_id, 'wpap_user_status', 0);

    $user = get_user_by('ID', $user_id);

    do_action('wpap_register_fields_save', $user);

    $email_opt = wpap_email_options();

    \Arvand\ArvandPanel\Mail\WPAPMail::registerMail(
        get_bloginfo('admin_email'),
        $user,
        $email_opt['reg_email_subject'],
        $email_opt['reg_email_content']
    );

    if (!$register['enable_admin_approval'] && $register['register_activation']) {
        \Arvand\ArvandPanel\Mail\WPAPMail::registerMail(
            $user->user_email,
            $user,
            $email_opt['activation_email_subject'],
            $email_opt['activation_email'],
            true
        );

        wp_send_json_success([
            'msg' => __('ثبت نام با موفقیت اجام شد. لینک فعالسازی حساب کاربری به ایمیل شما ارسال شد.', 'arvand-panel'),
            'redirect' => false
        ]);
    }

    if ($register['enable_admin_approval']) {
        wp_send_json_success([
            'msg' => __('ثبت نام با موفقیت انجام شد. لطفاً تا فعال سازی حساب شما توسط مدیر سایت، شکیبا باشید.', 'arvand-panel'),
            'redirect' => false
        ]);
    }

    wp_clear_auth_cookie();
    wp_set_current_user($user->ID);
    wp_set_auth_cookie($user->ID);
    wp_send_json_success(['msg' => __('ثبت نام با موفقیت انجام شد.', 'arvand-panel'), 'redirect' => true]);
}

function wpap_phone_format($phone_number)
{
    $phone_number = wpap_en_num($phone_number);
    $pattern = apply_filters('wpap_phone_number_pattern', "/^(?:98|\+98|0098|0)?9[0-9]{9}$/");

    if (!preg_match($pattern, $phone_number)) {
        return false;
    }

    $phone = false;

    if (preg_match('/^09[0-9]{9}$/', $phone_number))
        $phone = $phone_number;
    if (preg_match('/^989[0-9]{9}$/', $phone_number))
        $phone = '0' . substr($phone_number, 2);
    if (preg_match('/^\+989[0-9]{9}$/', $phone_number))
        $phone = '0' . substr($phone_number, 3);
    if (preg_match('/^00989[0-9]{9}$/', $phone_number))
        $phone = '0' . substr($phone_number, 4);
    if (preg_match('/^9[0-9]{9}$/', $phone_number))
        $phone = '0' . $phone_number;

    return $phone;
}