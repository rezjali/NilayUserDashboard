<?php

namespace Arvand\ArvandPanel\Form\Fields;

defined('ABSPATH') || exit;

use Arvand\ArvandPanel\Form\WPAPField;
use Arvand\ArvandPanel\Form\WPAPFieldHtml;

class wpap_field_radio extends WPAPField
{
    public $type = 'user_meta';

    public function __construct()
    {
        parent::__construct(self::defaultSettings());
    }

    public static function defaultSettings(): array
    {
        return [
            'field_name' => 'radio',
            'type' => 'radio',
            'label' => __('Radio button', 'arvand-panel'),
            'meta_key' => '',
            'attrs' => [
                'name' => 'radio',
                'type' => 'radio',
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
        return ['ri-radio-button-line', __('Radio Button', 'arvand-panel')];
    }

    public function adminOutput(array $settings, $value = null)
    {
        ob_start();
        ?>
        <fieldset>
            <?php foreach ($settings['options'] as $option): ?>
                <label>
                    <input type="radio" name="<?php echo esc_attr($settings['attrs']['name']); ?>"
                           value="<?php echo esc_attr($option); ?>" <?php checked($value, $option); ?>>
                    <span><?php echo esc_html($option); ?></span>
                </label>
                <br/>
            <?php endforeach; ?>
        </fieldset>
        <?php
        $html = new WPAPFieldHtml($settings, $value);
        $html->wrap(ob_get_clean());
    }

    public function output(array $settings, $value = null): void
    {
        ob_start();
        ?>
        <div class="wpap-options-sets">
            <span class="wpap-radio-button-wrap wpap-options-wrap">
                <?php
                $i = 0;

                foreach ($settings['options'] as $option): $i++;
                    $id = strtolower(str_replace(' ', '_', $settings['attrs']['name']) . '_' . $i); ?>

                    <label for="wpap-radio-<?php echo esc_attr($id); ?>">
                        <input id="wpap-radio-<?php echo esc_attr($id); ?>" type="radio"
                               name="<?php echo esc_attr($settings['attrs']['name']); ?>"
                               value="<?php echo esc_attr($option); ?>" <?php checked($value, $option); ?>>
                        <span class="wpap-radio-button"></span>

                        <span><?php echo esc_html($option); ?></span>
                    </label>

                    <?php echo is_admin() ? '<br />' : '';
                endforeach; ?>
            </span>
        </div>
        <?php
        $html = new WPAPFieldHtml($settings, $value);
        $html->wrap(ob_get_clean());
    }

    public function adminValidation($field_settings): bool
    {
        $value = $_POST[$field_settings['attrs']['name']] ?? '';
        return (!empty($value) && in_array($value, $field_settings['options']));
    }

    public function validation($field_settings): ?string
    {
        $attr_name = $field_settings['attrs']['name'];

        if (!empty($_POST[$attr_name]) && !in_array($_POST[$attr_name], $field_settings['options'])) {
            return sprintf(__('%s is required.', 'arvand-panel'), esc_html($field_settings['label']));
        }

        return null;
    }
}