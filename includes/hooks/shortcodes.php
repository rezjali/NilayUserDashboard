<?php
defined('ABSPATH') || exit;

use Arvand\ArvandPanel\Form\WPAPFieldSettings;

add_shortcode("wpap_register_form", function () {
    $register = wpap_register_options();

    if ($register['pass_strength']) {
        wp_enqueue_script('wpap_password_strength');
    }

    wp_enqueue_script('password-strength-meter');

    ob_start();
    ?>
    <div id="wpap-register" class="wpap-auth">
        <?php
        if (!$register['enable_def_reg']) {
            wpap_print_notice(__('ثبت نام غیرفعال می باشد.', 'arvand-panel'), 'info', false);
        } elseif (!is_user_logged_in()) {
            if (!WPAPFieldSettings::get('user_login')
                || !WPAPFieldSettings::get('user_email')
                || !WPAPFieldSettings::get('user_pass')
            ) {
                wpap_print_notice(__('فرم ثبت نام غیرفعال می باشد.', 'arvand-panel'), 'error', false);
            } else {
                $styles = wpap_styles();
                $pages_opt = wpap_pages_options();
                require WPAP_TEMPLATES_PATH . 'general-styles.php';
                require WPAP_TEMPLATES_PATH . 'auth/register.php';
            }
        } else {
            wpap_print_notice(__('کاربر محترم، شما وارد حساب خود شده اید.', 'arvand-panel'), 'info', false);
        }
        ?>
    </div>
    <?php
    return ob_get_clean();
});

add_shortcode("wpap_login_form", function () {
    ob_start();
    ?>
    <div id="wpap-login" class="wpap-auth">
        <?php
        if (!is_user_logged_in()) {
            $styles = wpap_styles();
            $login = wpap_login_options();
            require WPAP_TEMPLATES_PATH . 'general-styles.php';
            require WPAP_TEMPLATES_PATH . 'auth/login.php';
        } else {
            wpap_print_notice(esc_html__('You are logged in to your account.', 'arvand-panel'), 'info', false);
        }
        ?>
    </div>
    <?php
    return ob_get_clean();
});

add_shortcode("wpap_sms_register_login", function ($attributes) {
    if (!is_admin_bar_showing()) {
        wp_enqueue_style('dashicons');
    }

    ob_start();
    ?>
    <div id="wpap-sms-register-login" class="wpap-auth">
        <?php
        if (!is_user_logged_in()):
            $login = wpap_login_options();

            if (!$login['enable_sms_register_login']) {
                wpap_print_notice(esc_html__('Login and register with sms disabled.', 'arvand-panel'), 'info', false);
            } else {
                $attributes = shortcode_atts(['redirect_url' => '', 'redirect_page_id' => -1], $attributes);
                $redirect_page_id = is_numeric($attributes['redirect_page_id']) ? (int)$attributes['redirect_page_id'] : -1;
                $register = wpap_register_options();
                $styles = wpap_styles();
                $pages_opt = wpap_pages_options();
                ?>
                <div class="wpap-form-wrap">
                    <?php
                    require WPAP_TEMPLATES_PATH . 'general-styles.php';
                    require WPAP_TEMPLATES_PATH . 'auth/sms-send.php';
                    require WPAP_TEMPLATES_PATH . 'auth/sms-register.php';
                    require WPAP_TEMPLATES_PATH . 'auth/sms-login.php';
                    ?>
                </div>
                <?php
            }
        else:
            wpap_print_notice(esc_html__('You are logged in to your account.', 'arvand-panel'), 'info', false);
        endif;
        ?>
    </div>
    <?php
    return ob_get_clean();
});

