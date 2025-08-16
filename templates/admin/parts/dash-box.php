<?php
defined('ABSPATH') || exit;

ob_start();
?>

<div class="wpap-preview-box">
    <div>
        <i class="wpap-box-icon ri-star-line" style="background-color: #0078ff; color: #fff"></i>
        <h3><?php esc_html_e('Box title', 'arvand-panel'); ?></h3>
        <span><?php esc_html_e('Box content', 'arvand-panel'); ?></span>
        <a class="wpap-delete-box" href="#"><i class="ri-delete-bin-7-line"></i></a>
    </div>

    <div class="wpap-popup-form-wrap">
        <div class="wpap-popup-form">
                <input type="hidden" name="boxes[0][name]" value="custom_box"/>

                <p>
                    <label><?php esc_html_e('Box background color', 'arvand-panel'); ?></label>
                    <input class="wpap-input-box-bg-color" type="color" name="boxes[0][bg_color]" value="#ffffff"/>
                </p>

                <p>
                    <label><?php esc_html_e('Box icon', 'arvand-panel'); ?></label>
                    <input class="wpap-input-box-icon" type="text" name="boxes[0][icon]" value=""/>

                    <span style="margin-top: 5px; display: block;">
                    <?php
                    printf(
                        __('برای قرار دادن آیکن می توانید از این %s استفاده کنید.', 'arvand-panel'),
                        sprintf('<a href="https://remixicon.com" target="_blank">%s</a>', esc_html__('وبسایت', 'arvand-panel'))
                    );
                    ?>
                </span>
                </p>

                <p>
                    <label><?php esc_html_e('Box icon color', 'arvand-panel'); ?></label>
                    <input class="wpap-input-box-icon-color" type="color" name="boxes[0][icon_color]" value="#ffffff"/>
                </p>

                <p>
                    <label><?php esc_html_e('Box icon background color', 'arvand-panel'); ?></label>
                    <input class="wpap-input-box-icon-bg-color" type="color" name="boxes[0][icon_bg]" value="#0078ff"/>
                </p>

                <p>
                    <label><?php esc_html_e('Box title', 'arvand-panel'); ?></label>
                    <input class="wpap-input-box-title" type="text" name="boxes[0][title]"
                           value="<?php _e('Box title', 'arvand-panel'); ?>"/>
                </p>

                <p>
                    <label><?php esc_html_e('Box link', 'arvand-panel'); ?></label>
                    <input type="text" name="boxes[0][link]" placeholder="<?php esc_attr_e('eg: google.con', 'arvand-panel'); ?>"
                           style="direction: ltr"/>
                </p>

                <p>
                    <label><?php esc_html_e('Content type', 'arvand-panel'); ?></label>

                    <select name="boxes[0][content_type]">
                        <option value="show"><?php esc_html_e('text', 'arvand-panel'); ?></option>
                        <option value="hide"><?php esc_html_e('shortcode', 'arvand-panel'); ?></option>
                    </select>
                </p>

                <p>
                    <label><?php esc_html_e('Box content (shortcode or text)', 'arvand-panel'); ?></label>
                    <textarea class="wpap-input-box-content"
                              name="boxes[0][content]"
                              rows="5"><?php esc_attr_e('Box content', 'arvand-panel'); ?></textarea>
                </p>

                <p>
                    <label><?php esc_html_e('Display', 'arvand-panel'); ?></label>

                    <select name="boxes[0][display]">
                        <option value="show"><?php esc_html_e('Show', 'arvand-panel'); ?></option>
                        <option value="hide"><?php esc_html_e('Hide', 'arvand-panel'); ?></option>
                    </select>
                </p>

                <button type="button" class="wpap-box-setting-done wpap-close-popup wpap-btn-1">
                    <?php esc_html_e('Done', 'arvand-panel'); ?>
                </button>
            </div>
    </div>
</div>

<?php
return ob_get_clean();