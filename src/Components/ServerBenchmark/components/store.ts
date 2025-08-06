import { configure, makeAutoObservable } from 'mobx';
import type { ServerBenchmarkProps } from './typings.ts';

configure({
  enforceActions: 'observed',
});
class Main {
  benchmarking = false;
  maxMarks = 0;
  servers: ServerBenchmarkProps[] = [];
  constructor() {
    makeAutoObservable(this);
  }
  setMaxMarks = (maxMarks: number) => {
    this.maxMarks = maxMarks;
  };
  setServers = (servers: ServerBenchmarkProps[]) => {
    this.servers = servers;
  };
  setServer = (
    id: ServerBenchmarkProps['id'],
    server: ServerBenchmarkProps
  ) => {
    const i = this.servers.findIndex((server) => server.id === id);
    if (i === -1) {
      return;
    }
    this.servers[i] = server;
  };
  setBenchmarking = (benchmarking: boolean) => {
    this.benchmarking = benchmarking;
  };
}
export const ServerBenchmarkStore = new Main();
