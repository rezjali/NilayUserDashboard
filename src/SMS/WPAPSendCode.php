<?php

namespace Arvand\ArvandPanel\SMS;

defined('ABSPATH') || exit;

use Exception;
use SoapClient;

class WPAPSendCode
{
    public static function send($number, $code)
    {
        $sms_opt = wpap_sms_options();

        if (in_array($sms_opt['provider'], array_keys(WPAPSMS::supportedGateways()))) {
            $provider_function = esc_html($sms_opt['provider']) . '_send_code';
        } else {
            $provider_function = 'melipayamak_send_code';
        }

        return WPAPSendCode::$provider_function($number, $code);
    }

    public static function melipayamak_send_code($number, $code)
    {
        try {
            ini_set('soap.wsdl_cache_enabled', '0');
            $sms = new SoapClient('http://api.payamak-panel.com/post/Send.asmx?wsdl', ['encoding' => 'UTF-8']);
            $opt = wpap_sms_provider_options('melipayamak');

            $data = [
                'username' => $opt['username'],
                'password' => $opt['password'],
                'text' => [$code],
                'to' => $number,
                'bodyId' => $opt['pattern_code'],
            ];

            return $sms->SendByBaseNumber($data)->SendByBaseNumberResult;
        } catch (Exception $error) {
            return $error->getMessage();
        }
    }

    public static function farapayamak_send_code($number, $code)
    {
        ini_set('soap.wsdl_cache_enabled', '0');

        try {
            $sms = new SoapClient('http://api.payamak-panel.com/post/Send.asmx?wsdl', ['encoding' => 'UTF-8']);
            $opt = wpap_sms_provider_options('farapayamak');

            $data = [
                'username' => $opt['username'],
                'password' => $opt['password'],
                'text' => [$code],
                'to' => $number,
                'bodyId' => $opt['pattern_code'],
            ];

            return $sms->SendByBaseNumber($data)->SendByBaseNumberResult;
        } catch (Exception $error) {
            return $error;
        }
    }

    public static function sms_ir_send_code($number, $code)
    {
        $opt = wpap_sms_provider_options('sms_ir');

        try {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.sms.ir/v1/send/verify',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode([
                    "parameters" => [
                        ['name' => 'code', 'value' => $code]
                    ],
                    "mobile" => $number,
                    "templateId" => $opt['template_id']
                ]),
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Accept: text/plain',
                    "x-api-key: " . $opt['api_key']
                ],
            ));

            $response = curl_exec($curl);
            curl_close($curl);
            return $response;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function farazsms_send_code($number, $code)
    {
        $opt = wpap_sms_provider_options('farazsms');
        $username = $opt['username'];
        $password = $opt['password'];
        $from = $opt['from'];
        $pattern_code = $opt['pattern_code'];
        $to = $number;
        $input_data = ['code' => $code];
        $url = 'https://ippanel.com/patterns/pattern?username=' . $username . '&password=' . urlencode($password) . "&from=$from&to=" . json_encode($to) . '&input_data=' . urlencode(json_encode($input_data)) . "&pattern_code=$pattern_code";

        try {
            $handler = curl_init($url);
            curl_setopt($handler, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($handler, CURLOPT_POSTFIELDS, $input_data);
            curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
            $res = curl_exec($handler);
            return $res[0] == 0;
        } catch (Exception $error) {
            return $error->getMessage();
        }
    }

    public static function kavenegar_send_code($number, $code)
    {
        $opt = wpap_sms_provider_options('kavenegar');
        $url = sprintf('https://api.kavenegar.com/v1/%s/verify/lookup.json/', $opt['api_key']);

        $headers = [
            'Accept: application/json',
            'Content-Type: application/x-www-form-urlencoded',
            'charset: utf-8'
        ];

        $params = [
            'receptor' => $number,
            'token' => $code,
            'template' => $opt['pattern_code'],
            'type' => 'sms'
        ];

        try {
            $handle = curl_init();
            curl_setopt($handle, CURLOPT_URL, $url);
            curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($handle, CURLOPT_POST, true);
            curl_setopt($handle, CURLOPT_POSTFIELDS, http_build_query($params));
            $response = curl_exec($handle);
            return json_decode($response);
        } catch (Exception $e) {
            return false;
        }
    }

    public static function parsgreen_send_code($number, $code)
    {
        try {
            $apiMainurl = 'http://sms.parsgreen.ir';
            $opt = wpap_sms_provider_options('parsgreen');
            $apiKey = $opt['api_key'];

            $req = [
                'Mobile' => $number,
                'SmsCode' => $code,
                'TemplateId' => 5,
                'AddName' => false,
            ];

            $url = $apiMainurl . '/Apiv2/Message/SendOtp';
            $ch = curl_init($url);
            $jsonDataEncoded = json_encode($req);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $header = ['authorization: BASIC APIKEY:' . $apiKey, 'Content-Type: application/json;charset=utf-8'];
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            $result = curl_exec($ch);
            $res = json_decode($result);
            curl_close($ch);
            return $res->R_Success;
        } catch (Exception $error) {
            return $error->getMessage();
        }
    }

    public static function modirpayamak_send_code($number, $code)
    {
        try {
            $client = new SoapClient('http://ippanel.com/class/sms/wsdlservice/server.php?wsdl');
            $opt = wpap_sms_provider_options('modirpayamak');
            $user = $opt['username'];
            $pass = $opt['password'];
            $fromNum = $opt['from'];
            $toNum = [$number];
            $pattern_code = $opt['pattern_code'];
            $input_data = ['code' => $code];
            $res = $client->sendPatternSms($fromNum, $toNum, $user, $pass, $pattern_code, $input_data);
            return is_numeric($res);
        } catch (Exception $error) {
            return $error->getMessage();
        }
    }

    public static function raygansms_send_code($number, $code)
    {
        try {
            $Url = 'http://smspanel.Trez.ir/SendMessageWithCode.ashx';
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $Url);
            curl_setopt($ch, CURLOPT_POST, 1);
            $opt = wpap_sms_provider_options('raygansms');

            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
                'Username' => $opt['username'],
                'Password' => $opt['password'],
                'Mobile' => $number,
                'Message' => str_replace(['[verification_code]', '[site_name]'], [$code, get_bloginfo('name')], $opt['text']),
            ]));

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $server_output = curl_exec($ch);
            curl_close($ch);
            return $server_output >= 2000;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public static function webone_sms_send_code($number, $code)
    {
        ini_set('soap.wsdl_cache_enabled', '0');
        $opt = wpap_sms_provider_options('webone_sms');
        $parameters['userName'] = $opt['username'];
        $parameters['password'] = $opt['password'];
        $parameters['fromNumber'] = $opt['from'];
        $parameters['toNumbers'] = [$number];
        $parameters['messageContent'] = str_replace(['[verification_code]', '[site_name]'], [$code, get_bloginfo('name')], $opt['text']);
        $parameters['isFlash'] = false;
        $recId = [0];
        $status = 0x0;
        $parameters['recId'] = &$recId;
        $parameters['status'] = &$status;

        try {
            $sms_client = new SoapClient('http://payamakapi.ir/SendService.svc?wsdl', ['encoding' => 'UTF-8']);
            return $sms_client->SendSMS($parameters)->SendSMSResult;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}