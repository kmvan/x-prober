import { get } from 'lodash-es'
import conf from '~components/Helper/src/components/conf'
import { configure, observable, action, computed } from 'mobx'
import FetchStore from '~components/Fetch/src/stores'

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
    if (FetchStore.isLoading) {
      return get(this.conf, 'serverTime')
    }

    return FetchStore.data[this.ID].serverTime
  }

  @computed
  get serverUptime(): IUptime {
    return FetchStore.isLoading
      ? get(this.conf, 'serverUptime')
      : get(FetchStore.data, `${this.ID}.serverUptime`)
  }

  @computed
  get serverUtcTime(): string {
    if (FetchStore.isLoading) {
      return get(this.conf, 'serverUtcTime')
    }

    return FetchStore.data[this.ID].serverUtcTime
  }
}

export default new ServerInfoStore()
