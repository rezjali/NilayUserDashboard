<?php
defined('ABSPATH') || exit;

if (!class_exists('Woocommerce') || !in_array('coupons', $dash['dash_widgets'])) {
    return;
}

global $current_user;

$coupons = get_posts([
    'post_type' => 'shop_coupon',
    'limit' => -1,
    'orderby' => 'date',
    'order' => 'DESC',
]);

if (empty($coupons)) {
    return;
}
?>

<div id="wpap-dash-coupons" class="wpap-mb-30 wpap-grid wpap-col-lg-2 wpap-gap-20">
    <?php foreach ($coupons as $coupon): ?>
        <?php
        $coupon_obj = new WC_Coupon($coupon->ID);

        // Coupon exists and is published
        if (!$coupon || !$coupon_obj->get_id() || get_post_status($coupon_obj->get_id()) !== 'publish') {
            continue;
        }

        // Not expired
        if ($coupon_obj->get_date_expires() && $coupon_obj->get_date_expires()->getTimestamp() < time()) {
            continue;
        }

        // Usage limit not reached
        if ($coupon_obj->get_usage_limit() && $coupon_obj->get_usage_count() >= $coupon_obj->get_usage_limit()) {
            continue;
        }

        // Usage limit per user
        if ($coupon_obj->get_usage_limit_per_user()) {
            $used_by = $coupon_obj->get_used_by();
            $user_email = $current_user->user_email;

            $user_count = count(array_filter($used_by, function ($email) use ($user_email) {
                return $email === $user_email;
            }));

            if ($user_count >= $coupon_obj->get_usage_limit_per_user()) {
                continue;
            }
        }

        // Email restrictions (if coupon is only for specific emails)
        $allowed_emails = $coupon_obj->get_email_restrictions();
        if (!empty($allowed_emails) && !in_array($current_user->user_email, $allowed_emails)) {
            continue;
        }
        ?>

        <div class="wpap-dash-coupon">
            <div>
                <img src="<?php echo esc_url(WPAP_ASSETS_URL . 'front/images/discount.svg'); ?>" alt="coupon"/>

                <p class="wpap-dash-coupon-desc">
                    <?php echo esc_html($coupon_obj->get_description() ?: __('کد تخفیف:', 'arvand-panel')); ?>
                </p>
            </div>

            <strong class="wpap-dash-coupon-name"
                    data-wpap-dash-coupon-code="<?php echo esc_attr($coupon_obj->get_code()); ?>">
                <span><?php echo esc_html($coupon_obj->get_code()); ?></span>
                <i class="bi bi-copy"></i>
                <i style="display: none;" class="bi bi-check2-all"></i>
            </strong>
        </div>
    <?php endforeach; ?>
</div>

<script>
    jQuery(document).ready(function ($) {
        $(".wpap-dash-coupon-name").click(function () {
            var couponName = $(this);

            var textToCopy = couponName.data('wpap-dash-coupon-code');
            if (!textToCopy) {
                return;
            }

            navigator.clipboard.writeText(textToCopy)
                .then(function () {
                    couponName.find('i.bi-copy').hide();
                    couponName.find('i.bi-check2-all').show();

                    setTimeout(function () {
                        couponName.find('i.bi-copy').show();
                        couponName.find('i.bi-check2-all').hide();
                    }, 2000)
                })
                .catch(function (err) {
                    console.error("Failed to copy text: ", err);
                });
        });
    });
</script>
