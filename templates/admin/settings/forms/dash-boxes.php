<?php
defined('ABSPATH') || exit;

if (isset($_POST['wpap_reset_dash_boxes'])
    && wp_verify_nonce(wp_unslash($_POST['wpap_reset_dash_boxes']), 'reset_dash_boxes')
) {
    update_option('wpap_dash_box', require(WPAP_INC_PATH . 'default-dash-boxes.php'));
}

$dash_box_html = require(WPAP_ADMIN_TEMPLATES_PATH . 'parts/dash-box.php');
$opt_dash_box = wpap_dash_box_options();
?>

<form id="wpap-dash-boxes-section" class="wpap-settings-section wpap-form" method="post">
    <?php wp_nonce_field('dash_box_nonce', 'dash_box_nonce'); ?>
    <input type="hidden" name="form" value="wpap_dash_boxes"/>

    <button id="wpap-add-box" class="wpap-btn-2" type="button">
        <i class="ri-add-line"></i>
        <?php esc_html_e('Add new box', 'arvand-panel'); ?>
    </button>

    <div id="wpap-box-wrap">
        <?php if ($opt_dash_box): ?>
            <?php foreach ($opt_dash_box as $index => $box): ?>
                <div class="wpap-preview-box">
                    <div>
                        <i class="wpap-box-icon <?php echo esc_html($box['icon']); ?>"
                           style="<?php echo 'background-color:' . esc_attr($box['icon_bg']) . ';color:' . esc_attr($box['icon_color']); ?>"></i>

                        <h3><?php echo esc_html($box['title']); ?></h3>
                        <span><?php echo esc_html($box['content']); ?></span>

                        <?php if ($box['box_type'] === 'custom'): ?>
                            <i class="wpap-delete-box ri-delete-bin-7-line"></i>
                        <?php endif; ?>
                    </div>

                    <div class="wpap-popup-form-wrap">
                        <div class="wpap-popup-form">
                            <input type="hidden" name="boxes[<?php echo $index; ?>][name]" value="<?php echo esc_attr($box['name']); ?>"/>

                            <p>
                                <label><?php esc_html_e('Box icon', 'arvand-panel'); ?></label>
                                <input class="wpap-input-box-icon" type="text" name="boxes[<?php echo $index; ?>][icon]"
                                       value="<?php echo esc_attr($box['icon']); ?>"/>

                                <span style="margin-top: 5px; display: block;">
                                    <?php
                                    printf(
                                        __('برای قرار دادن آیکن می توانید از این %s استفاده کنید.', 'arvand-panel'),
                                        sprintf(
                                            '<a href="https://icons.getbootstrap.com" target="_blank">%s</a>',
                                            esc_html__('وبسایت', 'arvand-panel')
                                        )
                                    );
                                    ?>
                                </span>
                            </p>

                            <p>
                                <label><?php esc_html_e('Box icon color', 'arvand-panel'); ?></label>
                                <input class="wpap-input-box-icon-color"
                                       type="color"
                                       name="boxes[<?php echo $index; ?>][icon_color]"
                                       value="<?php echo esc_attr($box['icon_color']); ?>"/>
                            </p>

                            <p>
                                <label><?php esc_html_e('Box icon background color', 'arvand-panel'); ?></label>
                                <input class="wpap-input-box-icon-bg-color"
                                       type="color"
                                       name="boxes[<?php echo $index; ?>][icon_bg]"
                                       value="<?php echo esc_attr($box['icon_bg']); ?>"/>
                            </p>

                            <p>
                                <label><?php esc_html_e('Box title', 'arvand-panel'); ?></label>
                                <input class="wpap-input-box-title"
                                       type="text"
                                       name="boxes[<?php echo $index; ?>][title]"
                                       value="<?php echo esc_attr($box['title']); ?>"/>
                            </p>

                            <?php if ($box['box_type'] !== 'default'): ?>
                                <p>
                                    <label><?php esc_html_e('Content type', 'arvand-panel'); ?></label>

                                    <select name="boxes[<?php echo $index; ?>][content_type]">
                                        <option value="text" <?php selected($box['content_type'] === 'text'); ?>>
                                            <?php esc_html_e('Text', 'arvand-panel'); ?>
                                        </option>

                                        <option value="shortcode" <?php selected($box['content_type'] === 'shortcode'); ?>>
                                            <?php esc_html_e('Shortcode', 'arvand-panel'); ?>
                                        </option>
                                    </select>
                                </p>
                            <?php endif; ?>

                            <?php if ($box['box_type'] !== 'default'): ?>
                                <p>
                                    <label><?php esc_html_e('Box content (shortcode or text)', 'arvand-panel'); ?></label>
                                    <textarea class="wpap-input-box-content"
                                              name="boxes[<?php echo $index; ?>][content]"
                                              rows="5"><?php echo esc_html($box['content']); ?></textarea>
                                </p>
                            <?php endif; ?>

                            <?php if ($box['box_type'] !== 'default'): ?>
                                <p>
                                    <label><?php esc_html_e('Box link', 'arvand-panel'); ?></label>
                                    <input type="text"
                                           name="boxes[<?php echo $index; ?>][link]"
                                           placeholder="<?php esc_attr_e('eg: google.con', 'arvand-panel'); ?>"
                                           value="<?php echo esc_attr($box['link']); ?>" style="direction: ltr"/>
                                </p>
                            <?php endif; ?>

                            <p>
                                <label><?php esc_html_e('Box display', 'arvand-panel'); ?></label>

                                <select name="boxes[<?php echo $index; ?>][display]">
                                    <option value="show" <?php selected($box['display'] === 'show'); ?>>
                                        <?php esc_html_e('Show', 'arvand-panel'); ?>
                                    </option>

                                    <option value="hide" <?php selected($box['display'] === 'hide'); ?>>
                                        <?php esc_html_e('Hide', 'arvand-panel'); ?>
                                    </option>
                                </select>
                            </p>

                            <button type="button" class="wpap-close-popup wpap-box-setting-done wpap-btn-1">
                                <?php esc_html_e('Done', 'arvand-panel'); ?>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <footer>
        <button class="wpap-btn-2"
                form="wpap-reset-dash-boxes-form"
                onclick="return confirm('<?php esc_html_e('آیا از بازگشت به تنظیمات پیشفرض مطمئنید؟', 'arvand-panel'); ?>')">
            <?php esc_html_e('تنظیمات پیشفرض', 'arvand-panel'); ?>
        </button>

        <?php require WPAP_ADMIN_TEMPLATES_PATH . 'parts/button.php'; ?>
    </footer>
