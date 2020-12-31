import { observable, action, configure, makeObservable } from 'mobx'
import serverFetch from '@/Fetch/src/server-fetch'
import { OK } from '@/Restful/src/http-status'
import { gettext } from '@/Language/src'
import { ServerInfoDataProps } from '@/ServerInfo/src/stores'
import { ServerStatusDataProps } from '@/ServerStatus/src/stores'
import { NetworkStatsItemProps } from '@/NetworkStats/src/stores'

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

class Store {
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

  @action
  public setIsLoading = (isLoading: boolean) => {
    this.isLoading = isLoading
  }

  @action
  public setData = (data: any) => {
    this.data = data
  }
}

const FetchStore = new Store()

export default FetchStore
