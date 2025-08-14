<?php
/**
 * The file is automatically generated.
 */

namespace InnStudio\Prober\Components\Config;

class ConfigApi
{
    public static $config = array (
  'APP_VERSION' => '9.0.2',
  'APP_NAME' => 'X Prober',
  'APP_URL' => 'https://github.com/kmvan/x-prober',
  'AUTHOR_URL' => 'https://inn-studio.com/prober',
  'UPDATE_PHP_URLS' => 
  array (
    0 => 'https://raw.githubusercontent.com/kmvan/x-prober/master/dist/prober.php',
    1 => 'https://api.inn-studio.com/download/?id=xprober',
  ),
  'APP_CONFIG_URLS' => 
  array (
    0 => 'https://raw.githubusercontent.com/kmvan/x-prober/master/AppConfig.json',
    1 => 'https://api.inn-studio.com/download/?id=xprober-config',
  ),
  'BENCHMARKS_URLS' => 
  array (
    0 => 'https://raw.githubusercontent.com/kmvan/x-prober/master/benchmarks.json',
    1 => 'https://api.inn-studio.com/download/?id=xprober-benchmarks',
  ),
  'APP_CONFIG_URL_DEV' => 'http://localhost:8000/AppConfig.json',
  'APP_TEMPERATURE_SENSOR_URL' => 'http://127.0.0.1',
  'APP_TEMPERATURE_SENSOR_PORTS' => 
  array (
    0 => 2048,
    1 => 4096,
  ),
  'AUTHOR_NAME' => 'INN STUDIO',
  'LATEST_PHP_STABLE_VERSION' => '8',
  'LATEST_NGINX_STABLE_VERSION' => '1.22.0',
  'BENCHMARKS' => 
  array (
    0 => 
    array (
      'name' => 'Olink / E5-2680 v4 / PHP83 JIT',
      'url' => 'https://www.olink.cloud/clients/aff.php?aff=120',
      'date' => '2024-05-29',
      'proberUrl' => 'https://x-prober-server-benchmark-olink-sj.inn-studio.com',
      'binUrl' => '',
      'detail' => 
      array (
        'cpu' => 268212,
        'read' => 18495,
        'write' => 6164,
      ),
    ),
    1 => 
    array (
      'name' => 'RamNode / PHP82 JIT',
      'url' => 'https://clientarea.ramnode.com/aff.php?aff=4143',
      'date' => '2023-05-02',
      'detail' => 
      array (
        'cpu' => 203245,
        'read' => 68706,
        'write' => 11452,
      ),
    ),
    2 => 
    array (
      'name' => 'SpartanHost / HDD / PHP80 JIT',
      'url' => 'https://billing.spartanhost.net/aff.php?aff=801',
      'date' => '2021-07-17',
      'proberUrl' => 'https://x-prober-server-benchmark-spartanhost-dalls.inn-studio.com',
      'binUrl' => 'https://lg.dal.spartanhost.net/100MB.test',
      'detail' => 
      array (
        'cpu' => 280903,
        'read' => 65551,
        'write' => 16238,
      ),
    ),
    3 => 
    array (
      'name' => 'Vultr / Tokyo / PHP82 JIT',
      'url' => 'https://www.vultr.com/?ref=7826363-4F',
      'date' => '2023-05-02',
      'proberUrl' => 'https://x-prober-server-benchmark-vultr-tokyo.inn-studio.com/',
      'binUrl' => 'https://hnd-jp-ping.vultr.com/vultr.com.100MB.bin',
      'detail' => 
      array (
        'cpu' => 243748,
        'read' => 46066,
        'write' => 13824,
      ),
    ),
    4 => 
    array (
      'name' => 'BandwagonHOST / KVM / PHP80 JIT',
      'url' => 'https://bandwagonhost.com/aff.php?aff=34116',
      'proberUrl' => 'https://x-prober-server-benchmark-bwh-los-angeles.inn-studio.com/',
      'binUrl' => 'https://x-prober-server-benchmark-bwh-los-angeles.inn-studio.com/512m.bin',
      'date' => '2021-07-17',
      'detail' => 
      array (
        'cpu' => 185491,
        'read' => 13616,
        'write' => 4529,
      ),
    ),
  ),
);
}