add_shortcode("wpap_lost_password_form", function () {
    ob_start();
    ?>
    <div id="wpap-lost-password" class="wpap-auth">
        <?php if (!is_user_logged_in()): ?>
            <div class="wpap-form-wrap">
                <?php
                $login = wpap_login_options();
                $styles = wpap_styles();

                require WPAP_TEMPLATES_PATH . 'general-styles.php';
                ?>

                <?php if ($login['reset_pass_method'] === 'email' || $login['reset_pass_method'] === 'both'): ?>
                    <form id="wpap-lost-password-form" action="<?php echo wp_lostpassword_url(); ?>" method="post">
                        <div class="wpap-fields">
                            <?php wp_nonce_field('lost_pass_nonce', 'lost_pass_nonce'); ?>

                            <label class="wpap-field-wrap">
                               <span class="wpap-field-label">
                                   <?php esc_attr_e('Email', 'arvand-panel'); ?>
                               </span>

                                <input type="text" name="user_login"/>
                            </label>

                            <footer>
                                <button class="wpap-btn-1" type="submit">
                                    <span class="wpap-btn-text"><?php esc_attr_e('Recovery Password', 'arvand-panel'); ?></span>
                                    <div class="wpap-loading"></div>
                                </button>
                            </footer>

                            <?php if ($login['enable_sms_register_login']): ?>
                                <div id="wpap-form-buttons">
                                    <a href="" id="wpap-sms-pass-lost-btn">
                                        <i class="ri-message-3-line"></i>
                                        <span><?php esc_html_e('Reset password by mobile', 'arvand-panel'); ?></span>
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </form>
                <?php endif; ?>

                <?php if ($login['reset_pass_method'] === 'mobile' || $login['reset_pass_method'] === 'both'): ?>
                    <form id="wpap-sms-lost-pass-form" method="post" <?php echo $login['reset_pass_method'] === 'mobile' ? 'style="display: block"' : ''; ?>>
                        <div class="wpap-fields">
                            <?php wp_nonce_field('sms_reset_pass_nonce', 'sms_reset_pass_nonce'); ?>

                            <label class="wpap-field-wrap">
                               <span class="wpap-field-label">
                                   <?php esc_attr_e('Mobile Number', 'arvand-panel'); ?>
                               </span>

                                <input type="text" name="phone"/>
                            </label>

                            <footer>
                                <button class="wpap-btn-1" type="submit" name="sms_reset_pass">
                                    <span class="wpap-btn-text"><?php esc_html_e('Send Code', 'arvand-panel'); ?></span>
                                    <div class="wpap-loading"></div>
                                </button>
                            </footer>
                        </div>
                    </form>

                    <form id="wpap-sms-reset-pass-verify-form" method="post">
                        <div class="wpap-fields">
                            <?php wp_nonce_field('sms_reset_pass_verify_nonce', 'sms_reset_pass_verify_nonce'); ?>

                            <label class="wpap-field-wrap">
                               <span class="wpap-field-label">
                                   <?php esc_html_e('Verification Code', 'arvand-panel'); ?>
                               </span>

                                <input type="text" name="verification_code"/>
                            </label>

                            <label class="wpap-field-wrap">
                               <span class="wpap-field-label">
                                   <?php esc_html_e('New Password', 'arvand-panel'); ?>
                               </span>

                                <input type="password" name="new_password"/>

                                <span class="wpap-input-info">
                                    <?php
                                    $pass_opt = WPAPFieldSettings::password();

                                    echo sprintf(
                                        esc_html__('At least %d letters. For more security, use a combination of letters and numbers.', 'arvand-panel'),
                                        esc_html($pass_opt['rules']['min_length'])
                                    );
                                    ?>
                                </span>
                            </label>

                           <footer>
                               <button class="wpap-btn-1" type="submit" name="sms_reset_pass_verify">
                                   <span class="wpap-btn-text"><?php esc_attr_e('Reset Password', 'arvand-panel'); ?></span>
                                   <div class="wpap-loading"></div>
                               </button>
                           </footer>
                        </div>
                    </form>
                <?php endif; ?>

                <?php
                $page = 'lost-pass';
                require WPAP_TEMPLATES_PATH . 'auth/form-links.php';
                ?>
            </div>
        <?php else: ?>
            <?php wpap_print_notice(__('You are logged in to your account.', 'arvand-panel'), 'info', false); ?>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
});

