import CardStore from '@/Card/src/stores'
import serverFetch from '@/Fetch/src/server-fetch'
import {
  action,
  computed,
  configure,
  makeObservable,
  observable
  } from 'mobx'
import { OK } from '@/Restful/src/http-status'
configure({
  enforceActions: 'observed',
})
export interface TemperatureSensorItemProps {
  id: string
  name: string
  celsius: number
}
class Store {
  public readonly ID = 'temperatureSensor'
  @observable public items: TemperatureSensorItemProps[] = []
  public constructor() {
    makeObservable(this)
  }
  @action
  public setItems = (items: TemperatureSensorItemProps[]) => {
    this.items = items
  }
  @action
  private setEnabledCard = () => {
    const { setCard, cards } = CardStore
    const item = cards.find(({ id }) => id === this.ID)
    if (!item) {
      return
    }
    if (item.enabled) {
      return
    }
    setCard({
      id: this.ID,
      enabled: true,
    })
  }
  @action
  public fetch = async () => {
    const { data: items, status } = await serverFetch('temperature-sensor')
    if (status === OK) {
      this.setItems(items)
      this.setEnabledCard()
      setTimeout(() => {
        this.fetch()
      }, 1000)
    }
  }
  @computed
  public get itemsCount() {
    return this.items.length
  }
}
const TemperatureSensorStore = new Store()
export default TemperatureSensorStore
