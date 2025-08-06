import { configure, makeAutoObservable } from 'mobx';
import { isDeepEqual } from '@/Components/Utils/components/is-deep-equal/index.ts';
import type { NetworkStatsPollDataProps } from './typings.ts';

configure({
  enforceActions: 'observed',
});

class Main {
  pollData: NetworkStatsPollDataProps | null = null;
  constructor() {
    makeAutoObservable(this);
  }
  setPollData(pollData: NetworkStatsPollDataProps | null) {
    if (isDeepEqual(pollData, this.pollData)) {
      return;
    }
    this.pollData = pollData;
  }
  get networks(): NetworkStatsPollDataProps['networks'] {
    return this.pollData?.networks ?? [];
  }
  get timestamp(): NetworkStatsPollDataProps['timestamp'] {
    return this.pollData?.timestamp ?? 0;
  }
  get sortNetworks() {
    return this.networks
      .filter(({ tx }) => Boolean(tx))
      .toSorted((a, b) => a.tx - b.tx);
  }
  get networksCount() {
    return this.sortNetworks.length;
  }
}
export const NetworkStatsStore = new Main();
