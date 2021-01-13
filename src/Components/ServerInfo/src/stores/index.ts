import conf from '@/Helper/src/components/conf'
import FetchStore from '@/Fetch/src/stores'
import { computed, configure, makeObservable } from 'mobx'
configure({
  enforceActions: 'observed',
})
interface ServerInfoDiskUsageProps {
  max: number
  value: number
}
interface UptimeProps {
  days: number
  hours: number
  mins: number
  secs: number
}
export interface ServerInfoDataProps {
  serverTime: string
  serverUptime: UptimeProps
  serverUtcTime: string
  diskUsage: ServerInfoDiskUsageProps
}
class Store {
  public readonly ID = 'serverInfo'
  public readonly conf = conf?.[this.ID]
  public readonly enabled: boolean = !!this.conf
  public constructor() {
    makeObservable(this)
  }
  @computed
  public get serverTime(): string {
    return FetchStore.isLoading
      ? this.conf?.serverTime
      : FetchStore.data?.[this.ID]?.serverTime
  }
  @computed
  public get serverUptime(): UptimeProps {
    return FetchStore.isLoading
      ? this.conf?.serverUptime
      : FetchStore.data?.[this.ID]?.serverUptime
  }
  @computed
  public get serverUtcTime(): string {
    return FetchStore.isLoading
      ? this.conf?.serverUtcTime
      : FetchStore.data?.[this.ID]?.serverUtcTime
  }
  @computed
  public get diskUsage(): ServerInfoDiskUsageProps {
    return FetchStore.isLoading
      ? this.conf?.diskUsage
      : FetchStore.data?.[this.ID]?.diskUsage
  }
}
const ServerInfoStore = new Store()
export default ServerInfoStore
