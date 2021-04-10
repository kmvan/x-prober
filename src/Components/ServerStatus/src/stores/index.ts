import { FetchStore } from '@/Fetch/src/stores'
import { conf } from '@/Utils/src/components/conf'
import { computed, configure, makeObservable } from 'mobx'
configure({
  enforceActions: 'observed',
})
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
class Main {
  public readonly ID = 'serverStatus'
  public readonly conf = conf?.[this.ID]
  public readonly enabled: boolean = !!this.conf
  public constructor() {
    makeObservable(this)
  }
  @computed private get fetchData() {
    return FetchStore.data?.[this.ID]
  }
  @computed public get sysLoad(): number[] {
    return FetchStore.isLoading
      ? this.conf?.sysLoad
      : this.fetchData?.sysLoad || [0, 0, 0]
  }
  @computed public get cpuUsage(): ServerStatusCpuUsageProps {
    return FetchStore.isLoading
      ? {
          idle: 90,
          nice: 0,
          sys: 5,
          user: 5,
        }
      : this.fetchData?.cpuUsage
  }
  @computed public get memRealUsage(): ServerStatusUsageProps {
    return FetchStore.isLoading
      ? this.conf?.memRealUsage
      : this.fetchData?.memRealUsage
  }
  @computed public get memCached(): ServerStatusUsageProps {
    return FetchStore.isLoading
      ? this.conf?.memCached
      : this.fetchData?.memCached
  }
  @computed public get memBuffers(): ServerStatusUsageProps {
    return FetchStore.isLoading
      ? this.conf?.memBuffers
      : this.fetchData?.memBuffers
  }
  @computed public get swapUsage(): ServerStatusUsageProps {
    return FetchStore.isLoading
      ? this.conf?.swapUsage
      : this.fetchData?.swapUsage
  }
  @computed public get swapCached(): ServerStatusUsageProps {
    return FetchStore.isLoading
      ? this.conf?.swapCached
      : this.fetchData?.swapCached
  }
}
export const ServerStatusStore = new Main()
