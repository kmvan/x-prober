import { get } from 'lodash-es'
import conf from '~components/Helper/src/components/conf'
import { configure, computed } from 'mobx'
import FetchStore from '~components/Fetch/src/stores'

interface IServerInfoDiskUsage {
  max: number
  value: number
}

configure({
  enforceActions: 'observed',
})

interface IUptime {
  days: number
  hours: number
  mins: number
  secs: number
}

class ServerInfoStore {
  public ID = 'serverInfo'
  public conf = get(conf, this.ID)

  @computed
  get serverTime(): string {
    return FetchStore.isLoading
      ? get(this.conf, 'serverTime')
      : get(FetchStore.data, `${this.ID}.serverTime`)
  }

  @computed
  get serverUptime(): IUptime {
    return FetchStore.isLoading
      ? get(this.conf, 'serverUptime')
      : get(FetchStore.data, `${this.ID}.serverUptime`)
  }

  @computed
  get serverUtcTime(): string {
    return FetchStore.isLoading
      ? get(this.conf, 'serverUtcTime')
      : FetchStore.data[this.ID].serverUtcTime
  }

  @computed
  get diskUsage(): IServerInfoDiskUsage {
    return FetchStore.isLoading
      ? get(this.conf, 'diskUsage')
      : FetchStore.data[this.ID].diskUsage
  }
}

export default new ServerInfoStore()
