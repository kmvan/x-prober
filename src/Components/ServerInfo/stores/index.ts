import { configure, makeAutoObservable } from 'mobx'
import { serverFetch } from '../../Fetch/server-fetch'
import { FetchStore } from '../../Fetch/stores'
import { gettext } from '../../Language'
import { OK } from '../../Rest/http-status'
import { conf } from '../../Utils/components/conf'
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
export interface LocationProps {
  country: string
  region: string
  city: string
  flag: string
}
class Main {
  public readonly ID = 'serverInfo'
  public readonly conf = conf?.[this.ID]
  public readonly enabled: boolean = Boolean(this.conf)
  public serverIpv4: string = gettext('Loading...')
  public serverIpv6: string = gettext('Loading...')
  public serverLocation: LocationProps | null = null
  public constructor() {
    makeAutoObservable(this)
    this.fetchServerIpv4()
    this.fetchServerIpv6()
  }
  public setServerLocation = (serverLocation: LocationProps) => {
    this.serverLocation = serverLocation
  }
  public setServerIpv4 = (serverIpv4: string) => {
    this.serverIpv4 = serverIpv4
  }
  public setServerIpv6 = (serverIpv6: string) => {
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
  public get serverTime(): string {
    return FetchStore.isLoading
      ? this.conf?.serverTime
      : FetchStore.data?.[this.ID]?.serverTime
  }
  public get serverUptime(): UptimeProps {
    return FetchStore.isLoading
      ? this.conf?.serverUptime
      : FetchStore.data?.[this.ID]?.serverUptime
  }
  public get serverUtcTime(): string {
    return FetchStore.isLoading
      ? this.conf?.serverUtcTime
      : FetchStore.data?.[this.ID]?.serverUtcTime
  }
  public get diskUsage(): ServerInfoDiskUsageProps {
    return FetchStore.isLoading
      ? this.conf?.diskUsage
      : FetchStore.data?.[this.ID]?.diskUsage
  }
}
export const ServerInfoStore = new Main()
