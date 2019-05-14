import { observable, action, computed } from 'mobx'
import conf from '~components/Helper/src/components/conf'

export interface IPingItem {
  time: number
}

class MyInfoStore {
  public ID = 'myInfo'
  public conf = conf[this.ID] || false

  @observable public isPing: boolean = false
  @observable public pingItems: IPingItem[] = []

  @observable public refs = {}

  @action
  public setRef = (id: string, c) => {
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

export default new MyInfoStore()
