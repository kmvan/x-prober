import { configure, makeAutoObservable } from 'mobx';
import { isDeepEqual } from '@/Components/Utils/components/is-deep-equal/index.ts';
import type { DatabasePollDataProps } from './typings.ts';

configure({
  enforceActions: 'observed',
});
class Main {
  pollData: DatabasePollDataProps | null = null;
  constructor() {
    makeAutoObservable(this);
  }
  setPollData = (pollData: DatabasePollDataProps | null) => {
    if (isDeepEqual(pollData, this.pollData)) {
      return;
    }
    this.pollData = pollData;
  };
}
export const DatabaseStore = new Main();
