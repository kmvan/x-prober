import { configure, makeAutoObservable } from 'mobx';
import { isDeepEqual } from '@/Components/Utils/components/is-deep-equal/index.ts';
import type { PhpInfoPollDataProps } from './typings.ts';configure({
  enforceActions: 'observed',
});
class Main {
  pollData: PhpInfoPollDataProps | null = null;
  latestPhpVersion = '';
  constructor() {
    makeAutoObservable(this);
  }
  setPollData = (pollData: PhpInfoPollDataProps | null) => {
    if (isDeepEqual(pollData, this.pollData)) {
      return;
    }
    this.pollData = pollData;
  };
  setLatestPhpVersion = (latestPhpVersion: string) => {
    this.latestPhpVersion = latestPhpVersion;
  };
}
export const PhpInfoStore = new Main();
