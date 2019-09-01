import { observable, action, computed, configure } from 'mobx'

configure({
  enforceActions: 'observed',
})

export interface IPingItem {
  time: number
}

class PingStore {
  public readonly ID = 'ping'

  @observable public isPing: boolean = false
  @observable public pingItems: IPingItem[] = []
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
  public setPingItems = (pingItems: IPingItem[]) => {
    this.pingItems = pingItems
  }

  @computed
  get pingItemsCount() {
    return this.pingItems.length
  }

  @action
  public appendPingItem = (item: IPingItem) => {
    this.pingItems.push(item)
  }
}

export default new PingStore()
