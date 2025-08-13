export type UserConfigDisableFeatureKey =
  | 'ServerStatus'
  | 'DiskUsage'
  | 'NetworkStats'
  | 'Ping'
  | 'ServerInfo'
  | 'PhpInfo'
  | 'PhpInfoDetail'
  | 'PhpDisabledFunctions'
  | 'PhpDisabledClasses'
  | 'PhpExtensions'
  | 'PhpExtensionsLoaded'
  | 'Database'
  | 'MyServerBenchmark'
  | 'MyInfo'
  | 'ServerIp';
export type UserConfigDisableFeatureValue =
  | 'serverStatus'
  | 'diskUsage'
  | 'networkStats'
  | 'ping'
  | 'serverInfo'
  | 'phpInfo'
  | 'phpInfoDetail'
  | 'phpDisabledFunctions'
  | 'phpDisabledClasses'
  | 'phpExtensions'
  | 'phpExtensionsLoaded'
  | 'database'
  | 'myServerBenchmark'
  | 'myInfo'
  | 'serverIp';
export const UserConfigDisableFeature = {
  ServerStatus: 'serverStatus',
  DiskUsage: 'diskUsage',
  NetworkStats: 'networkStats',
  Ping: 'ping',
  ServerInfo: 'serverInfo',
  PhpInfo: 'phpInfo',
  PhpInfoDetail: 'phpInfoDetail',
  PhpDisabledFunctions: 'phpDisabledFunctions',
  PhpDisabledClasses: 'phpDisabledClasses',
  PhpExtensions: 'phpExtensions',
  PhpExtensionsLoaded: 'phpExtensionsLoaded',
  Database: 'database',
  MyServerBenchmark: 'myServerBenchmark',
  MyInfo: 'myInfo',
  ServerIp: 'serverIp',
};
export type UserConfigNodeProps = [nodeName: string, url: string];
export interface UserConfigProps {
  serverBenchmarkCd?: number;
  nodes?: UserConfigNodeProps[];
  disabled?: UserConfigDisableFeatureKey;
}
