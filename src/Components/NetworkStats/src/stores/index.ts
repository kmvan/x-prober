import { get } from 'lodash-es'
import conf from '~components/Helper/src/components/conf'
import { computed, configure } from 'mobx'
import FetchStore from '~components/Fetch/src/stores'

configure({
  enforceActions: 'observed',
})

export interface NetworkStatsItemProps {
  [networkCardid: string]: {
    rx: number
    tx: number
  }
}

class NetworkStatsStore {
  public readonly ID = 'networkStats'
  public readonly conf = get(conf, this.ID)

  @computed
  public get items(): NetworkStatsItemProps | null {
    return (
      (FetchStore.isLoading
        ? get(this.conf, 'networks')
        : get(FetchStore.data, `${this.ID}.networks`)) || null
    )
  }
}

export default new NetworkStatsStore()
