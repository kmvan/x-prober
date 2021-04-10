import { serverFetch } from '@/Fetch/src/server-fetch'
import { gettext } from '@/Language/src'
import { NetworkStatsItemProps } from '@/NetworkStats/src/stores'
import { OK } from '@/Restful/src/http-status'
import { ServerInfoDataProps } from '@/ServerInfo/src/stores'
import { ServerStatusDataProps } from '@/ServerStatus/src/stores'
import { action, configure, makeObservable, observable } from 'mobx'
configure({
  enforceActions: 'observed',
})
export interface DataNetworkStatsProps {
  networks: NetworkStatsItemProps[]
  timestamp: number
}
export interface DataProps {
  serverInfo: ServerInfoDataProps
  serverStatus: ServerStatusDataProps
  networkStats: DataNetworkStatsProps
}
class Main {
  @observable public isLoading: boolean = true
  @observable public data = {}
  constructor() {
    makeObservable(this)
    this.initFetch()
  }
  public initFetch = async () => {
    const { data, status } = await serverFetch('fetch')
    if (data && status === OK) {
      this.setData(data)
      this.isLoading && this.setIsLoading(false)
      setTimeout(async () => {
        await this.initFetch()
      }, 1000)
    } else {
      alert(gettext('Fetch error, please refresh page.'))
    }
  }
  @action public setIsLoading = (isLoading: boolean) => {
    this.isLoading = isLoading
  }
  @action public setData = (data: any) => {
    this.data = data
  }
}
export const FetchStore = new Main()
