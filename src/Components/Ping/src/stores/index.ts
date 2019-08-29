import { observable, action, computed } from 'mobx'

export interface IPingItem {
  time: number
}

class PingStore {
  public ID = 'ping'

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
