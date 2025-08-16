<?php

namespace Arvand\ArvandPanel\SMS;

defined('ABSPATH') || exit;

use Exception;
use SoapClient;

class WPAPSendMessage
{
    public static function send($number, $message)
    {
        $sms_opt = wpap_sms_options();

        if (in_array($sms_opt['provider'], array_keys(WPAPSMS::supportedGateways()))) {
            $method = "{$sms_opt['provider']}_send_message";
        } else {
            $method = 'melipayamak_send_message';
        }

        return self::$method($number, $message);
    }

    public static function melipayamak_send_message($number, $message)
    {
        $opt = wpap_sms_provider_options('melipayamak');

        $data = [
            'username' => $opt['username'],
            'password' => $opt['password'],
            'to' => [$number],
            'from' => $opt['from'],
            'text' => $message,
            'isflash' => false
        ];

        ini_set("soap.wsdl_cache_enabled", 0);

        try {
            $sms = new SoapClient('http://api.payamak-panel.com/post/Send.asmx?wsdl', ['encoding' => 'UTF-8']);
            return $sms->SendSimpleSMS($data)->SendSimpleSMSResult;
        } catch (Exception $error) {
            return $error->getMessage();
        }
    }

    public static function farapayamak_send_message($number, $message)
    {
        $opt = wpap_sms_provider_options('farapayamak');

        $data = [
            'username' => $opt['username'],
            'password' => $opt['password'],
            'to' => [$number],
            'from' => $opt['from'],
            'text' => $message,
            'isflash' => false
        ];

        ini_set("soap.wsdl_cache_enabled", 0);

        try {
            $sms = new SoapClient('http://api.payamak-panel.com/post/Send.asmx?wsdl', ['encoding' => 'UTF-8']);
            return $sms->SendSimpleSMS($data)->SendSimpleSMSResult;
        } catch (Exception $w) {
            return false;
        }
    }

    public static function sms_ir_send_message($number, $message)
    {
        $opt = wpap_sms_provider_options('sms_ir');

        $params = [
            "lineNumber" => $opt['from'],
            "messageText" => $message,
            "mobiles" => [$number]
        ];

        $auth = array(
            'Content-Type: application/json',
            'X-API-KEY: ' . $opt['api_key']
        );

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.sms.ir/v1/send/bulk',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($params),
            CURLOPT_HTTPHEADER => $auth
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        return json_decode($response,true) == true;
    }

    public static function farazsms_send_message($number, $message)
    {
        $url = "https://ippanel.com/services.jspd";
        $rcpt_nm = [$number];
        $opt = wpap_sms_provider_options('farazsms');

        $param = [
            'uname' => $opt['username'],
            'pass' => $opt['password'],
            'from' => $opt['from'],
            'message' => $message,
            'to' => json_encode($rcpt_nm),
            'op' => 'send'
        ];

        try {
            $handler = curl_init($url);
            curl_setopt($handler, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($handler, CURLOPT_POSTFIELDS, $param);
            curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
            $res = curl_exec($handler);
            $res = json_decode($res);
            return $res[0] == 0;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function kavenegar_send_message($number, $message)
    {
        $opt = wpap_sms_provider_options('kavenegar');
        $url = sprintf('https://api.kavenegar.com/v1/%s/sms/send.json/', $opt['api_key']);

        $headers = [
            'Accept: application/json',
            'Content-Type: application/x-www-form-urlencoded',
            'charset: utf-8'
        ];

        $params = [
            "receptor" => $number,
            "sender" => $opt['from'],
            "message" => $message,
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

    public static function parsgreen_send_message($number, $message)
    {
        $api_main_url = 'http://sms.parsgreen.ir';
        $url = $api_main_url . '/Apiv2/Message/SendSms';
        $opt = wpap_sms_provider_options('parsgreen');
        $req = ['SmsBody' => $message, 'Mobiles' => [$number]];
        $json_data_encoded = json_encode($req);
        $api_key = $opt['api_key'];

        try {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data_encoded);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $header = ['authorization: BASIC APIKEY:' . $api_key, 'Content-Type: application/json;charset=utf-8'];
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            $result = curl_exec($ch);
            $res = json_decode($result);
            curl_close($ch);
            return $res->R_Success;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function modirpayamak_send_message($number, $message)
    {
        $opt = wpap_sms_provider_options('modirpayamak');
        $url = "https://ippanel.com/services.jspd";
        $rcpt_nm = [$number];

        $param = [
            'uname' => $opt['username'],
            'pass' => $opt['password'],
            'from' => $opt['from'],
            'message' => $message,
            'to' => json_encode($rcpt_nm),
            'op' => 'send'
        ];

        try {
            $handler = curl_init($url);
            curl_setopt($handler, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($handler, CURLOPT_POSTFIELDS, $param);
            curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
            $response2 = curl_exec($handler);
            $response2 = json_decode($response2);
            return $response2;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public static function raygansms_send_message($number, $message)
    {
        $opt = wpap_sms_provider_options('raygansms');
        $Url = 'http://smspanel.Trez.ir/SendMessageWithPost.ashx';

        $data = [
            'Username' => $opt['username'],
            'Password' => $opt['password'],
            'PhoneNumber' => $opt['from'],
            'MessageBody' => $message,
            'RecNumber' => $number,
            'Smsclass' => '1'
        ];

        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $Url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $server_output = curl_exec($ch);
            curl_close($ch);
            return $server_output >= 2000;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function webone_sms_send_message($number, $message)
    {
        $opt = wpap_sms_provider_options('webone_sms');
        $recId = [0];
        $status = 0x0;

        $parameters = [
            'userName' => $opt['username'],
            'password' => $opt['password'],
            'fromNumber' => $opt['from'],
            'toNumbers' => [$number],
            'messageContent' => $message,
            'isFlash' => false,
            'recId' => &$recId,
            'status' => &$status
        ];

        ini_set('soap.wsdl_cache_enabled', '0');

        try {
            $sms_client = new SoapClient('http://payamakapi.ir/SendService.svc?wsdl', ['encoding' => 'UTF-8']);
            return $sms_client->SendSMS($parameters)->SendSMSResult;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}