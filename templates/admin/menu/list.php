<?php
defined('ABSPATH') || exit;

$sort_response = Arvand\ArvandPanel\Admin\Handlers\WPAPMenuHandler::sortMenus();
$rest_response = Arvand\ArvandPanel\Admin\Handlers\WPAPMenuHandler::resetMenus();
?>

<form id="wpap-menu-list" class="wpap-container" method="post">
    <header class="wpap-menu-header">
        <a href="<?php echo add_query_arg('section', 'new'); ?>">
            <i class="ri-add-line"></i>
            <?php esc_html_e('منوی جدید', 'arvand-panel'); ?>
        </a>

        <button type="submit" name="sort_menus">
            <i class="ri-save-line"></i>
            <?php esc_html_e('ذخیره مرتب سازی', 'arvand-panel'); ?>
        </button>

        <button type="submit"
                name="sort_menus"
                form="masoud"
                onclick="return confirm('<?php esc_attr_e('آیا از بازگردانی منوها به تنظیمات پیشفرض مطمئنید؟', 'arvand-panel'); ?>')">
            <i class="ri-text-wrap"></i>
            <?php esc_html_e('تنظیمات پیشفرض', 'arvand-panel'); ?>
        </button>
    </header>

    <div>
        <?php
        if ($sort_response) {
            wpap_admin_notice($sort_response['msg'], $sort_response['ok'] ? 'success' : 'error');
        }

        if ($rest_response) {
            wpap_admin_notice($rest_response['msg'], $rest_response['ok'] ? 'success' : 'error');
        }

        wp_nonce_field('sort_menus', 'sort_menus_nonce');
        ?>

        <div id="wpap-menu-items">
            <?php echo Arvand\ArvandPanel\WPAPMenu::adminMenuSettings(); ?>
        </div>
    </div>
</form>

<form id="masoud" method="post">
    <input type="hidden" name="reset_menus">
    <?php wp_nonce_field('reset_menus', 'reset_menus_nonce'); ?>
</form>

<script>
    jQuery(document).ready(function ($) {
        $("#wpap-menu-items, .wpap-child-menus").sortable({
            axis: "y",
            placeholder: "wpap-placeholder"
        });

        $(document).on("click", '.wpap-show-child-menus', function () {
            $(this).toggleClass('wpap-menu-opened')
                .parents('.wpap-menu')
                .siblings(".wpap-child-menus")
                .slideToggle(200);
        });

        $(document).on('click', '.wpap-hide-menu', function () {
            let hideBTN = $(this);
            let nonce = hideBTN.data('nonce');
            let menuId = hideBTN.data('id');

            $.ajax({
                type: 'post',
                url: '<?php echo esc_url(admin_url('admin-ajax.php')); ?>',
                data: {
                    action: 'wpap_hide_menu',
                    nonce: nonce,
                    id: menuId,
                },
                beforeSend: function () {
                    hideBTN.css('opacity', '0.4');
                },
                success: function (response) {
                    console.log(response)
                    if (response.success) {
                        if ('hide' === response.data) {
                            hideBTN.addClass('ri-eye-off-line').removeClass('ri-eye-line');
                        } else {
                            hideBTN.addClass('ri-eye-line').removeClass('ri-eye-off-line');
                        }
                    }
                },
                error: function (error) {},
                complete: function () {
                    hideBTN.css('opacity', '1');
                }
            });
        });

        $(document).on('click', '.wpap-remove-menu', function () {
            if (!confirm('<?php esc_html_e('آیا از حذف این منو مطمئن هستید؟', 'arvand-panel'); ?>')) {
                return;
            }

            const removeBTN = $(this);
            const removeBTNParent = removeBTN.parents('.wpap-menu-item');
            const childrenWrap = removeBTN.parents('.wpap-menu-item').siblings('.wpap-child-menus');
            let nonce = removeBTN.data('nonce');
            let menuId = removeBTN.data('id');

            removeBTNParent.css('opacity', '0.4');

            $.ajax({
                type: 'post',
                url: '<?php echo esc_url(admin_url('admin-ajax.php')); ?>',
                data: {
                    action: 'wpap_delete_menu',
                    nonce: nonce,
                    id: menuId,
                },
                success: function (response) {
                    if (response.success) {
                        removeBTNParent.hide(0);
                        childrenWrap.fadeIn(200).css({ paddingRight: '0', transition: 'padding 200ms' });
                    } else {
                        removeBTNParent.css('opacity', '1');
                    }
                },
                error: function () {
                    removeBTNParent.css('opacity', '1');
                },
            });
        });
    });
</script>