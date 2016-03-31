<?php
namespace App\Http\Models\sms;

class SENDSMS
{

    public static function  sendSeeyouSMS($to, $datas, $tempId)
    {
        $accountSid = '8a48b5514fa577af014fa5ab5aed00fc';
        $accountToken = 'da915b84e73d44858f909a478d4480bc';
        $appId = '8a48b55153404cc30153408ea81200aa';
        $serverIP = 'sandboxapp.cloopen.com';
        $serverPort = '8883';
        $softVersion = '2013-12-26';
        $rest = new REST($serverIP, $serverPort, $softVersion);
        $rest->setAccount($accountSid, $accountToken);
        $rest->setAppId($appId);
                var_dump("OK");
        die();
        return $rest->sendSeeyouSMS($to, $datas, $tempId);
    }
}

?>