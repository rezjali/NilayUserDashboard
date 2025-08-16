<?php
defined('ABSPATH') || exit;

global $current_user;

$styles = wpap_styles();
$logo_width = $styles['panel_logo_width'] > 0 ? "width: {$styles['panel_logo_width']}px;" : 'width: auto;';
$logo_height = $styles['panel_logo_height'] > 0 ? "height: {$styles['panel_logo_height']}px;" : 'height: auto;';
$logo_align = in_array($styles['panel_logo_align'], ['right', 'center', 'left'])
    ? "justify-content: {$styles['panel_logo_align']};"
    : 'justify-content: right;';

$shortcode_opt = wpap_shortcode_options();
$wallet_opt = wpap_wallet_options();
?>

<aside id="wpap-sidebar">
    <header>
        <a id="wpap-hide-sidebar" href="" title="<?php esc_html_e('Hide Menu', 'arvand-panel'); ?>">
            <i class="ri-close-large-line"></i>
        </a>
    </header>

    <?php if (wpap_panel_options()['display_top_sidebar']): ?>
        <div id="wpap-top-sidebar">
            <?php do_action('wpap_top_sidebar_top'); ?>

            <?php if (!empty($panel_opt['logo_url'])): ?>
                <a style="<?php echo esc_attr($logo_align); ?>" href="<?php echo esc_url(home_url()); ?>" id="wpap-logo">
                    <img style="<?php echo esc_attr($logo_width . $logo_height); ?>"
                         src="<?php echo esc_attr($panel_opt['logo_url']); ?>"
                         alt="logo"
                    />

                    <?php if (!empty($shortcode_opt['after_logo'])): ?>
                        <div class="wpap-after-logo-shortcode">
                            <?php echo wp_kses_post(do_shortcode($shortcode_opt['after_logo'])); ?>
                        </div>
                    <?php endif; ?>
                </a>
            <?php endif; ?>

            <?php do_action('wpap_top_sidebar_bottom'); ?>
        </div>
    <?php endif; ?>

    <a id="wpap-sidebar-user-info" href="<?php echo wpap_is_valid_section('user_edit') ? esc_url(wpap_get_page_url_by_name('user_edit')) : '#'; ?>">
        <div>
            <strong><?php echo esc_html($current_user->display_name); ?></strong>
        </div>

        <i class="ri-user-settings-line"></i>
    </a>

    <?php if (class_exists('WooCommerce') && $wallet_opt['enabled']): ?>
        <a href="<?php echo wpap_is_valid_section('wallet_topup') ? esc_url(wpap_get_page_url_by_name('wallet_topup')) : '#'; ?>" id="wpap-sidebar-wallet-info">
            <span><?php esc_html_e('کیف پول', 'arvand-panel'); ?></span>
            <strong><?php echo wc_price(wpap_wallet_get_balance($current_user->ID)); ?></strong>
        </a>
    <?php endif; ?>

    <?php do_action('wpap_before_sidebar_menus'); ?>

    <?php if (!empty($shortcode_opt['before_sidebar_nav'])): ?>
        <div class="wpap-before-sidebar-nav-shortcode">
            <?php echo do_shortcode(wp_kses($shortcode_opt['before_sidebar_nav'], 'post')); ?>
        </div>
    <?php endif; ?>

    <nav id="wpap-nav">
        <?php echo wpap_panel_menus_html(wpap_get_global_data('menus')); ?>
    </nav>

    <?php if (!empty($shortcode_opt['after_sidebar_nav'])): ?>
        <div class="wpap-after-sidebar-nav-shortcode">
            <?php echo wp_kses_post(do_shortcode($shortcode_opt['after_sidebar_nav'])); ?>
        </div>
    <?php endif; ?>

    <?php do_action('wpap_after_sidebar_menus'); ?>

    <div id="wpap-sidebar-bottom-btn-wrapper">
        <a id="wpap-home-link" href="<?php echo esc_url(site_url()); ?>"
           title="<?php esc_attr_e('صفحه اصلی', 'arvand-panel'); ?>">
            <i class="ri-home-9-line"></i>
        </a>

        <a id="wpap-theme-toggle"
           href=""
           data-theme="<?php echo esc_attr($theme); ?>"
           title="<?php esc_attr_e('تغییر به حالت تاریک/روشن', 'arvand-panel'); ?>">
            <i class="<?php echo 'dark' === $theme ? 'ri-sun-fill' : 'ri-contrast-2-line'; ?>"></i>
        </a>
    </div>
</aside>