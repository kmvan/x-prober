export enum UserConfigDisableItemProps {
  ServerStatus = 'serverStatus',
  DiskUsage = 'diskUsage',
  NetworkStats = 'networkStats',
  Ping = 'ping',
  ServerInfo = 'serverInfo',
  PhpInfo = 'phpInfo',
  PhpInfoDetail = 'phpInfoDetail',
  PhpDisabledFunctions = 'phpDisabledFunctions',
  PhpDisabledClasses = 'phpDisabledClasses',
  PhpExtensions = 'phpExtensions',
  PhpExtensionsLoaded = 'phpExtensionsLoaded',
  Database = 'database',
  MyServerBenchmark = 'myServerBenchmark',
  MyInfo = 'myInfo',
  ServerIp = 'serverIp',
}
export type UserConfigNodeProps = [nodeName: string, url: string];
export interface UserConfigProps {
  serverBenchmarkCd?: number;
  nodes?: UserConfigNodeProps[];
  disabled?: UserConfigDisableItemProps[];
}
