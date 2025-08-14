export interface PhpExtensionsPollDataProps {
  redis: boolean;
  sqlite3: boolean;
  memcache: boolean;
  memcached: boolean;
  opcache: boolean;
  opcacheEnabled: boolean;
  opcacheJitEnabled: boolean;
  swoole: boolean;
  imagick: boolean;
  gmagick: boolean;
  exif: boolean;
  fileinfo: boolean;
  simplexml: boolean;
  sockets: boolean;
  mysqli: boolean;
  zip: boolean;
  mbstring: boolean;
  phalcon: boolean;
  xdebug: boolean;
  zendOptimizer: boolean;
  ionCube: boolean;
  sourceGuardian: boolean;
  ldap: boolean;
  curl: boolean;
  loadedExtensions: string[];
}
