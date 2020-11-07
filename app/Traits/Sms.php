<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 9/14/18
 * Time: 12:45 PM
 */

namespace App\Traits;

trait Sms
{
    protected $port = "80";

    public function sendSms($message, $phone, $sender = null, $wapUrl = false)
    {
        $response = $this->send($phone, $message, $sender, $wapUrl);

        if (explode(';', $response)[0] == 'accepted')
            return true;

        return false;
    }

    public function send($phone, $text, $sender, $wap_url)
    {
        if (!$sender)
            $sender = config('services.sms.sender');

        $fp = fsockopen(config('services.sms.host'), $this->port, $error_no, $error_str);
        if (!$fp) {
            return "errno: $error_no \nerrstr: $error_str\n";
        }
        fwrite($fp, "GET /messages/v2/send/" .
            "?phone=" . rawurlencode($phone) .
            "&text=" . rawurlencode($text) .
            ($sender ? "&sender=" . rawurlencode($sender) : "") .
            ($wap_url ? "&wapurl=" . rawurlencode($wap_url) : "") .
            "  HTTP/1.0\n");
        fwrite($fp, "Host: " . config('services.sms.host') . "\r\n");
        fwrite($fp, "Authorization: Basic " .
            base64_encode(config('services.sms.login') . ":" . config('services.sms.password')) . "\n");
        fwrite($fp, "\n");
        $response = "";
        while (!feof($fp)) {
            $response .= fread($fp, 1);
        }
        fclose($fp);
        list($other, $responseBody) = explode("\r\n\r\n", $response, 2);

        return $responseBody;
    }
}
