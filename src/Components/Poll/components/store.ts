import { configure, makeAutoObservable } from 'mobx';
import type { PollDataProps } from './typings.ts';

configure({
  enforceActions: 'observed',
});
class Main {
  pollData: PollDataProps | null = null;
  constructor() {
    makeAutoObservable(this);
  }
  setPollData = (data: PollDataProps | null) => {
    this.pollData = data;
  };
}
export const PollStore = new Main();
