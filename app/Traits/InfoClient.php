<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 10/4/18
 * Time: 5:16 PM
 */

namespace App\Traits;


trait InfoClient
{
    public function getPlatform($agent)
    {
        if (preg_match('/linux/i', $agent)) {
            $platform = 'Linux';
        } elseif (preg_match('/iPhone os x/i', $agent)) {
            $platform = 'iPhone';
        } elseif (preg_match('/macintosh|mac os x/i', $agent)) {
            $platform = 'Mac';
        } elseif (preg_match('/windows phone/i', $agent)) {
            $platform = 'Windows phone';
        } elseif (preg_match('/windows|win32/i', $agent)) {
            $platform = 'Windows';
        } elseif (preg_match('/Android/i', $agent)) {
            $platform = 'Android';
        } else {
            $platform = 'Unknown';
        }

        return $platform;
    }

    public function getBrowserName($agent)
    {
        $data = ['browserName' => '', 'ub' => ''];
        if (preg_match('/MSIE/i', $agent) && !preg_match('/Opera/i', $agent)) {
            $data['browserName'] = 'Internet Explorer';
            $data['ub'] = "MSIE";
        } elseif (preg_match('/Firefox/i', $agent)) {
            $data['browserName'] = 'Mozilla Firefox';
            $data['ub'] = "Firefox";
        } elseif (preg_match('/Opera/i', $agent)) {
            $data['browserName'] = 'Opera';
            $data['ub'] = "Opera";
        } elseif (preg_match('/Netscape/i', $agent)) {
            $data['browserName'] = 'Netscape';
            $data['ub'] = "Netscape";
        } elseif (strpos($agent, "Edge") !== false) {
            $data['browserName'] = 'Microsoft Edge';
            $data['ub'] = "Edge";
        } elseif (preg_match('/Edg/i', $agent)) {
            $data['browserName'] = 'Microsoft Edge';
            $data['ub'] = "Edg";
        } elseif (preg_match('/Chrome/i', $agent)) {
            $data['browserName'] = 'Google Chrome';
            $data['ub'] = "Chrome";
        } elseif (preg_match('/Safari/i', $agent)) {
            $data['browserName'] = 'Apple Safari';
            $data['ub'] = "Safari";
        } elseif (preg_match('/YaBrowser/i', $agent)) {
            $data['browserName'] = 'Yandex browser';
            $data['ub'] = "YaBrowser";
        } elseif (strpos($agent, "facebook") !== false) {
            $data['browserName'] = 'Facebook';
            $data['ub'] = "Facebook Bot";
        } elseif (strpos($agent, "Sitemaps") !== false) {
            $data['browserName'] = 'Sitemap';
            $data['ub'] = "Sitemap Bot";
        } elseif (strpos($agent, "bot") !== false) {
            $data['browserName'] = 'Bots';
            $data['ub'] = "Other Bots";
        } else {
            $data['ub'] = "Bot";
            $data['browserName'] = "";
        }

        return $data;
    }

    public function getBrowserVersion($agent, $ub)
    {
        $known = ['Version', $ub, 'other'];
        $pattern = '#(?<browser>' . join('|', $known) . ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
        preg_match_all($pattern, $agent, $matches);

        // see how many we have
        $i = count($matches['browser']);
        if ($i != 1) {
            //we will have two since we are not using 'other' argument yet
            //see if version is before or after the name
            if (strripos($agent, "Version") < strripos($agent, $ub)) {
                $data['version'] = $matches['version'][0];
            } else {
                if (!empty($matches['version'])) {
                    $data['version'] = $matches['version'][1];
                } else {
                    $data['version'] = 0;
                }
            }
        } else {
            $data['version'] = $matches['version'][0];
        }

        $data['pattern'] = $pattern;

        return $data;
    }
}
