import { observable, action, computed, configure } from 'mobx'
import conf from '~components/Helper/src/components/conf'

configure({
  enforceActions: 'observed',
})

export interface PingItemProps {
  time: number
}

class MyInfoStore {
  public readonly ID = 'myInfo'
  public readonly conf = conf[this.ID]

  @observable public isPing: boolean = false
  @observable public pingItems: PingItemProps[] = []

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

export default new MyInfoStore()
