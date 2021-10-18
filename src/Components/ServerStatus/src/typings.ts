export interface ServerStatusUsageProps {
  max: number
  value: number
}
export interface ServerStatusCpuUsageProps {
  idle: number
  nice: number
  sys: number
  user: number
}
export interface ServerStatusDataProps {
  sysLoad: number[]
  cpuUsage: ServerStatusCpuUsageProps
  memRealUsage: ServerStatusUsageProps
  memBuffers: ServerStatusUsageProps
  memCached: ServerStatusUsageProps
  swapUsage: ServerStatusUsageProps
  swapCached: ServerStatusUsageProps
}
