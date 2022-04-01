import { configure, makeAutoObservable } from 'mobx'
import { PingItemProps } from '../typings'
configure({
  enforceActions: 'observed',
})
class Main {
  public isPing = false
  public pingItems: PingItemProps[] = []
  public refs: {
    [id: string]: any
  } = {}
  public constructor() {
    makeAutoObservable(this)
  }
  public setRef = (id: string, c: any) => {
    this.refs[id] = c
  }
  public setIsPing = (isPing: boolean) => {
    this.isPing = isPing
  }
  public setPingItems = (pingItems: PingItemProps[]) => {
    this.pingItems = pingItems
  }
  public get pingItemsCount() {
    return this.pingItems.length
  }
  public appendPingItem = (item: PingItemProps) => {
    this.pingItems.push(item)
  }
}
export const PingStore = new Main()
