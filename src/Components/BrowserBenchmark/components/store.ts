import { configure, makeAutoObservable } from 'mobx';
import type { BrowserBenchmarkProps } from './typings.ts';

configure({
  enforceActions: 'observed',
});
class Main {
  benchmarking = false;
  maxMarks = 0;
  browsers: BrowserBenchmarkProps[] = [];
  constructor() {
    makeAutoObservable(this);
  }
  setMaxMarks = (maxMarks: number) => {
    this.maxMarks = maxMarks;
  };
  setBrowsers = (browsers: BrowserBenchmarkProps[]) => {
    this.browsers = browsers;
  };
  setBrowser = (
    id: BrowserBenchmarkProps['id'],
    item: BrowserBenchmarkProps
  ) => {
    const i = this.browsers.findIndex((n) => n.id === id);
    if (i === -1) {
      return;
    }
    this.browsers[i] = item;
  };
  setBenchmarking = (benchmarking: boolean) => {
    this.benchmarking = benchmarking;
  };
}
export const BrowserBenchmarkStore = new Main();
