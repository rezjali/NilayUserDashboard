<?php

namespace Arvand\ArvandPanel;

defined('ABSPATH') || exit;

use Arvand\ArvandPanel\DB\WPAPMenuDB;

class WPAPMenu
{
    public static function adminMenuSettings(int $parent_id = 0): string
    {
        ob_start();

        $db = new WPAPMenuDB;
        $children = $db->getMenus($parent_id, true);

        foreach ($children as $menu):
            ?>
            <div class="wpap-menu-item" data-title="<?php echo $menu->menu_title; ?>">
                <input style="display: none;" type="text" name="menu_id[]" value="<?php esc_attr_e($menu->menu_id); ?>" />

                <div class="wpap-menu" data-id="<?php esc_attr_e($menu->menu_id); ?>">
                    <h3>
                        <?php
                        $icon = self::icon($menu);

                        $icon_image = wp_get_attachment_image($icon['image_id'], 'thumbnail', false, [
                            'style' => 'width: 20px; height: 20px; object-fit: cover; vertical-align: middle'
                        ]);

                        echo $icon_image ?: sprintf('<i style="color: %s" class="%s"></i>', esc_html($icon['color']), esc_attr($icon['classes']));
                        ?>

                        <span><?php esc_html_e($menu->menu_title); ?></span>
                    </h3>

                    <div class="wpap-menu-actions">
                        <a href="<?php echo esc_url(add_query_arg(['section' => 'edit', 'menu' => $menu->menu_id])); ?>">
                            <i class='wpap-show-menu-settings ri-edit-2-line'
                               title="<?php esc_attr_e('ویرایش', 'arvand-panel'); ?>"></i>
                        </a>

                        <?php if ('parent' === $menu->menu_type): ?>
                            <i class='wpap-show-child-menus ri-arrow-down-s-line'
                               title="<?php esc_attr_e('نمایش زیرمنوها', 'arvand-panel'); ?>"></i>
                        <?php endif; ?>

                        <i class="wpap-hide-menu <?php echo 'hide' === $menu->menu_display ? 'ri-eye-off-line' : 'ri-eye-line'; ?>"
                           title="<?php esc_attr_e('وضعیت نمایش', 'arvand-panel'); ?>"
                           data-id="<?php echo esc_attr($menu->menu_id); ?>"
                           data-nonce="<?php echo wp_create_nonce('hide_menu'); ?>">
                        </i>

                        <?php if ('default' !== $menu->menu_type): ?>
                            <i class="wpap-remove-menu ri-delete-bin-7-line"
                               data-nonce="<?php echo wp_create_nonce('delete_menu'); ?>"
                               data-id="<?php echo esc_attr($menu->menu_id); ?>"
                            ></i>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if ($menu->menu_type !== 'default' && 0 == $menu->menu_parent): ?>
                    <div class="wpap-child-menus">
                        <?php echo self::adminMenuSettings($menu->menu_id); ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach;

        return ob_get_clean();
    }

    public static function icon(object $menu): array
    {
        $icon = json_decode($menu->menu_icon);
        $is_json = json_last_error() === JSON_ERROR_NONE;

        return [
            'classes' => $is_json ? $icon->classes : $menu->menu_icon,
            'image_id' => $is_json ? $icon->image_id : 0,
            'color_type' => $is_json ? $icon->color_type : 0,
            'color' => $is_json && 1 == $icon->color_type ? $icon->color : '',
        ];
    }
}