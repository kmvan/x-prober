export interface ConfigProps {
  APP_VERSION: string;
  APP_NAME: string;
  APP_URL: string;
  AUTHOR_URL: string;
  UPDATE_PHP_URLS: string[];
  APP_CONFIG_URLS: string[];
  BENCHMARKS_URLS: string[];
  APP_CONFIG_URL_DEV: string;
  APP_TEMPERATURE_SENSOR_URL: string;
  APP_TEMPERATURE_SENSOR_PORTS: number[];
  AUTHOR_NAME: string;
  LATEST_PHP_STABLE_VERSION: string;
  LATEST_NGINX_STABLE_VERSION: string;
}
