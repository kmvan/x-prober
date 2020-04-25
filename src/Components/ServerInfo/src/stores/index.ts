import conf from '~components/Helper/src/components/conf'
import { configure, computed } from 'mobx'
import FetchStore from '~components/Fetch/src/stores'

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

class ServerInfoStore {
  public readonly ID = 'serverInfo'
  public readonly conf = conf?.[this.ID]

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

export default new ServerInfoStore()
