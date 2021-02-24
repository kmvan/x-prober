<?php

namespace InnStudio\Prober\Components\Utils;

class UtilsLocation
{
    /**
     * Get IP location.
     *
     * @param [string] $ip
     *
     * @return null|array $args
     *                    $args['country'] string Country, e.g, China
     *                    $args['region'] string Region, e.g, Heilongjiang
     *                    $args['city'] string City, e.g, Mohe
     *                    $args['flag'] string Emoji string, e,g, 🇨🇳
     */
    public static function getLocation($ip)
    {
        $url     = "http://api.ipstack.com/{$ip}?access_key=e4394fd12dbbefa08612306ca05baca3&format=1";
        $content = '';

        if (\function_exists('\curl_init')) {
            $ch = \curl_init();
            \curl_setopt_array($ch, array(
                \CURLOPT_URL            => $url,
                \CURLOPT_RETURNTRANSFER => true,
            ));
            $content = \curl_exec($ch);
            \curl_close($ch);
        } else {
            $content = \file_get_contents($url);
        }

        $item = \json_decode($content, true) ?: null;

        if ( ! $item) {
            return null;
        }

        return array(
            'country' => isset($item['country_name']) ? $item['country_name'] : '',
            'region'  => isset($item['region_name']) ? $item['region_name'] : '',
            'city'    => isset($item['city']) ? $item['city'] : '',
            'flag'    => isset($item['location']['country_flag_emoji']) ? $item['location']['country_flag_emoji'] : '',
        );
    }
}
