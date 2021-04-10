import { action, computed, configure, makeObservable, observable } from 'mobx'
import { FunctionComponent, MouseEvent } from 'react'
configure({
  enforceActions: 'observed',
})
export interface StoragePriorityItemProps {
  id: string
  priority: number
}
export interface CardProps {
  id: string
  title: string
  tinyTitle: string
  enabled?: boolean
  priority: number
  component: FunctionComponent
}
class Main {
  @observable public cards: CardProps[] = []
  public constructor() {
    makeObservable(this)
  }
  @action public addCard = (card: CardProps) => {
    const priority = this.getStoragePriority(card.id)
    if (priority) {
      card.priority = priority
    }
    this.cards.push(card)
  }
  @computed public get cardsLength() {
    return this.cards.length
  }
  @computed public get enabledCards(): CardProps[] {
    return this.cards
      .slice()
      .filter(({ enabled = true }) => enabled)
      .sort((a, b) => a.priority - b.priority)
  }
  @computed public get enabledCardsLength(): number {
    return this.enabledCards.length
  }
  @action private setCardsPriority = (cards: CardProps[]) => {
    cards.map(({ id, priority }) => {
      const i = this.cards.findIndex((item) => item.id === id)
      if (i !== -1 && this.cards[i].priority !== priority) {
        this.cards[i].priority = priority
      }
    })
  }
  @action public setCard = ({ id, ...card }: Partial<CardProps>) => {
    const i = this.cards.findIndex((item) => item.id === id)
    if (i === -1) {
      return
    }
    this.cards[i] = { ...this.cards[i], ...card }
  }
  @action public moveCardUp = (
    e: MouseEvent<HTMLAnchorElement>,
    id: string
  ) => {
    e.preventDefault()
    const cards = this.enabledCards
    const i = cards.findIndex((item) => item.id === id)
    if (i <= 0) {
      return
    }
    ;[cards[i].priority, cards[i - 1].priority] = [
      cards[i - 1].priority,
      cards[i].priority,
    ]
    this.setCardsPriority(cards)
    this.setStoragePriorityItems()
  }
  @action public moveCardDown = (
    e: MouseEvent<HTMLAnchorElement>,
    id: string
  ) => {
    e.preventDefault()
    const cards = this.enabledCards
    const i = cards.findIndex((item) => item.id === id)
    if (i === -1 || i === cards.length - 1) {
      return
    }
    ;[cards[i].priority, cards[i + 1].priority] = [
      cards[i + 1].priority,
      cards[i].priority,
    ]
    this.setCardsPriority(cards)
    this.setStoragePriorityItems()
  }
  private getStoragePriorityItems = (): StoragePriorityItemProps[] | null => {
    const items = localStorage.getItem('cardsPriority')
    if (!items) {
      return null
    }
    return (JSON.parse(items) as StoragePriorityItemProps[]) || null
  }
  private setStoragePriorityItems = () => {
    localStorage.setItem(
      'cardsPriority',
      JSON.stringify(
        this.enabledCards.map(({ id, priority }) => ({ id, priority }))
      )
    )
  }
  private getStoragePriority = (id: string): number => {
    const items = this.getStoragePriorityItems()
    if (!items) {
      return 0
    }
    const item = items.find((item) => item.id === id)
    return item ? item.priority : 0
  }
}
export const CardStore = new Main()
