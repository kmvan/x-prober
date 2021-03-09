import { serverFetch } from '@/Fetch/src/server-fetch'
import { FetchStore } from '@/Fetch/src/stores'
import { gettext } from '@/Language/src'
import { OK } from '@/Restful/src/http-status'
import { conf } from '@/Utils/src/components/conf'
import { action, computed, configure, makeObservable, observable } from 'mobx'
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
export interface locationProps {
  country: string
  region: string
  city: string
  flag: string
}
class Main {
  public readonly ID = 'serverInfo'
  public readonly conf = conf?.[this.ID]
  public readonly enabled: boolean = !!this.conf
  @observable public serverIpv4: string = gettext('Loading...')
  @observable public serverIpv6: string = gettext('Loading...')
  @observable public serverLocation: locationProps | null = null
  public constructor() {
    makeObservable(this)
    this.fetchServerIpv4()
    this.fetchServerIpv6()
  }
  @action public setServerLocation = (serverLocation: locationProps) => {
    this.serverLocation = serverLocation
  }
  @action public setServerIpv4 = (serverIpv4: string) => {
    this.serverIpv4 = serverIpv4
  }
  @action public setServerIpv6 = (serverIpv6: string) => {
    this.serverIpv6 = serverIpv6
  }
  public fetchServerIpv4 = async () => {
    const { data, status } = await serverFetch(`serverIpv4`)
    if (data?.ip && status === OK) {
      this.setServerIpv4(data.ip)
    } else {
      this.setServerIpv4('-')
    }
  }
  public fetchServerIpv6 = async () => {
    const { data, status } = await serverFetch(`serverIpv6`)
    if (data?.ip && status === OK) {
      this.setServerIpv6(data.ip)
    } else {
      this.setServerIpv6('-')
    }
  }
  @computed public get serverTime(): string {
    return FetchStore.isLoading
      ? this.conf?.serverTime
      : FetchStore.data?.[this.ID]?.serverTime
  }
  @computed public get serverUptime(): UptimeProps {
    return FetchStore.isLoading
      ? this.conf?.serverUptime
      : FetchStore.data?.[this.ID]?.serverUptime
  }
  @computed public get serverUtcTime(): string {
    return FetchStore.isLoading
      ? this.conf?.serverUtcTime
      : FetchStore.data?.[this.ID]?.serverUtcTime
  }
  @computed public get diskUsage(): ServerInfoDiskUsageProps {
    return FetchStore.isLoading
      ? this.conf?.diskUsage
      : FetchStore.data?.[this.ID]?.diskUsage
  }
}
export const ServerInfoStore = new Main()
