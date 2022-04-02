<?php

namespace InnStudio\Prober\Components\Utils;

final class UtilsServerIp
{
    public static function getV4()
    {
        return self::getV4ViaInnStudioCom() ?: self::getV4ViaIpv6TestCom() ?: self::getV4Local();
    }

    public static function getV6()
    {
        return self::getV6ViaInnStudioCom() ?: self::getV6ViaIpv6TestCom() ?: self::getV6Local();
    }

    private static function getV4Local()
    {
        $content = isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '';

        return filter_var($content, \FILTER_VALIDATE_IP, array(
            'flags' => \FILTER_FLAG_IPV4,
        )) ?: '';
    }

    private static function getV6Local()
    {
        $content = isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '';

        return filter_var($content, \FILTER_VALIDATE_IP, array(
            'flags' => \FILTER_FLAG_IPV6,
        )) ?: '';
    }

    private static function getV4ViaInnStudioCom()
    {
        return self::getContent('https://ipv4.inn-studio.com/ip/', 4);
    }

    private static function getV6ViaInnStudioCom()
    {
        return self::getContent('https://ipv6.inn-studio.com/ip/', 6);
    }

    private static function getV4ViaIpv6TestCom()
    {
        return self::getContent('https://v4.ipv6-test.com/api/myip.php', 4);
    }

    private static function getV6ViaIpv6TestCom()
    {
        return self::getContent('https://v6.ipv6-test.com/api/myip.php', 6);
    }

    private static function getContent($url, $type)
    {
        $content = '';

        if (\function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt_array($ch, array(
                \CURLOPT_URL            => $url,
                \CURLOPT_RETURNTRANSFER => true,
            ));
            $content = curl_exec($ch);
            curl_close($ch);
        } else {
            $content = file_get_contents($url);
        }

        return (string) filter_var($content, \FILTER_VALIDATE_IP, array(
            'flags' => 6 === $type ? \FILTER_FLAG_IPV6 : \FILTER_FLAG_IPV4,
        )) ?: '';
    }
}
