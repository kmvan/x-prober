import { configure, makeAutoObservable } from 'mobx'
import { FetchStore } from '../../Fetch/stores'
import { ServerStatusConstants } from '../constants'
import { ServerStatusCpuUsageProps, ServerStatusUsageProps } from '../typings'
configure({
  enforceActions: 'observed',
})
const { id, conf } = ServerStatusConstants
class Main {
  public constructor() {
    makeAutoObservable(this)
  }
  private get fetchData() {
    return FetchStore.data?.[id]
  }
  public get sysLoad(): number[] {
    return FetchStore.isLoading
      ? conf?.sysLoad
      : this.fetchData?.sysLoad || [0, 0, 0]
  }
  public get cpuUsage(): ServerStatusCpuUsageProps {
    return FetchStore.isLoading
      ? {
          idle: 90,
          nice: 0,
          sys: 5,
          user: 5,
        }
      : this.fetchData?.cpuUsage
  }
  public get memRealUsage(): ServerStatusUsageProps {
    return FetchStore.isLoading
      ? conf?.memRealUsage
      : this.fetchData?.memRealUsage
  }
  public get memCached(): ServerStatusUsageProps {
    return FetchStore.isLoading ? conf?.memCached : this.fetchData?.memCached
  }
  public get memBuffers(): ServerStatusUsageProps {
    return FetchStore.isLoading ? conf?.memBuffers : this.fetchData?.memBuffers
  }
  public get swapUsage(): ServerStatusUsageProps {
    return FetchStore.isLoading ? conf?.swapUsage : this.fetchData?.swapUsage
  }
  public get swapCached(): ServerStatusUsageProps {
    return FetchStore.isLoading ? conf?.swapCached : this.fetchData?.swapCached
  }
}
export const ServerStatusStore = new Main()
