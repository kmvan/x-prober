export interface ServerInfoUptimeProps {
  days: number;
  hours: number;
  mins: number;
  secs: number;
}
export interface ServerInfoPollDataProps {
  serverName: string;
  serverUtcTime: string;
  serverTime: string;
  localIpv4: string;
  localIpv6: string;
  serverUptime: ServerInfoUptimeProps;
  serverIp: string;
  serverSoftware: string;
  phpVersion: string;
  cpuModel: string;
  serverOs: string;
  scriptPath: string;
}
