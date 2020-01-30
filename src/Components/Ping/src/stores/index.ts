import { observable, action, computed, configure } from 'mobx'

configure({
  enforceActions: 'observed',
})

export interface PingItemProps {
  time: number
}

class PingStore {
  public readonly ID = 'ping'

  @observable public isPing: boolean = false
  @observable public pingItems: PingItemProps[] = []
  @observable public refs: {
    [id: string]: any
  } = {}

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

export default new PingStore()
