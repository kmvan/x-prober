import { observable, action, configure, computed } from 'mobx'
import { ComponentClass } from 'react'
import { orderBy } from 'lodash-es'

configure({
  enforceActions: 'observed',
})

export interface ICard {
  id: string
  title: string
  tinyTitle: string
  priority: number
  component: ComponentClass
}

class CardStore {
  @observable public cards: ICard[] = []

  @action
  public addCard = (card: ICard) => {
    this.cards.push(card)
  }

  @computed
  get cardsLength() {
    return this.cards.length
  }

  @computed
  get sortedCards() {
    return orderBy(this.cards, ['priority'], ['asc'])
  }
}

export default new CardStore()
