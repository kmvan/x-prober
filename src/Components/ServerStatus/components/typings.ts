export interface ServerStatusUsageProps {
  max: number;
  value: number;
}
export interface ServerStatusCpuUsageProps {
  usage: number;
  idle: number;
  sys: number;
  user: number;
}
export interface ServerStatusPollDataProps {
  sysLoad: number[];
  cpuUsage: ServerStatusCpuUsageProps;
  memRealUsage: ServerStatusUsageProps;
  memBuffers: ServerStatusUsageProps;
  memCached: ServerStatusUsageProps;
  swapUsage: ServerStatusUsageProps;
  swapCached: ServerStatusUsageProps;
}
