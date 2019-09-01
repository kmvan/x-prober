import { observable, action, computed, configure } from 'mobx'
import conf from '~components/Helper/src/components/conf'

configure({
  enforceActions: 'observed',
})

export interface IPingItem {
  time: number
}

class MyInfoStore {
  public readonly ID = 'myInfo'
  public readonly conf = conf[this.ID]

  @observable public isPing: boolean = false
  @observable public pingItems: IPingItem[] = []

  @observable public refs = {}

  @action
  public setRef = (id: string, c: HTMLElement) => {
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
