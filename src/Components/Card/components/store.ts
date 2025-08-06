import { configure, makeAutoObservable } from 'mobx';
import { DatabaseLoader } from '@/Components/Database/components/loader';
import { DiskUsageLoader } from '@/Components/DiskUsage/components/loader.ts';
import { MyInfoLoader } from '@/Components/MyInfo/components/loader.ts';
import { NetworkStatsLoader } from '@/Components/NetworkStats/components/loader.ts';
import { NodesLoader } from '@/Components/Nodes/components/loader.ts';
import { PhpExtensionsLoader } from '@/Components/PhpExtensions/components/loader.ts';
import { PhpInfoLoader } from '@/Components/PhpInfo/components/loader.ts';
import { PingLoader } from '@/Components/Ping/components/loader.ts';
import { PollStore } from '@/Components/Poll/components/store.ts';
import type { PollDataProps } from '@/Components/Poll/components/typings.ts';
import { ServerBenchmarkLoader } from '@/Components/ServerBenchmark/components/loader.ts';
import { ServerInfoLoader } from '@/Components/ServerInfo/components/loader.ts';
import { ServerStatusLoader } from '@/Components/ServerStatus/components/loader.ts';
import { TemperatureSensorLoader } from '@/Components/TemperatureSensor/components/loader.ts';
import type { CardProps } from './typings.ts';

configure({
  enforceActions: 'observed',
});
export interface StoragePriorityItemProps {
  id: string;
  priority: number;
}
class Main {
  cards: CardProps[] = [];
  constructor() {
    makeAutoObservable(this);
    [
      NodesLoader(),
      TemperatureSensorLoader(),
      ServerStatusLoader(),
      NetworkStatsLoader(),
      DiskUsageLoader(),
      PingLoader(),
      ServerInfoLoader(),
      PhpInfoLoader(),
      PhpExtensionsLoader(),
      DatabaseLoader(),
      MyInfoLoader(),
      ServerBenchmarkLoader(),
    ].map((item) => {
      return this.addCard(item);
    });
  }
  addCard = (card: CardProps) => {
    const priority = this.getStoragePriority(card.id);
    if (priority) {
      card.priority = priority;
    }
    this.cards.push(card);
  };
  get cardsLength() {
    return this.cards.length;
  }
  get enabledCards(): CardProps[] {
    const { pollData } = PollStore;

    return this.cards
      .filter(({ id }) => Boolean(pollData?.[id as keyof PollDataProps]))
      .toSorted((a, b) => {
        return a.priority - b.priority;
      });
  }
  get enabledCardsLength(): number {
    return this.enabledCards.length;
  }
  private setCardsPriority = (cards: CardProps[]) => {
    for (const { id, priority } of cards) {
      const i = this.cards.findIndex((item) => item.id === id);
      if (i !== -1 && this.cards[i].priority !== priority) {
        this.cards[i].priority = priority;
      }
    }
  };
  setCard = ({ id, ...card }: Partial<CardProps>) => {
    const i = this.cards.findIndex((item) => item.id === id);
    if (i === -1) {
      return;
    }
    this.cards[i] = { ...this.cards[i], ...card };
  };
  moveCardUp = (id: string) => {
    const cards = this.enabledCards;
    const i = cards.findIndex((item) => item.id === id);
    if (i <= 0) {
      return;
    }
    [cards[i].priority, cards[i - 1].priority] = [
      cards[i - 1].priority,
      cards[i].priority,
    ];
    this.setCardsPriority(cards);
    this.setStoragePriorityItems();
  };
  moveCardDown = (id: string) => {
    const cards = this.enabledCards;
    const i = cards.findIndex((item) => item.id === id);
    if (i === -1 || i === cards.length - 1) {
      return;
    }
    [cards[i].priority, cards[i + 1].priority] = [
      cards[i + 1].priority,
      cards[i].priority,
    ];
    this.setCardsPriority(cards);
    this.setStoragePriorityItems();
  };
  private getStoragePriorityItems = (): StoragePriorityItemProps[] | null => {
    const items = localStorage.getItem('cardsPriority');
    if (!items) {
      return null;
    }
    return (JSON.parse(items) as StoragePriorityItemProps[]) || null;
  };
  private setStoragePriorityItems = (): void => {
    localStorage.setItem(
      'cardsPriority',
      JSON.stringify(
        this.enabledCards.map(({ id, priority }) => ({ id, priority }))
      )
    );
  };
  getStoragePriority = (id: string): number => {
    const items = this.getStoragePriorityItems();
    if (!items) {
      return 0;
    }
    const item = items.find((n) => n.id === id);
    return item ? item.priority : 0;
  };
  get disabledMoveUpId(): string {
    const items = this.enabledCards;
    if (items.length <= 1) {
      return '';
    }
    return items[0].id;
  }
  get disabledMoveDownId(): string {
    const items = this.enabledCards;
    if (items.length <= 1) {
      return '';
    }
    return items.at(-1)?.id ?? '';
  }
}
export const CardStore = new Main();
