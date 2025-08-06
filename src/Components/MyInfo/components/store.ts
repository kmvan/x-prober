import { configure, makeAutoObservable } from 'mobx';
import { isDeepEqual } from '@/Components/Utils/components/is-deep-equal/index.ts';
import type { MyInfoPollDataProps } from './typings.ts';

configure({
  enforceActions: 'observed',
});
class Main {
  pollData: MyInfoPollDataProps | null = null;
  constructor() {
    makeAutoObservable(this);
  }
  setPollData = (pollData: MyInfoPollDataProps | null) => {
    if (isDeepEqual(pollData, this.pollData)) {
      return;
    }
    this.pollData = pollData;
  };
}
export const MyInfoStore = new Main();
