import { configure, makeAutoObservable } from 'mobx'
import { FetchStore } from '../../Fetch/stores'
import { NetworkStatsConstants } from '../constants'
configure({
  enforceActions: 'observed',
})
const { conf, id } = NetworkStatsConstants
export interface NetworkStatsItemProps {
  id: string
  rx: number
  tx: number
}
class Main {
  public constructor() {
    makeAutoObservable(this)
  }
  public get items(): NetworkStatsItemProps[] {
    return (
      (FetchStore.isLoading
        ? conf?.networks
        : FetchStore.data?.[id]?.networks) || []
    )
  }
  public get sortItems() {
    return this.items
      .slice()
      .filter(({ tx }) => Boolean(tx))
      .sort((a, b) => a.tx - b.tx)
  }
  public get itemsCount() {
    return this.sortItems.length
  }
  public get timestamp(): number {
    return (
      (FetchStore.isLoading
        ? conf?.timestamp
        : FetchStore.data?.[id]?.timestamp) ||
      conf?.timestamp ||
      0
    )
  }
}
export const NetworkStatsStore = new Main()
