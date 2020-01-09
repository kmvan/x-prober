import { observable, action, configure, computed } from 'mobx'
import { ComponentClass } from 'react'
import { orderBy, findIndex } from 'lodash-es'

configure({
  enforceActions: 'observed',
})

export interface ICard {
  id: string
  title: string
  tinyTitle: string
  enabled?: boolean
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
  public get cardsLength() {
    return this.cards.length
  }

  @action
  public setCard = ({ id, ...card }: Partial<ICard>) => {
    const i = findIndex(this.cards, { id })

    if (i === -1) {
      return
    }

    this.cards[i] = { ...this.cards[i], ...card }
  }

  @computed
  public get sortedCards() {
    return orderBy(this.cards, ['priority'], ['asc'])
  }
}

export default new CardStore()
