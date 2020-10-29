import { observable, action, computed, configure, makeObservable } from 'mobx'
import conf from '@/Helper/src/components/conf'

configure({
  enforceActions: 'observed',
})

export interface PingItemProps {
  time: number
}

class Store {
  public readonly ID = 'ping'
  public readonly conf = conf?.[this.ID]
  public readonly enabled: boolean = !!this.conf

  @observable public isPing: boolean = false
  @observable public pingItems: PingItemProps[] = []
  @observable public refs: {
    [id: string]: any
  } = {}

  public constructor() {
    makeObservable(this)
  }

  @action
  public setRef = (id: string, c: any) => {
    this.refs[id] = c
  }

  @action
  public setIsPing = (isPing: boolean) => {
    this.isPing = isPing
  }

  @action
  public setPingItems = (pingItems: PingItemProps[]) => {
    this.pingItems = pingItems
  }

  @computed
  public get pingItemsCount() {
    return this.pingItems.length
  }

  @action
  public appendPingItem = (item: PingItemProps) => {
    this.pingItems.push(item)
  }
}

const PingStore = new Store()

export default PingStore
