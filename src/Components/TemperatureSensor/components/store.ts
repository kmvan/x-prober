import { configure, makeAutoObservable } from 'mobx';
import { isDeepEqual } from '@/Components/Utils/components/is-deep-equal/index.ts';
import type { TemperatureSensorPollDataProps } from './typings.ts';configure({
  enforceActions: 'observed',
});
class Main {
  pollData: TemperatureSensorPollDataProps | null = null;
  latestPhpVersion = '';
  constructor() {
    makeAutoObservable(this);
  }
  setPollData = (pollData: TemperatureSensorPollDataProps | null) => {
    if (isDeepEqual(pollData, this.pollData)) {
      return;
    }
    this.pollData = pollData;
  };
  setLatestPhpVersion = (latestPhpVersion: string) => {
    this.latestPhpVersion = latestPhpVersion;
  };
}
export const TemperatureSensorStore = new Main();
