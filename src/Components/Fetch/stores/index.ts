import { configure, makeAutoObservable } from 'mobx'
import { gettext } from '../../Language'
import { NetworkStatsItemProps } from '../../NetworkStats/stores'
import { OK } from '../../Rest/http-status'
import { ServerInfoDataProps } from '../../ServerInfo/stores'
import { ServerStatusDataProps } from '../../ServerStatus/typings'
import { serverFetch } from '../server-fetch'
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
  public isLoading = true
  public data = {}
  constructor() {
    makeAutoObservable(this)
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
  public setIsLoading = (isLoading: boolean) => {
    this.isLoading = isLoading
  }
  public setData = (data: any) => {
    this.data = data
  }
}
export const FetchStore = new Main()
