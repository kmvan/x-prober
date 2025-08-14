import { configure, makeAutoObservable } from 'mobx';
import { isDeepEqual } from '@/Components/Utils/components/is-deep-equal/index.ts';
import type { DiskUsagePollDataProps } from './typings.ts';configure({
  enforceActions: 'observed',
});
class Main {
  pollData: DiskUsagePollDataProps | null = null;
  constructor() {
    makeAutoObservable(this);
  }
  setPollData = (pollData: DiskUsagePollDataProps | null) => {
    if (isDeepEqual(pollData, this.pollData)) {
      return;
    }
    this.pollData = pollData;
  };
}
export const DiskUsageStore = new Main();
