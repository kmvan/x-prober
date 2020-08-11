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
  public readonly conf = conf?.[this.ID]
  public readonly enabled: boolean = !!this.conf

  @computed
  public get items(): NetworkStatsItemProps | null {
    return (
      (FetchStore.isLoading
        ? this.conf?.networks
        : FetchStore.data?.[this.ID]?.networks) || null
    )
  }
}

export default new NetworkStatsStore()
