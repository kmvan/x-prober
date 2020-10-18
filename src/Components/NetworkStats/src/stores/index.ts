import conf from '@/Helper/src/components/conf'
import { computed, configure } from 'mobx'
import FetchStore from '@/Fetch/src/stores'

configure({
  enforceActions: 'observed',
})

export interface NetworkStatsItemProps {
  id: string
  rx: number
  tx: number
}

class NetworkStatsStore {
  public readonly ID = 'networkStats'
  public readonly conf = conf?.[this.ID]
  public readonly enabled: boolean = !!this.conf

  @computed
  public get items(): NetworkStatsItemProps[] {
    return (
      (FetchStore.isLoading
        ? this.conf?.networks
        : FetchStore.data?.[this.ID]?.networks) || []
    )
  }

  @computed
  public get sortItems() {
    return this.items
      .slice()
      .filter(({ tx }) => !!tx)
      .sort((a, b) => a.tx - b.tx)
  }

  @computed
  public get itemsCount() {
    return this.sortItems.length
  }

  @computed
  public get timestamp(): number {
    return (
      (FetchStore.isLoading
        ? this.conf?.timestamp
        : FetchStore.data?.[this.ID]?.timestamp) ||
      this.conf?.timestamp ||
      0
    )
  }
}

export default new NetworkStatsStore()
