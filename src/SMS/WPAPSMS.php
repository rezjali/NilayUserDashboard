<?php

namespace Arvand\ArvandPanel\SMS;

defined('ABSPATH') || exit;

class WPAPSMS
{
    public static function supportedGateways(): array
    {
        return [
            'melipayamak' => __('ملی پیامک', 'arvand-panel'),
            'farapayamak' => __('فراپیامک', 'arvand-panel'),
            'sms_ir' => __('sms.ir', 'arvand-panel'),
            'farazsms' => __('فراز اس ام اس', 'arvand-panel'),
            'kavenegar' => __('کاوه نگار', 'arvand-panel'),
            'modirpayamak' => __('مدیر پیامک', 'arvand-panel'),
            'parsgreen' => __('پارس گرین', 'arvand-panel'),
            'raygansms' => __('رایگان اس ام اس', 'arvand-panel'),
            'webone_sms' => __('وب وان اس ام اس', 'arvand-panel')
        ];
    }
}