<?php

namespace Arvand\ArvandPanel\Form\Fields;

defined('ABSPATH') || exit;

use Arvand\ArvandPanel\Form\WPAPField;
use Arvand\ArvandPanel\Form\WPAPFieldHtml;

class wpap_field_checkbox extends WPAPField
{
    public $type = 'user_meta';

    public function __construct()
    {
        parent::__construct(self::defaultSettings());
    }

    public static function defaultSettings(): array
    {
        return [
            'field_name' => 'checkbox',
            'type' => 'checkbox',
            'label' => __('Checkbox', 'arvand-panel'),
            'meta_key' => '',
            'attrs' => [
                'name' => 'checkbox',
                'type' => 'checkbox',
            ],
            'options' => [__('گزینه', 'arvand-panel')],
            'rules' => [
                'required' => false,
            ],
            'description' => '',
            'display' => 'both'
        ];
    }

    public static function adminButton(): array
    {
        return ['ri-checkbox-line', __('Checkbox', 'arvand-panel')];
    }

    public function adminOutput(array $settings, $value = null)
    {
        $options = $settings['options'];
        $name = esc_attr($settings['attrs']['name']);
        ob_start();
        ?>
        <fieldset>
            <?php for ($i = 0; $i < count($options); $i++):
                $checked = checked($value && in_array($options[$i], (array)unserialize($value)), true, false); ?>

                <label>
                    <input type="checkbox" name="<?php echo $name; ?>[]" value="<?php echo esc_attr($options[$i]); ?>" <?php echo $checked; ?>>
                    <span><?php echo esc_html($options[$i]); ?></span>
                </label><br/>
            <?php endfor; ?>
        </fieldset>
        <?php
        $html = new WPAPFieldHtml($settings, $value);
        $html->wrap(ob_get_clean());
    }

    public function output(array $settings, $value = null): void
    {
        $html = new WPAPFieldHtml($settings, $value);
        $options = $settings['options'];
        $name = esc_attr($settings['attrs']['name']);
        ob_start();
        ?>
        <div class="wpap-options-sets">
            <div class="wpap-options-wrap">
                <?php for ($i = 0; $i < count($options); $i++):
                    $checked = checked($value && in_array($options[$i], unserialize($value)), true, false); ?>

                    <span class="wpap-checkbox-wrap">
                        <label for="wpap-checkbox-<?php echo $name . "_$i"; ?>">
                            <input id="wpap-checkbox-<?php echo $name . "_$i"; ?>" type="checkbox" name="<?php echo $name; ?>[]" value="<?php echo esc_attr($options[$i]); ?>" <?php echo $checked; ?>>
                            <span class="wpap-checkbox"></span>
                            <span><?php echo esc_html($options[$i]); ?></span>
                        </label>
                    </span>
                <?php endfor; ?>
            </div>
        </div>
        <?php
        $html->wrap(ob_get_clean());
    }

    public function adminValidation($field_settings = null): bool
    {
        $attr_name = $field_settings['attrs']['name'];

        if (isset($_POST[$attr_name]) && empty(array_intersect($_POST[$attr_name], $field_settings['options']))) {
            return false;
        }

        return true;
    }

    public function validation($field_settings): ?string
    {
        $attr_name = $field_settings['attrs']['name'];
        $display_name = $field_settings['label'];

        if (isset($_POST[$attr_name]) && empty(array_intersect($_POST[$attr_name], $field_settings['options']))) {
            return sprintf(__('%s field is not selected correctly.', 'arvand-panel'), $display_name);
        }

        return null;
    }

    public function value($field_settings = null): string
    {
        return maybe_serialize(array_map('sanitize_text_field', $_POST[$field_settings['attrs']['name']] ?? []));
    }
}