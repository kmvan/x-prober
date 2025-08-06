import { configure, makeAutoObservable } from 'mobx';
import { isDeepEqual } from '@/Components/Utils/components/is-deep-equal/index.ts';
import type { NodesItemProps, NodesPollDataProps } from './typings.ts';

configure({
  enforceActions: 'observed',
});
class Main {
  readonly DEFAULT_ITEM = {
    id: '',
    url: '',
    fetchUrl: '',
    loading: true,
    status: 204,
    data: null,
  };
  items: NodesItemProps[] = [];
  pollData: NodesPollDataProps | null = null;
  constructor() {
    makeAutoObservable(this);
  }
  setPollData = (pollData: NodesPollDataProps | null) => {
    if (isDeepEqual(pollData, this.pollData)) {
      return;
    }
    this.pollData = pollData;
  };
  setItems = (items: NodesItemProps[]) => {
    this.items = items;
  };
  setItem = ({ id, ...props }: Partial<NodesItemProps>) => {
    const i = this.items.findIndex((item) => item.id === id);
    if (i === -1) {
      return;
    }
    this.items[i] = { ...this.items[i], ...props };
  };
}
export const NodesStore = new Main();