</form>

<form id="wpap-reset-dash-boxes-form" method="post">
    <input type="hidden" name="wpap_reset_dash_boxes" value="<?php echo wp_create_nonce('reset_dash_boxes'); ?>"/>
</form>

<script>
     jQuery(document).ready(function ($) {
         function wpapSortableIndexes() {
             $(document).find('.wpap-popup-form-wrap').each(function (index) {
                 $(this).find('input, select, textarea').each(function () {
                     const name = $(this).attr('name');
                     if (name) {
                         const newName = name.replace(/boxes\[\d+\]/, 'boxes[' + index + ']');
                         $(this).attr('name', newName);
                     }
                 });
             });
         }

         $(document).on('click', '#wpap-add-box', function (e) {
             e.preventDefault();
             $('#wpap-box-wrap').append(<?php echo json_encode($dash_box_html); ?>);
             wpapSortableIndexes();
         });

         $(document).on('click', '.wpap-preview-box > div', function (e) {
             e.preventDefault();
             $(this).next().fadeIn(200);
         });

         $(document).on('click', '.wpap-box-setting-done', function (e) {
             let box = $(this).parent().parent().prev();
             let icon = box.find(".wpap-box-icon");
             let inputIcon = $(this).parent().find(".wpap-input-box-icon").val();
             let inputIconBGColor = $(this).parent().find(".wpap-input-box-icon-bg-color").val();
             let inputIconColor = $(this).parent().find(".wpap-input-box-icon-color").val();
             let inputTitle = $(this).parent().find(".wpap-input-box-title").val();
             let inputContent = $(this).parent().find(".wpap-input-box-content").val();

             icon
                 .attr('class', `wpap-box-icon ${inputIcon}`)
                 .css({ backgroundColor: inputIconBGColor, color: inputIconColor });

             box.find('h3').text(inputTitle);
             box.find('span').text(inputContent);
         });

         $(document).on('click', '.wpap-delete-box', function (e) {
             e.preventDefault();
             e.stopPropagation();
             $(this).parents('.wpap-preview-box').remove();
             wpapSortableIndexes();
         });

         $('#wpap-box-wrap').sortable({
             items: '.wpap-preview-box',
             tolerance: 'pointer',
             revert: 100,
             placeholder: 'wpap-box-sortable-placeholder',
             start: function (event, ui) {
                 ui.placeholder.height(ui.item.outerHeight());
                 ui.placeholder.width(ui.item.outerWidth());
             },
             update: function () {
                 wpapSortableIndexes();
             },
         });

         wpapSortableIndexes();
     });
</script>
