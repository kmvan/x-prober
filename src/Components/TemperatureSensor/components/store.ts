import { configure, makeAutoObservable } from 'mobx';
import { CardStore } from '@/Components/Card/components/store.ts';
import { serverFetch } from '@/Components/Fetch/server-fetch.ts';
import { OK } from '@/Components/Rest/http-status.ts';
import { TemperatureSensorConstants } from './constants.ts';
import type { TemperatureSensorItemProps } from './typings.ts';

configure({
  enforceActions: 'observed',
});
const { id } = TemperatureSensorConstants;
class Main {
  items: TemperatureSensorItemProps[] = [];
  constructor() {
    makeAutoObservable(this);
  }
  setItems = (items: TemperatureSensorItemProps[]) => {
    this.items = items;
  };
  private setEnabledCard = (): void => {
    const { setCard, cards } = CardStore;
    const item = cards.find((n) => n.id === id);
    if (!item) {
      return;
    }
    if (item.enabled) {
      return;
    }
    setCard({
      id,
      enabled: true,
    });
  };
  fetch = async () => {
    const { data: items, status } =
      await serverFetch<TemperatureSensorItemProps[]>('temperature-sensor');
    if (items?.length && status === OK) {
      this.setItems(items);
      this.setEnabledCard();
      setTimeout(() => {
        this.fetch();
      }, 1000);
    }
  };
  get itemsCount() {
    return this.items.length;
  }
}
export const TemperatureSensorStore = new Main();
