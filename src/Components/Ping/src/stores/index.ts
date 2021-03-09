import { conf } from '@/Utils/src/components/conf'
import { action, computed, configure, makeObservable, observable } from 'mobx'
configure({
  enforceActions: 'observed',
})
export interface PingItemProps {
  time: number
}
class Main {
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
  @action public setRef = (id: string, c: any) => {
    this.refs[id] = c
  }
  @action public setIsPing = (isPing: boolean) => {
    this.isPing = isPing
  }
  @action public setPingItems = (pingItems: PingItemProps[]) => {
    this.pingItems = pingItems
  }
  @computed public get pingItemsCount() {
    return this.pingItems.length
  }
  @action public appendPingItem = (item: PingItemProps) => {
    this.pingItems.push(item)
  }
}
export const PingStore = new Main()
