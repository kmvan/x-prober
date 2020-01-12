import { OK } from '~components/Restful/src/http-status'
import CardStore from '~components/Card/src/stores'
import restfulFetch from '~components/Fetch/src/restful-fetch'
import { observable, action, computed, configure } from 'mobx'
import { find } from 'lodash-es'

configure({
  enforceActions: 'observed',
})

export interface ITemperatureSensorItem {
  id: string
  name: string
  celsius: number
}

class TemperatureSensorStore {
  public readonly ID = 'temperatureSensor'

  @observable public items: ITemperatureSensorItem[] = []

  @action
  public setItems = (items: ITemperatureSensorItem[]) => {
    this.items = items
  }

  @action
  private setEnabledCard = () => {
    const { setCard, cards } = CardStore
    const item = find(cards, { id: this.ID })

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
    await restfulFetch('temperature-sensor')
      .then(([{ status }, items]) => {
        if (status === OK) {
          this.setItems(items)
          this.setEnabledCard()
          setTimeout(() => {
            this.fetch()
          }, 1000)
        }
      })
      .catch(err => {})
  }

  @computed
  public get itemsCount() {
    return this.items.length
  }
}

export default new TemperatureSensorStore()
