<?php
defined('ABSPATH') || exit;

global $current_user;
$dash = wpap_dash_options();
?>

<div id="wpap-dashboard">
    <?php do_action('wpap_top_dashboard'); ?>

    <?php if (!empty($shortcode_opt['dash_top_shortcode'])): ?>
        <div class="wpap-dash-top-shortcode-wrap wpap-mb-40">
            <?php echo wp_kses_post(do_shortcode($shortcode_opt['dash_top_shortcode'])); ?>
        </div>
    <?php endif; ?>

    <?php wpap_template('panel/dash/coupons', compact('dash')); ?>

    <?php do_action('wpap_before_info_boxes'); ?>

    <?php if (!empty($shortcode_opt['before_dash_info_boxes'])): ?>
        <div class="wpap-before-info-boxes-shortcode wpap-mb-40">
            <?php echo wp_kses_post(do_shortcode($shortcode_opt['before_dash_info_boxes'])); ?>
        </div>
    <?php endif; ?>

    <?php wpap_template('panel/dash/info-boxes'); ?>

    <?php if (!empty($shortcode_opt['after_dash_info_boxes'])): ?>
        <div class="wpap-after-info-boxes-shortcode">
            <?php echo wp_kses_post(do_shortcode($shortcode_opt['after_dash_info_boxes'])); ?>
        </div>
    <?php endif; ?>

    <?php do_action('wpap_after_info_boxes'); ?>

    <?php if (class_exists('Woocommerce') && array_intersect(['orders', 'products'], $dash['dash_widgets'])): ?>
        <div class="wpap-mb-30 wpap-grid wpap-col-xl-2 wpap-gap-20">
            <?php
            wpap_template('panel/dash/orders', compact('dash'));
            wpap_template('panel/dash/products', compact('dash'));
            ?>
        </div>
    <?php endif; ?>

    <?php if (array_intersect(['tickets', 'notices'], $dash['dash_widgets'])): ?>
        <div class="wpap-grid wpap-col-xl-2 wpap-gap-20">
            <?php
            wpap_template('panel/dash/tickets', compact('dash'));
            wpap_template('panel/dash/notices', compact('dash'));
            ?>
        </div>
    <?php endif; ?>

    <?php do_action('wpap_bottom_dashboard'); ?>

    <?php if (!empty($shortcode_opt['dash_bottom_shortcode'])): ?>
        <div class="wpap-dash-bottom-shortcode-wrap">
            <?php echo wp_kses_post(do_shortcode($shortcode_opt['dash_bottom_shortcode'])); ?>
        </div>
    <?php endif; ?>
</div>