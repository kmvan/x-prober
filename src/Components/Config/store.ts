import { configure, makeAutoObservable } from 'mobx';
import { isDeepEqual } from '../Utils/components/is-deep-equal/index.ts';
import type { ConfigProps } from './typings.ts';

configure({
  enforceActions: 'observed',
});
class Main {
  pollData: ConfigProps | null = null;
  constructor() {
    makeAutoObservable(this);
  }
  setPollData = (pollData: ConfigProps | null) => {
    if (isDeepEqual(pollData, this.pollData)) {
      return;
    }
    this.pollData = pollData;
  };
}
export const ConfigStore = new Main();