add_shortcode("wpap_reset_password_form", function () {
    ob_start();
    ?>
    <div id="wpap-reset-password" class="wpap-auth">
        <?php
        if (!is_user_logged_in()):
            if (!empty($_REQUEST['login']) && !empty($_REQUEST['key'])):
                $styles = wpap_styles();
                require WPAP_TEMPLATES_PATH . 'general-styles.php';
                ?>
                <form id="wpap-reset-password-form" action="<?php echo site_url('wp-login.php?action=resetpass'); ?>" method="post">
                   <div class="wpap-fields">
                       <?php
                       wpap_print_notice(__('Enter your new password.', 'arvand-panel'), 'success');

                       wp_nonce_field('reset_pass_nonce', 'reset_pass_nonce');
                       ?>

                       <input type="hidden" name="rp_login" value="<?php esc_attr_e($_REQUEST['login']); ?>" autocomplete="off"/>
                       <input type="hidden" name="rp_key" value="<?php esc_attr_e($_REQUEST['key']); ?>"/>

                       <label class="wpap-field-wrap">
                           <span class="wpap-field-label">
                               <?php esc_html_e('New Password', 'arvand-panel'); ?>
                           </span>

                           <input type="password" name="pass1" autocomplete="off"/>

                           <span class="wpap-input-info">
                                <?php
                                $pass_settings = WPAPFieldSettings::get('user_pass');
                                printf(esc_html__('At least %d letters. For more security, use a combination of letters and numbers.', 'arvand-panel'), $pass_settings['rules']['min_length']);
                                ?>
                            </span>
                       </label>

                       <label class="wpap-field-wrap">
                            <span class="wpap-field-label">
                               <?php esc_html_e('Confirm Password', 'arvand-panel'); ?>
                           </span>

                           <input type="password" name="pass2" autocomplete="off"/>
                       </label>

                       <footer>
                           <button class="wpap-btn-1" type="submit">
                               <span class="wpap-btn-text"><?php esc_attr_e('Reset Password', 'arvand-panel'); ?></span>
                               <div class="wpap-loading"></div>
                           </button>
                       </footer>
                   </div>
                </form>

                <?php
                $pages_opt = wpap_pages_options();
                $login_page_url = get_permalink($pages_opt['login_page_id']);
                $register_page_url = get_permalink($pages_opt['register_page_id']);
                ?>

                <div id="wpap-form-links">
                    <a href="<?php echo $login_page_url; ?>">
                        <?php esc_html_e('Login', 'arvand-panel'); ?>
                    </a>

                    <a href="<?php echo $register_page_url; ?>">
                        <?php esc_html_e('Register', 'arvand-panel'); ?>
                    </a>
                </div>
            <?php else:
                wpap_print_notice(__('Invalid Request.', 'arvand-panel'), 'error');
            endif;
        else:
            wpap_print_notice(__('You are logged in to your account.', 'arvand-panel'), 'info', false);
        endif; ?>
    </div>
    <?php
    return ob_get_clean();
});

add_shortcode("wpap_user_panel", function () {
    wp_enqueue_script('wpap_panel_form_handler');

    ob_start();

    if (is_user_logged_in()):
        wpap_template('panel/main');
    else: ?>
        <div id="wpap-non-logged-in">
            <?php echo do_shortcode('[wpap_login_form]'); ?>
        </div>
    <?php endif;

    return ob_get_clean();
});

add_shortcode("wpap_profile_menu", function () {
    ob_start();
    include WPAP_TEMPLATES_PATH . 'account-menu.php';
    return ob_get_clean();
});

add_shortcode("wpap_bookmark_btn", function () {
    if (!wpap_general_options()['add_to_list']) {
        return null;
    }

    $product_id = get_the_ID();
    $list = get_user_meta(get_current_user_id(), 'wpap_bookmarked', true);
    $list = is_array($list) ? $list : [];
    $is_added = in_array($product_id, $list) ? 'added' : '';
    ob_start();
    ?>
    <button class="wpap-add-to-list-button <?php echo esc_attr($is_added); ?>"
            data-product-id="<?php echo esc_attr($product_id); ?>"
            title="<?php esc_attr_e('افزودن به لیست علاقه مندی', 'arvand-panel'); ?>">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
            <path d="M16.5 3C19.5376 3 22 5.5 22 9C22 16 14.5 20 12 21.5C9.5 20 2 16 2 9C2 5.5 4.5 3 7.5 3C9.35997 3 11 4 12 5C13 4 14.64 3 16.5 3Z"></path>
        </svg>
    </button>
    <?php
    return ob_get_clean();
});