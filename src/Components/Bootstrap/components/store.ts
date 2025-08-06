import { configure, makeAutoObservable } from 'mobx';
import type { BootstrapPollDataProps } from './typings.ts';

configure({
  enforceActions: 'observed',
});
class Main {
  pollData: BootstrapPollDataProps | null = null;
  constructor() {
    makeAutoObservable(this);
  }
  setPollData = (data: BootstrapPollDataProps | null) => {
    this.pollData = data;
  };
}
export const BootstrapStore = new Main();
